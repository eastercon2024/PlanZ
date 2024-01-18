<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.

global $congoinfo, $linki, $message2, $message_error, $participant, $title;
$title = "My Interests";
require('PartCommonCode.php'); // initialize db; check login;

if (!may_I('my_gen_int_write')) {
    $message = "Currently, you do not have write access to this page.\n";
    RenderErrorAjax($message);
    exit();
}

$rolerows = $_POST["rolerows"];
$interestrows = $_POST["interestrows"];
$yespanels = stripslashes($_POST["yespanels"]);
$nopanels = stripslashes($_POST["nopanels"]);
$yespeople = stripslashes($_POST["yespeople"]);
$nopeople = stripslashes($_POST["nopeople"]);
$otherroles = stripslashes($_POST["otherroles"]);
$rolearray = array();
for ($i = 0; $i < $rolerows; $i++) {
    $rolearray[$i] = array();
    $rolearray[$i]["roleid"] = $_POST["roleid" . $i];
    $rolearray[$i]["willdorole"] = isset($_POST["willdorole" . $i]) && $_POST["willdorole" . $i] == 1;
}
$rolearray['count'] = $rolerows;

$interestarray = array();
for ($i = 1; $i < $interestrows; $i++) {
    $interestarray[$i] = array();
    $interestarray[$i]["interestid"] = $_POST["interestid" . $i];
    $interestarray[$i]["willdointerest"] = isset($_POST["willdointerest" . $i]) && $_POST["willdointerest" . $i] == 1;

}
$interestarray['count'] = $interestrows;

$query = "REPLACE INTO ParticipantInterests SET badgeid='$badgeid',";
$query .= "yespanels=\"" . mysqli_real_escape_string($linki, $yespanels);
$query .= "\",nopanels=\"" . mysqli_real_escape_string($linki, $nopanels);
$query .= "\",yespeople=\"" . mysqli_real_escape_string($linki, $yespeople);
$query .= "\",nopeople=\"" . mysqli_real_escape_string($linki, $nopeople);
$query .= "\",otherroles=\"" . mysqli_real_escape_string($linki, $otherroles) . "\"";
if (!mysqli_query($linki, $query)) {
    $message = $query . "<br>Error inserting into database.  Database not updated.";
    Render500ErrorAjax($message);
    exit();
}

for ($i = 0; $i < $rolerows; $i++) {
    if ($rolearray[$i]["willdorole"]) {
        $query = "REPLACE INTO ParticipantHasRole SET badgeid=\"" . $badgeid . "\", roleid=" . $rolearray[$i]["roleid"] . "";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br>Error inserting into database x.  Database not updated.";
            Render500ErrorAjax($message);
            exit();
        }
    } else {
        $query = "DELETE FROM ParticipantHasRole WHERE badgeid=\"" . $badgeid . "\" AND ";
        $query .= "roleid=" . $rolearray[$i]["roleid"];
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br>Error deleting from database.  Database not updated.";
            Render500ErrorAjax($message);
            exit();
        }
    }
}

for ($i = 1; $i < $interestrows; $i++) {
    if ($interestarray[$i]["willdointerest"]) {
        $query = "REPLACE INTO ParticipantHasInterest set badgeid=\"" . $badgeid . "\", interestid=" . $interestarray[$i]["interestid"] . "";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br>Error inserting into database.  Database not updated.";
            Render500ErrorAjax($message);
            exit();
        }
    } else {
        $query = "DELETE FROM ParticipantHasInterest WHERE badgeid=\"" . $badgeid . "\" AND ";
        $query .= "interestid=" . $interestarray[$i]["interestid"];
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br>Error deleting from database.  Database not updated.";
            Render500ErrorAjax($message);
            exit();
        }
    }
}


?>
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-success">
                Database updated successfully
            </div>
        </div>
    </div>
<?php
?>
