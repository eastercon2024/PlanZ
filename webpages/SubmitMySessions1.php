<?php
// Copyright (c) 2005-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $badgeid;

require('PartCommonCode.php'); //define database functions

$sessionId = getInt("sessionId");
$checked = getInt("checked");

if ($checked == 1) {
    $query = "INSERT INTO ParticipantSessionInterest SET badgeid=?, sessionid=?";
} else {
    $query = "DELETE FROM ParticipantSessionInterest WHERE badgeid=? AND sessionid=?";
}
$rows =  mysql_cmd_with_prepare($query, 'si', array($badgeid, $sessionId));
if ($rows != 1) {
    Render500ErrorAjax("Unable to update database");
    exit();
}
?>