<?php
// Copyright (c) 2022 BC Holmes. All rights reserved. See copyright document for more details.
// This functionality has been inspired by code created by Piglet for the original WisConDB
// codebase (https://bitbucket.org/wiscon/wiscon/src/master/), and by Alien Planit 
// (https://github.com/annalee/alienplanit) by Annalee (https://github.com/annalee)

global $title;
$title = "Auto-Scheduler";

require_once('StaffCommonCode.php'); // Checks for staff permission among other things

function to_mins($time) {
    if (!$time) {
        return NULL;
    }
    $parts = explode(":", $time);
    return $parts[0] * 60 + $parts[1];
}

function mins_to_hh_mm($mins) {
    $hours = floor($mins / 60);
    $minutes = $mins % 60;
    return sprintf("%02d:%02d:00", $hours, $minutes);
}

function format_mins($mins) {
    $days = $mins / 1440;
    $hours = ($mins % 1440) / 60;
    $minutes = $mins % 60;
    if ($days < 1) {
        $day = "Friday";
    } else if ($days < 2) {
        $day = "Saturday";
    } else if ($days < 3) {
        $day = "Sunday";
    } else {
        $day = "Monday";
    }
    return sprintf("%s %02d:%02d", $day, $hours, $minutes);
}

function get_all_sessions() {
    $slots = [
        to_mins('13:00:00'),
        to_mins('14:30:00'),
        to_mins('16:00:00'),
        to_mins('17:30:00'),
        to_mins('19:00:00'),
        to_mins('20:00:00'),
        to_mins('21:00:00'),
        to_mins('10:00:00') + 1440,
        to_mins('11:00:00') + 1440,
        to_mins('12:00:00') + 1440,
        to_mins('13:00:00') + 1440,
        to_mins('14:30:00') + 1440,
        to_mins('16:00:00') + 1440,
        to_mins('17:30:00') + 1440,
        to_mins('19:00:00') + 1440,
        to_mins('20:00:00') + 1440,
        to_mins('21:00:00') + 1440,
        to_mins('10:00:00') + (1440 * 2),
        to_mins('11:00:00') + (1440 * 2),
        to_mins('12:00:00') + (1440 * 2),
        to_mins('13:00:00') + (1440 * 2),
        to_mins('14:30:00') + (1440 * 2),
        to_mins('16:00:00') + (1440 * 2),
        to_mins('17:30:00') + (1440 * 2),
        to_mins('19:00:00') + (1440 * 2),
        to_mins('20:00:00') + (1440 * 2),
        to_mins('21:00:00') + (1440 * 2),
        to_mins('10:00:00') + (1440 * 3),
        to_mins('11:00:00') + (1440 * 3),
        to_mins('12:00:00') + (1440 * 3),
        to_mins('13:00:00') + (1440 * 3),
        to_mins('14:30:00') + (1440 * 3),
        to_mins('16:00:00') + (1440 * 3),
        1440 * 4
    ];

    $query = <<<EOD
    SELECT sessionid, title FROM Sessions JOIN SessionStatuses USING (statusid) WHERE statusname = "Assigned";
EOD;
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit;
    }

    $sessions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sessions[] = $row;
    }

    foreach ($sessions as &$session) {
        $query = <<<EOD
        SELECT
            badgeid, pubsname, ParticipantAvailabilityTimes.location, moderator, starttime, endtime
        FROM
            ParticipantOnSession
            LEFT OUTER JOIN ParticipantAvailabilityTimes USING (badgeid)
            JOIN Participants USING (badgeid)
        WHERE
            sessionid = ?
        ORDER BY moderator DESC, badgeid, starttime, endtime;
EOD;
        $result = mysqli_query_with_prepare_and_exit_on_error($query, 'i', [$session['sessionid']]);    

        $session['participants'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!array_key_exists($row['badgeid'], $session['participants'])) {
                $session['participants'][$row['badgeid']] = [
                    'name' => $row['pubsname'],
                    'moderator' => $row['moderator'] == 1,
                    'availability' => []
                ];
            }
            if ($row['location']) {
                $session['participants'][$row['badgeid']]['availability'][] = [
                    'location' => $row['location'],
                    'start' => to_mins($row['starttime']),
                    'end' => to_mins($row['endtime'])
                ];
            }
        }

        // Find the intersection of all participants' availability
        $availability = [[
            'start' => 0,
            'end' => 1440 * 4
        ]];
        foreach ($session['participants'] as $participant) {
            if (!empty($participant['availability'])) {
                $new_availability = [];
                foreach ($availability as $slot) {
                    foreach ($participant['availability'] as $participant_slot) {
                        if ($slot['start'] < $participant_slot['end'] && $slot['end'] > $participant_slot['start']) {
                            $new_availability[] = [
                                'start' => max($slot['start'], $participant_slot['start']),
                                'end' => min($slot['end'], $participant_slot['end'])
                            ];
                        }
                    }
                }
                $availability = $new_availability;
            }
        }

        $session['rank'] = 0;
        $session['availability'] = [];
        foreach ($availability as $avail_slot) {
            for ($i = 0; $i < count($slots) - 1; $i++) {
                if ($slots[$i] >= $avail_slot['start'] && $slots[$i + 1] <= $avail_slot['end']) {
                    $availability_details = [
                        'start' => $slots[$i],
                        'end' => $slots[$i + 1],
                        'rooms' => []
                    ];

                    // Count how many rooms are free in that slot
                    $query = <<<EOD
                    SELECT
                        roomname
                    FROM
                        Rooms
                    WHERE
                        roomid NOT IN (SELECT roomid FROM Schedule WHERE starttime = ? AND roomid IN (4, 9, 36, 38, 39))
                        AND roomid IN (4, 9, 36, 38, 39);
EOD;
                    $result = mysqli_query_with_prepare_and_exit_on_error($query, 's', [mins_to_hh_mm($slots[$i])]);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $availability_details['rooms'][] = $row['roomname'];
                        $session['rank']++;
                    }
                    $session['availability'][] = $availability_details;
                }
            }
        }
    }

    // Sort sessions by length of availability
    usort($sessions, function($a, $b) {
        return $a['rank'] - $b['rank'];
    });
    return $sessions;
}

$sessions = get_all_sessions();

staff_header($title, true);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Auto-Scheduler</h4>
        </div>
        <div class="card-body">
            <?php
            foreach ($sessions as $session) {
            ?>
                <div class="row">
                    <div class="col">
                        <h5><a href="https://planz.eastercon2024.co.uk/EditSession.php?id=<?php echo $session['sessionid']; ?>"><?php echo $session['sessionid']; ?>. <?php echo $session['title']; ?></a></h5>

                        <p><strong>Possible slots:</strong></p>
                        <ul>
                            <?php
                            if (empty($session['availability'])) {
                            ?>
                                <li><em>No slots satisfy everyone</em></li>
                            <?php
                            } else {
                                foreach ($session['availability'] as $availability) {
                            ?>
                                <li><?php echo format_mins($availability['start']); ?> (<?php
                                    if (empty($availability['rooms'])) {
                                        echo "<em>No rooms available</em>";
                                    } else {
                                        echo implode(', ', $availability['rooms']);
                                    }
                                ?>)</li>
                            <?php
                                }
                            }
                            ?>
                        </ul>

                        <p><strong>Participants:</strong></p>
                        <?php
                        foreach ($session['participants'] as $participant) {
                        ?>
                            <p><?php echo $participant['name']; ?> (<?php echo $participant['moderator'] ? "moderator" : "panelist"; ?>)</p>
                            <?php
                            if (empty($participant['availability'])) {
                            ?>
                                <p><ul><li><em>No availability specified</em></li></ul></p>
                            <?php
                            } else {
                            ?>
                            <ul>
                                <?php
                                foreach ($participant['availability'] as $availability) {
                                ?>
                                    <li><?php echo format_mins($availability['start']); ?> - <?php echo format_mins($availability['end']); ?></li>
                                <?php
                                }
                                ?>
                            </ul>
                            <?php
                            }
                            ?>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<?php
    staff_footer();
?>