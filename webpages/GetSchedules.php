<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// This page has two completely different entry points from a user flow standpoint:
//   1) Beginning of send email flow -- start to specify parameters
//   2) After verify -- 'back' can change parameters -- 'send' fire off email sending code
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('email_functions.php');
require_once('external/swiftmailer-5.4.8/lib/swift_required.php');
require_once('name.php');
global $title, $message, $link;
if (!(isLoggedIn() && may_I("SendEmail"))) {
    exit(0);
}

/*
SELECT badgeid, email, pubsname, firstname, lastname, badgename FROM Participants P JOIN CongoDump CD USING (badgeid) WHERE EXISTS (SELECT 1 FROM ParticipantOnSession POS WHERE POS.badgeid = P.badgeid)
*/
$query = "SELECT badgeid, email, badgename FROM Participants P JOIN CongoDump CD USING (badgeid) WHERE EXISTS (SELECT 1 FROM ParticipantOnSession POS WHERE POS.badgeid = P.badgeid)";
$result = mysqli_query_exit_on_error($query);
$recipientinfo = [];
$i = 0;
while ($recipientinfo[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $i++;
}
mysqli_free_result($result);


$schedules = generateSchedules("2", $recipientinfo);

echo '"email","name",';
for ($i = 0; $i < 13; $i++) {
    echo '"title' . $i . '",';
    echo '"start' . $i . '",';
    echo '"duration' . $i . '",';
    echo '"room' . $i . '",';
    echo '"how' . $i . '",';
    echo '"role' . $i . '"';
    if ($i < 12) {
        echo ',';
    }
}
echo "\n";

foreach ($recipientinfo as $recipient) {
  $scheduleInfo = "";
  $scheduleInfoArray = $schedules[$recipient['badgeid']];
  for ($i = 0; $i < 78; $i++) {
    if ($i < count($scheduleInfoArray)) {
      $scheduleInfo .= ',"' . $scheduleInfoArray[$i] . '"';
    }else {
      $scheduleInfo .= ',""';
    }
  }
  echo '"' . $recipient["email"] . '","' . $recipient["badgename"] . $scheduleInfo . "\n";
}
?>