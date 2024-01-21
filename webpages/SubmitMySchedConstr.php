<?php
require('PartCommonCode.php'); // initialize db; check login;
require('my_sched_constr_func.php');

function updateGeneralAvailability() {
    global $badgeid;

    $maxprog = getInt("maxprog", "NULL");
    $preventconflict = getString("preventconflict");
    $otherconstraints = getString("otherconstraints");
    $query = "REPLACE ParticipantAvailability SET badgeid = ?, maxprog = ?, preventconflict = ?, otherconstraints = ?";
    $rows = mysql_cmd_with_prepare($query, "siss", array($badgeid, $maxprog, $preventconflict, $otherconstraints));
    if (is_null($rows)) {
        Render500ErrorAjax("Error updating database.  Database not updated.");
        exit();
    }

    $query = "REPLACE ParticipantAvailabilityDays (badgeid,day,maxprog) values ";
    $types = "";
    $queryParams = array();
    for ($i = 1; $i <= CON_NUM_DAYS; $i++) {
        $query .= "(?,?,?),";
        $types .= "sii";
        $queryParams[] = $badgeid;
        $queryParams[] = $i;
        $queryParams[] = getInt("maxprogday$i", 0);
    }
    $query = substr($query, 0, -1); // remove extra trailing comma
    $rows = mysql_cmd_with_prepare($query, $types, $queryParams);
    if (is_null($rows)) {
        Render500ErrorAjax("Error updating database.  Database not updated.");
        exit();
    }
}

function setAvailability() {
    global $badgeid;

    $query = "SELECT timeid, timevalue FROM Times";
    $result = mysqli_query_with_error_handling($query, true);
    $times = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $times[$row["timeid"]] = $row["timevalue"];
    }
    
    $availability = array();
    $i = 1;
    while (true) {
        $startDay = getString("startday$i");
        if (is_null($startDay)) {
            break;
        }
        $startTimeIdx = getString("starttime$i");
        $endDay = getString("endday$i");
        $endTimeIdx = getString("endtime$i");

        $startTime = $times[$startTimeIdx];
        $findit = strpos($startTime, ':');
        $hour = substr($startTime, 0, $findit);
        $restOfTime = substr($startTime, $findit);
        $startTime = (intVal($hour) + (24 * ($startDay - 1))) . $restOfTime;

        $endTime = $times[$endTimeIdx];
        $findit = strpos($endTime, ':');
        $hour = substr($endTime, 0, $findit);
        $restOfTime = substr($endTime, $findit);
        $endTime = (intVal($hour) + (24 * ($endDay - 1))) . $restOfTime;

        $availability[$i] = array(
            "availabilitynum" => $i,
            "starttime" => $startTime,
            "endtime" => $endTime,
        );
        $i++;
    }

    if (count($availability) > 0) {
        $query = "REPLACE INTO ParticipantAvailabilityTimes(badgeid, availabilitynum, starttime, endtime) VALUES ";
        $types = "";
        $queryParams = array();
        for ($i = 1; $i <= count($availability); $i++) {
            $query .= "(?,?,?,?),";
            $types .= "siss";
            $queryParams[] = $badgeid;
            $queryParams[] = $availability[$i]["availabilitynum"];
            $queryParams[] = $availability[$i]["starttime"];
            $queryParams[] = $availability[$i]["endtime"];
        }
        $query = substr($query, 0, -1); // remove extra trailing comma
        $rows = mysql_cmd_with_prepare($query, $types, $queryParams);
        if (is_null($rows)) {
            Render500ErrorAjax("Error updating database.  Database not updated.");
            exit();
        }

        $query = "DELETE FROM ParticipantAvailabilityTimes WHERE badgeid = ? AND availabilitynum NOT IN (";
        $types = "s";
        $queryParams = array($badgeid);
        for ($i = 1; $i <= count($availability); $i++) {
            $query .= "?,";
            $types .= "i";
            $queryParams[] = $availability[$i]["availabilitynum"];
        }
        $query = substr($query, 0, -1); // remove extra trailing comma
        $query .= ")";
        $rows = mysql_cmd_with_prepare($query, $types, $queryParams);
        if (is_null($rows)) {
            Render500ErrorAjax("Error updating database.  Database not updated.");
            exit();
        }
    } else {
        $query = "DELETE FROM ParticipantAvailabilityTimes WHERE badgeid = ?";
        $rows = mysql_cmd_with_prepare($query, "s", array($badgeid));
        if (is_null($rows)) {
            Render500ErrorAjax("Error updating database.  Database not updated.");
            exit();
        }
    }

    if (!$partAvail = retrieve_participantAvailability_from_db($badgeid, true)) {
        Render500ErrorAjax("Error retrieving new schedule.");
        exit();
    }
    // This is copied from my_sched_constr.php
    // It should really be pulled out somewhere, but when I try it stops returning any data on the initial render
    // and I'm too tired to figure it out right now.
    $timesXML = retrieve_timesXML();
    $i = 1;
    while (isset($partAvail["starttimestamp_$i"])) {
        //error_log("Error: my_sched got here. i = $i");
        //availstartday, availendday: day1 is 1st day of con
        //availstarttime, availendtime: 0 is unset; other is index into Times table
        $x = convert_timestamp_to_timeindex($timesXML["XPath"], $partAvail["starttimestamp_$i"], true);
        $partAvail["availstartday_$i"] = $x["day"];
        $partAvail["availstarttime_$i"] = $x["hour"];
        $x = convert_timestamp_to_timeindex($timesXML["XPath"], $partAvail["endtimestamp_$i"], false);
        $partAvail["availendday_$i"] = $x["day"];
        $partAvail["availendtime_$i"] = $x["hour"];
        $i++;
    }
    renderTable($partAvail);
}

if (!$ajax_request_action=$_POST["ajax_request_action"]) {
    Render500ErrorAjax("Unknown action.");
	exit();
}

switch ($ajax_request_action) {
	case "updateGeneralAvailability":
		updateGeneralAvailability();
		break;
    case "setAvailability":
        setAvailability();
        break;
	default:
        Render500ErrorAjax("Unknown action.");
		exit();
}

exit();

// Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $message_error, $messages, $title;
$title = "My Availability";
require('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
require('my_sched_constr_func.php');
$partAvail = get_participant_availability_from_post();
$timesXML = retrieve_timesXML();
$status = validate_participant_availability(); /* return true if OK.  Store error messages in
        global $messages */
for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
    if ($partAvail["availstartday_$i"] == 0) {
        unset($partAvail["availstartday_$i"]);
    }
    if ($partAvail["availstarttime_$i"] == 0) {
        unset($partAvail["availstarttime_$i"]);
    }
    if ($partAvail["availendday_$i"] == 0) {
        unset($partAvail["availendday_$i"]);
    }
    if ($partAvail["availendtime_$i"] == 0) {
        unset($partAvail["availendtime_$i"]);
    }
}
if ($status == false) {
    $message_error = "The data you entered was incorrect.  Database not updated.<br />" . $messages; // error message
    unset($messages);
} else {  /* Update DB */
    $query = "REPLACE ParticipantAvailability SET ";
    $query .= "badgeid='$badgeid', ";
    $query .= "maxprog={$partAvail["maxprog"]}, ";
    $query .= "preventconflict=\"" . mysqli_real_escape_string($linki, $partAvail["preventconflict"]) . "\", ";
    $query .= "otherconstraints=\"" . mysqli_real_escape_string($linki, $partAvail["otherconstraints"]) . "\", ";
    $query .= "numkidsfasttrack={$partAvail["numkidsfasttrack"]};";
    if (!mysqli_query($linki, $query)) {
        $message = $query . "<br />Error updating database.  Database not updated.";
        RenderError($message);
        exit();
    }
    for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
        if (isset($partAvail["availstarttime_$i"]) && $partAvail["availstarttime_$i"] > 0) {
            if (CON_NUM_DAYS == 1) {
                // for 1 day con didn't collect or validate day info; just set day=1
                $partAvail["availstartday_$i"] = 1;
                $partAvail["availendday_$i"] = 1;
            }
            $time = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availstarttime_$i"] . "']/@timevalue)");
            $nextday = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availstarttime_$i"] . "']/@next_day)");
            $findit = strpos($time, ':');
            $hour = substr($time, 0, $findit);
            $restOfTime = substr($time, $findit);
            $starttime = (($partAvail["availstartday_$i"] - 1 + $nextday) * 24 + $hour) . $restOfTime;

            $time = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availendtime_$i"] . "']/@timevalue)");
            $nextday = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availendtime_$i"] . "']/@next_day)");
            $findit = strpos($time, ':');
            $hour = substr($time, 0, $findit);
            $restOfTime = substr($time, $findit);
            $endtime = (($partAvail["availendday_$i"] - 1 + $nextday) * 24 + $hour) . $restOfTime;

            $query = "REPLACE ParticipantAvailabilityTimes SET ";
            $query .= "badgeid=\"$badgeid\",availabilitynum=$i,starttime=\"$starttime\",endtime=\"$endtime\"";
            if (!mysqli_query($linki, $query)) {
                $message = $query . "<br />Error updating database.  Database not updated.";
                RenderError($message);
                exit();
            }
        }
    }
    if (CON_NUM_DAYS >= 1) {
        $query = "REPLACE ParticipantAvailabilityDays (badgeid,day,maxprog) values";
        for ($i = 1; $i <= CON_NUM_DAYS; $i++) {
            $x = $partAvail["maxprogday$i"];
            $query .= "(\"$badgeid\",$i,$x),";
        }
        $query = substr($query, 0, -1); // remove extra trailing comma
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.  Database not updated.";
            RenderError($message);
            exit();
        }
    }
    $query = "DELETE FROM ParticipantAvailabilityTimes WHERE badgeid=\"$badgeid\" and ";
    $query .= "availabilitynum in (";
    $deleteany = false;
    for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
        if (empty($partAvail["availstarttime_$i"])) {
            $query .= $i . ", ";
            $deleteany = true;
        }
    }
    if ($deleteany) {
        $query = substr($query, 0, -2) . ");\n";
        // error_log($query); for debugging only
        if (!mysqli_query_with_error_handling($query, true)) {
            exit();
        }
    }
    if (!$partAvail = retrieve_participantAvailability_from_db($badgeid, true)) {
        exit();
    }
    $i = 1;
    while (isset($partAvail["starttimestamp_$i"])) {
        //error_log("submit_my_sched got here. i = $i");
        //availstartday, availendday: day1 is 1st day of con
        //availstarttime, availendtime: 0 is unset; other is index into Times table
        $x = convert_timestamp_to_timeindex($timesXML["XPath"], $partAvail["starttimestamp_$i"], true);
        $partAvail["availstartday_$i"] = $x["day"];
        $partAvail["availstarttime_$i"] = $x["hour"];
        $x = convert_timestamp_to_timeindex($timesXML["XPath"], $partAvail["endtimestamp_$i"], false);
        $partAvail["availendday_$i"] = $x["day"];
        $partAvail["availendtime_$i"] = $x["hour"];
        $i++;
    }
    $message = "Database updated successfully.";
    unset($message_error);
}
require('renderMySchedConstr.php');
exit();
?>
