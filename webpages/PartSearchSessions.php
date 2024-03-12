<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo;
global $partAvail, $availability, $title;
$title="Search Sessions";
require ('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
participant_header($title, false, 'Normal', true);
if (!may_I('search_panels')) {

    $message_error="You do not currently have permission to view this page.<br>\n";
    RenderError($message_error);
    exit();
    }

$trackid = getInt('track');
$tagArr = getArrayOfInts('tags');
error_log("tags: " . print_r($tagArr, true));
$titlesearch = getString('title');
$tagmatch = getString('tagmatch');
if (TRACK_TAG_USAGE !== "TAG_ONLY")
    $addtrack = "T.trackname,";
else
    $addtrack = "";

$queryArray = array();

// List of sessions that match search criteria
// Includes sessions in which participant is already interested if they do match match search
// Use "Session Interests" page to just see everything in which you are interested
$sql = <<<EOD
SELECT
        S.sessionid, $addtrack S.title, GROUP_CONCAT(TA.tagname ORDER BY TA.display_order SEPARATOR ', ') AS taglist,
        CASE
            WHEN (minute(S.duration)=0) THEN date_format(S.duration,'%l hr')
            WHEN (hour(S.duration)=0) THEN date_format(S.duration, '%i min')
            ELSE date_format(S.duration,'%l hr, %i min')
            END
            AS duration,
        TY.typename, S.progguiddesc, S.persppartinfo, PSI.badgeid
    FROM
                  Sessions S
EOD;
if (TRACK_TAG_USAGE !== "TAG_ONLY")
    $sql .= "\n JOIN Tracks T USING (trackid)\n";
$sql .= <<<EOD
             JOIN Types TY USING (typeid)
             JOIN SessionStatuses SST USING (statusid)
        LEFT JOIN
                  (SELECT
                          badgeid, sessionid
                      FROM
                          ParticipantSessionInterest
                      WHERE badgeid='$badgeid'
                  ) as PSI USING (sessionid)
        LEFT JOIN SessionHasTag SHT USING (sessionid)
EOD;
if (TRACK_TAG_USAGE !== "TRACK_ONLY")
    $sql .= "\n LEFT JOIN Tags TA USING (tagid)\n";
$sql .= <<<EOD
    WHERE
            SST.may_be_scheduled=1
        AND S.Sessionid in
            (SELECT
                    S2.Sessionid
                FROM
                         Sessions S2
EOD;
if (TRACK_TAG_USAGE !== "TAG_ONLY")
    $sql .= "\n JOIN Tracks T USING (trackid)\n";
$sql .= <<<EOD
                    JOIN Types Y USING (typeid)
                WHERE
                         S2.invitedguest=0
                     AND Y.selfselect=1
EOD;
if (TRACK_TAG_USAGE !== "TAG_ONLY")
    $sql .= "\n     AND T.selfselect=1\n";

    $queryArray["sessions"] = $sql;

$queryArray['interested'] = <<<EOD
SELECT
        P.interested
    FROM
        Participants P
    WHERE
        P.badgeid = '$badgeid';
EOD;
if (TRACK_TAG_USAGE !== "TAG_ONLY") {
    $queryArray['tracks'] = <<<EOD
SELECT
        trackid, trackname
    FROM
        Tracks
    WHERE
        selfselect=1
    ORDER BY
        display_order;
EOD;
}
if (TRACK_TAG_USAGE !== "TRACK_ONLY") {
    $tagIdList = implode(',', $tagArr);
    if (empty($tagIdList)) {
        # Need to have something to put in the list, so put in an id that can't exist.
        $tagIdList = '-1';
    }
    $queryArray['tags'] = <<<EOD
SELECT
        tagid, tagname, tagid IN ($tagIdList) AS selected
    FROM
        Tags
    ORDER BY
        display_order;
EOD;
}

if ($trackid !== false && $trackid != 0 && TRACK_TAG_USAGE !== "TRACK_ONLY") {
    $queryArray["sessions"] .= "                     AND S2.trackid=$trackid\n";
}
if (!empty($titlesearch)) {
    $x = mysqli_real_escape_string($linki, $titlesearch);
    $queryArray["sessions"] .= "                     AND S2.title LIKE \"%$x%\"\n";
}
if ($tagArr !== false && count($tagArr) > 0 && TRACK_TAG_USAGE !== "TRACK_ONLY") {
    if ($tagmatch =='all') {
        foreach ($tagArr as $tag) {
            $queryArray["sessions"] .= " AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S2.sessionid AND tagid = $tag)";
        }
    } else {
        $tagidList = implode(',', $tagArr);
        $queryArray["sessions"] .= " AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S2.sessionid AND tagid IN ($tagidList))";
    }
}
$queryArray["sessions"] .= ") GROUP BY S.sessionid ORDER BY $addtrack S.sessionid;";
$queryArray["interested"] = <<<EOD
SELECT
        P.interested
    FROM
        Participants P
    WHERE
        P.badgeid = '$badgeid';
EOD;

if (($resultXML = mysql_query_XML($queryArray)) === false) {
    $message="Error querying database. Unable to continue.<br>";
    echo "<p class\"alert alert-error\">$message</p>\n";
    participant_footer();
    exit();
}

//Run the sessions query to get session ids for the multi collapse class
if (!$result = mysqli_query_exit_on_error($queryArray["sessions"])) {
    exit(); // Should have exited already
}

$collapse_list = '';
while ($row = mysqli_fetch_assoc($result)) {
    $collapse_list .= 'collapse-$row["sessionid"] ';
}

$paramArray = array();
$paramArray["conName"] = CON_NAME;
$paramArray["trackIsPrimary"] = TRACK_TAG_USAGE === "TRACK_ONLY" || TRACK_TAG_USAGE === "TRACK_OVER_TAG";
$paramArray["showTags"] = TRACK_TAG_USAGE !== "TRACK_ONLY";
$paramArray["showTrack"] = TRACK_TAG_USAGE !== "TAG_ONLY";
$paramArray["collapse_list"] = $collapse_list;
$paramArray["PARTICIPANT_PHOTOS"] = PARTICIPANT_PHOTOS === TRUE ? 1 : 0;
$paramArray['may_I'] = "0";
$paramArray["title"] = $titlesearch;
$paramArray["tagMatch"] = $tagmatch === null ? "any" : $tagmatch;
$paramArray["showingAll"] = (empty($titlesearch) && empty($tagArr)) ? "1" : "0";

echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
RenderXSLT('PartSearchSessions.xsl', $paramArray, $resultXML);

participant_footer();
?>
