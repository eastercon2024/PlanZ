<?php
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('db_functions.php');
require_once('error_functions.php');
require_once('StaffPublish_FNC.php');

$pub_sched_id = $_POST["pub_sched_id"];

$query = "SELECT program_json, people_json FROM PublishedSchedule WHERE pub_sched_id = ?";
$result = mysqli_query_with_prepare_and_exit_on_error($query, "s", array($pub_sched_id));
if (!$result) {
    Render500ErrorAjax("<p>Failed to retrieve published schedule.</p>");
    exit();
}

$row = mysqli_fetch_row($result);
$new_program = json_decode($row[0]);
$new_people = json_decode($row[1]);

$query = "INSERT INTO PublishHistory(pub_sched_id, status, published_time, published_user) VALUES(?, 'Publishing to ConClar', NOW(), ?)";
mysqli_query_with_prepare_and_exit_on_error($query, "ss", array($pub_sched_id, $_SESSION['badgeid']));
$publish_id = mysqli_insert_id($linki);

if (ENVIRONMENT == "STAGING") {
    $json_root = "/home/u943682649/guide-staging/";
} else {
    $json_root = "/home/u943682649/guide/";
}

$programFH = fopen($json_root . "program.json","wb");
$peopleFH = fopen($json_root . "people.json","wb");

if ($programFH === FALSE) {
    $message_error = "Cannot open " . $programFH . " for writing.";
    error_log($message_error);
    RenderError($message_error);
    exit(1);
}
if ($peopleFH === FALSE) {
    $message_error = "Cannot open " . $peopleFH . " for writing.";
    error_log($message_error);
    RenderError($message_error);
    exit(1);
}
if (fwrite($programFH, json_encode($new_program)) === FALSE) {
    $message_error = "Error writing to " . $programFH . ".";
    error_log($message_error);
    RenderError($message_error);
    fclose($programFH);
    fclose($peopleFH);
    exit(1);
}
if (fwrite($peopleFH, json_encode($new_people)) === FALSE) {
    $message_error = "Error writing to " . $peopleFH . ".";
    error_log($message_error);
    RenderError($message_error);
    fclose($programFH);
    fclose($peopleFH);
    exit(1);
}
fclose($programFH);
fclose($peopleFH);

$query = "UPDATE PublishHistory SET status=? WHERE publish_id=?";

mysqli_query_with_prepare_and_exit_on_error($query, "ss", array("Completed", $publish_id));

echo "<p>Succesfully published schedule</p>";

?>