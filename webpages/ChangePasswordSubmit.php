<?php
require ('PartCommonCode.php'); // initialize db; check login;

$oldPassword = getString('oldPassword');
$newPassword = getString('newPassword');

if (is_null($newPassword) || strlen($newPassword) == 0) {
    Render500ErrorAjax("New password must not be empty.");
    exit();
}

$query = "SELECT password FROM Participants WHERE badgeid=?";;
$result =  mysqli_query_with_prepare_and_exit_on_error($query, 's', array($badgeid));
$row = mysqli_fetch_assoc($result);
if (!$row) {
    Render500ErrorAjax("Could not find user in database.");
    exit();
}
$hash = $row['password'];
if (!password_verify($oldPassword, $hash)) {
    Render500ErrorAjax("Old password is incorrect.");
    exit();
}

$hash = password_hash($newPassword, PASSWORD_DEFAULT);
$query = "UPDATE Participants SET password=? WHERE badgeid=?";
$rows = mysql_cmd_with_prepare($query, "ss", array($hash, $badgeid));
if (is_null($rows) || $rows != 1) {
  Render500ErrorAjax("Could not update password. " . $result . " " . mysqli_error($linki));
  exit();
}