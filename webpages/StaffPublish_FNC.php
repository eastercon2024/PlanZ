<?php

require_once('db_functions.php');
require_once('error_functions.php');

function get_diff($new_program, $new_people) {
    $query = "SELECT program_json, people_json FROM PublishHistory JOIN PublishedSchedule USING (pub_sched_id) ORDER BY published_time DESC LIMIT 1";
    $result = mysqli_query_with_error_handling($query);
    if (mysqli_num_rows($result) == 0) {
        $cur_program = [];
        $cur_people = [];
    } else {
        $row = mysqli_fetch_assoc($result);
        $cur_program = json_decode($row["program_json"]);
        $cur_people = json_decode($row["people_json"]);
    }

    if ($cur_program == $new_program && $cur_people == $new_people) {
        return [];
    }

    $changes = [];

    if ($cur_people != $new_people) {
        $changes[] = ["type" => "change_people"];
    }

    foreach ($new_program as $new_item) {
        $old_item = null;
        foreach ($cur_program as $cur_item) {
            if ($cur_item->id== $new_item->id) {
                $old_item = $cur_item;
                break;
            }
        }
        if ($old_item == null) {
            $changes[] = ["type" => "new", "new" => $new_item];
        } else {
            if ($old_item != $new_item) {
                $changes[] = ["type" => "change", "old" => $old_item, "new" => $new_item];
            }
        }
    }

    foreach ($cur_program as $old_item) {
        $found_item = false;
        foreach ($new_program as $new_item) {
            if ($old_item->id == $new_item->id) {
                $found_item = true;
                break;
            }
        }
        if (!$found_item) {
            $changes[] = ["type" => "delete", "old" => $old_item];
        }
    }

    usort($changes, function($a, $b) {
        if (array_key_exists("old", $a)) {
            $a_item = $a["old"];
        } else {
            $a_item = $a["new"];
        }

        if (array_key_exists("old", $b)) {
            $b_item = $b["old"];
        } else {
            $b_item = $b["new"];
        }
        
        if ($a_item->date == $b_item->date) {
            return $a_item->time <=> $b_item->time;
        }
        return $a_item->date <=> $b_item->date;
    });

    return $changes;
}

?>