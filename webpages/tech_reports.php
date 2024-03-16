<?php

require_once('CommonCode.php');
require_once('db_functions.php');

$badgeid = isset($_SESSION['badgeid']) ? $_SESSION['badgeid'] : null;
if (!(may_I("Staff"))) {
    global $headerErrorMessage, $returnAjaxErrors;
    $headerErrorMessage = "You are not authorized to access this page or your login session has expired.";
    if (isset($returnAjaxErrors) && $returnAjaxErrors) {
        RenderErrorAjax($headerErrorMessage);
    } else {
        require ('login.php');
    }
    exit();
};

if (prepare_db_and_more() === false) {
    exit("Unable to connect to database");
};

$ConStartDatim = CON_START_DATIM;

$query = <<<EOD
SELECT
    S.sessionid AS id,
    S.title,
    R.roomname AS loc,
    DATE_FORMAT(duration, '%k') * 60 + DATE_FORMAT(duration, '%i') AS mins,
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%Y-%m-%d') as date,
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%H:%i') as time,
    TL.techlevel,
    S.servicenotes,
    S.presentationname,
    NVL(POS_ALL.count, 0) as total_parts,
    NVL(POS_VIRT.count, 0) as num_virtual,
    NVL(IPO.bool, 1) as stream,
    NVL(VOD.bool, 1) as vod
FROM
              Schedule SCH
         JOIN Sessions S USING (sessionid)
         JOIN Tracks TR USING (trackid)
         JOIN Types TY USING (typeid)
         JOIN Rooms R USING (roomid)
         JOIN TechLevel TL USING (techlevelid)
         LEFT OUTER JOIN (
             SELECT
             	sessionid,
                0 AS bool
             FROM
             	SessionHasTag
                JOIN Tags USING (tagid)
             WHERE
             	Tags.tagname = 'In person only'
         ) IPO ON IPO.sessionid = S.sessionid
         LEFT OUTER JOIN (
             SELECT
             	sessionid,
                0 AS bool
             FROM
             	SessionHasTag
                JOIN Tags USING (tagid)
             WHERE
             	Tags.tagname = 'No Catch-Up Available'
         ) VOD ON VOD.sessionid = S.sessionid
         LEFT OUTER JOIN (
             SELECT
             	sessionid,
             	COUNT(*) as count
             FROM
             	ParticipantOnSession POS
             WHERE
             	POS.location = 'virtual'
             GROUP BY
             	sessionid
         ) POS_VIRT ON POS_VIRT.sessionid = S.sessionid
		LEFT OUTER JOIN (
            SELECT
            	sessionid,
            	COUNT(*) as count
           	FROM
            	ParticipantOnSession POS
           	GROUP BY
            	sessionid
            ) POS_ALL ON POS_ALL.sessionid = S.sessionid
WHERE
    S.pubstatusid IN (2) /* Public */
    AND R.roomid IN (4, 9, 36, 38, 39)
ORDER BY R.roomname, SCH.starttime;
EOD;
$result = mysqli_query_with_error_handling($query);

$tech_reports = array();
while($row = mysqli_fetch_assoc($result)) {
    $item = [
        "id" => $row["id"],
        "title" => $row["title"],
        "mins" => $row["mins"],
        "time" => $row["time"],
        "techlevel" => $row["techlevel"],
        "notes" => $row["servicenotes"],
        "presentation" => $row["presentationname"],
        "total_participants" => $row["total_parts"],
        "num_virtual" => $row["num_virtual"],
        "can_stream" => $row["stream"] == '1' ? 'Yes' : 'No',
        "can_vod" => $row["vod"] == '1' ? 'Yes' : 'No'
    ];
    $tech_reports[$row["loc"]][$row["date"]][] = $item;
}

$zip = new ZipArchive();
$filename = tempnam(sys_get_temp_dir(), "tech_reports_") . ".zip";
error_log("Creating zip file: " . $filename);

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

foreach ($tech_reports as $room => $dates) {
    foreach ($dates as $date => $items) {
        $day = date('l', strtotime($date));
        $csv_contents = "Title,Duration,Time,Tech Level,# Participants,# Virtual,Can Stream,Can VOD,Presentation,Notes\n";
        foreach ($items as $item) {
            $csv_contents .= '"' . str_replace('"', '""', $item["title"]) . '",' . $item["mins"] . ',"' . $item["time"] . '",' . $item["techlevel"] . ',"' . $item["total_participants"] . '","' . $item["num_virtual"] . '","' . $item["can_stream"] . '","' . $item["can_vod"] . '","' . str_replace('"', '""', $item["presentation"]) . '","' . str_replace('"', '""', $item["notes"]) . "\"\n";
        }
        $zip->addFromString("$room/$day.csv", $csv_contents);
    }
}

$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="tech_reports-' . date('Y-m-d--h-i-s') . '.zip"');
header('Content-Length: ' . filesize($filename));
readfile($filename);
unlink($filename);
?>