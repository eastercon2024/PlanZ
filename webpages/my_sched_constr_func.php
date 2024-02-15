<?php
// Copyright (c) 2015-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function convert_timestamp_to_timeindex($timesXPath, $timestamp, $start) {
    // $timestamp in hh:mm:ss or hhh:mm:ss from start of con
    // start = 1 if starttime; 0 if endtime
    $hour = 0 + substr($timestamp, 0, strlen($timestamp) - 6); // get 1st 2 if hh:mm:ss or 1st 3 if hhh:mm:ss
    $minute = substr($timestamp, strlen($timestamp) - 5, 2); // handle hh:mm:ss or hhh:mm:ss
    //echo($hour)."&nbsp;&nbsp;";
    if (($hour % 24) >= DAY_CUTOFF_HOUR) {
        $next_day = 0;
        $day = 1 + floor($hour / 24);
    } else {
        $next_day = 1;
        $day = 0 + floor($hour / 24);
    }
    $hour %= 24;
    $searchTime = (($hour < 10) ? "0" : "") . $hour . ":" . $minute . ":00";
    $xPathQuery = "string(query/row[@next_day='";
    $xPathQuery .= ($next_day == 1) ? "1" : "0";
    $xPathQuery .= "' and @" . (($start) ? "avail_start" : "avail_end") . "='1' and ";
    $xPathQuery .= "@timevalue = '" . $searchTime . "']/@timeid)";
    //echo($xPathQuery)."<BR>";
    $timesIndex = $timesXPath->evaluate($xPathQuery);
    if (strlen($timesIndex) == 0)
        $timesIndex = "0";
    return (array("day" => $day, "hour" => $timesIndex));
}

function retrieve_timesXML() {
    global $message_error;
    $result = array();
    $query = array();
	$query["times"] = "SELECT timeid, DATE_FORMAT(timevalue,'%T') AS timevalue, timedisplay, next_day, avail_start, avail_end FROM Times ";
	$query["times"] .= "WHERE avail_start = 1 or avail_end = 1";
	if (!$result["XML"] = mysql_query_XML($query)) {
        RenderError($message_error);
        exit();
    }
	$result["XPath"] = new DOMXPath($result["XML"]);
	$result["variablesNode"] = $result["XML"]->createElement("variables");
	$docNode = $result["XML"]->getElementsByTagName("doc")->item(0);
	$result["variablesNode"] = $docNode->appendChild($result["variablesNode"]);
	return $result;
}

function renderAvailItems($partAvail) {    
    $query = "SELECT timeid, timedisplay, avail_start, avail_end FROM Times WHERE avail_start = 1 or avail_end = 1 ORDER BY display_order";
    $result = mysqli_query_with_error_handling($query, true);
    $times = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $times[$row["timeid"]] = $row;
    }

    $i=1;
    while (isset($partAvail["availstartday_$i"])) {
        if ($partAvail["location_$i"] == "onsite") {
            $location = "in person";
        } else {
            $location = "virtually";
        }
        echo "<div data-availstartday=\"" . $partAvail["availstartday_$i"] . "\" data-availstarttime=\"" . $partAvail["availstarttime_$i"] . "\" data-availendday=\"" . $partAvail["availendday_$i"] . "\" data-availendtime=\"" . $partAvail["availendtime_$i"] . "\" data-availlocation=\"" . $partAvail["location_$i"] . "\">\n";
        echo "<p style=\"display: inline-block; width: 16.5ch; margin: 0\" class=\"pr-2\">" . longDayNameFromInt($partAvail["availstartday_$i"]) . " " . $times[$partAvail["availstarttime_$i"]]["timedisplay"] . "</p>";
        echo "<p style=\"display: inline-block; margin: 0\" class=\"pr-2\">-</p>";
        echo "<p style=\"display: inline-block; width: 16.5ch; margin: 0\" class=\"pr-2\">" . longDayNameFromInt($partAvail["availendday_$i"]) . " " . $times[$partAvail["availendtime_$i"]]["timedisplay"] . "</p>";
        echo "<p style=\"display: inline-block; width: 10ch; margin: 0\" class=\"pr-2\">(" . $location . ")</p>";
        echo "<p style=\"display: inline-block\">\n";
        echo "<button class=\"btn btn-primary\" type=\"button\" name=\"edit\">Edit</button>\n";
        echo "<button class=\"btn btn-danger\" type=\"button\" name=\"delete\">Delete</button>\n";
        echo "</p>\n";
        echo "</div>\n";
        $i++;
    }
}


function renderItems($partAvail) {    
    $query = "SELECT timeid, timedisplay, avail_start, avail_end FROM Times WHERE avail_start = 1 or avail_end = 1 ORDER BY display_order";
    $result = mysqli_query_with_error_handling($query, true);
    $times = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $times[$row["timeid"]] = $row;
    }

    $i=1;
    while (isset($partAvail["availstartday_$i"])) {
        echo "<tr>\n";
        echo "<td class=\"align-middle\" data-availstartday=\"" . $partAvail["availstartday_$i"] . "\">" . longDayNameFromInt($partAvail["availstartday_$i"]) . "</td>\n";
        echo "<td class=\"align-middle\" data-availstarttime=\"" . $partAvail["availstarttime_$i"] . "\">" . $times[$partAvail["availstarttime_$i"]]["timedisplay"] . "</td>\n";
        echo "<td class=\"text-center align-middle\">-</td>\n";
        echo "<td class=\"align-middle\" data-availendday=\"" . $partAvail["availendday_$i"] . "\">" . longDayNameFromInt($partAvail["availendday_$i"]) . "</td>\n";
        echo "<td class=\"align-middle\" data-availendtime=\"" . $partAvail["availendtime_$i"] . "\">" . $times[$partAvail["availendtime_$i"]]["timedisplay"] . "</td>\n";
        echo "<td>\n";
        echo "<button class=\"btn btn-primary\" type=\"button\" name=\"edit\">Edit</button>\n";
        echo "<button class=\"btn btn-danger\" type=\"button\" name=\"delete\">Delete</button>\n";
        echo "</td>\n";
        echo "</tr>\n";
        $i++;
    }
}

?>
