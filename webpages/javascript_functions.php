<?php
//	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
function load_external_javascript($isDataTables = false, $isRecaptcha = false, $bootstrap4 = false) {
    if ($bootstrap4) { ?>
    <script src="external/jquery3.5.1/jquery-3.5.1.min.js"></script>
    <script src="external/bootstrap4.5.0/bootstrap.bundle.min.js" type="text/javascript"></script>
<?php } else { ?>
    <script src="external/jquery1.7.2/jquery-1.7.2.min.js"></script>
    <script src="external/jqueryui1.8.16/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="external/bootstrap2.3.2/bootstrap.js" type="text/javascript"></script>
<?php } ?>
    <script src="external/choices9.0.0/choices.min.js"></script>
<?php if ($isDataTables && $bootstrap4) { ?>
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<?php } else if ($isDataTables) { ?>
    <script src="external/dataTables1.10.16/jquery.dataTables.js"></script>
<?php }
    if ($isRecaptcha) { ?>
    <script async defer id="recaptcha-script" src="https://www.google.com/recaptcha/api.js"></script>
<?php }
}

function get_internal_url($script) {
    return $script . '?v=' . hash_file('md5', $script);
}

function load_internal_javascript($title, $isDataTables = false) {
    ?>
    <script src="<?php echo get_internal_url("js/main.js"); ?>"></script>
    <?php
    /**
     * These js files initialize themselves and therefore should be included only on the relevant pages.
     * See main.js
     *
     * Forgot Password -- ForgotPassword.js
     * Invite Participants -- InviteParticipants.js
     * Maintain Room Schedule -- MaintainRoomSched.js
     * Reset Password -- ForgotPasswordResetForm.js
     * Session History -- SessionHistory.js
     *
     * Other js files may be included in this switch statement, but aren't required
     */
    switch ($title) {
        case "Forgot Password":
            echo "<script src=\"" . get_internal_url("js/ForgotPassword.js") . "\"></script>\n";
            break;
        case "Invite Participants":
            echo "<script src=\"" . get_internal_url("js/InviteParticipants.js") . "\"></script>\n";
            break;
        case "Maintain Room Schedule":
            echo "<script src=\"" . get_internal_url("js/MaintainRoomSched.js") . "\"></script>\n";
            break;
        case "Reset Password":
            echo "<script src=\"" . get_internal_url("js/ForgotPasswordResetForm.js") . "\"></script>\n";
            break;
        case "Session History":
            echo "<script src=\"" . get_internal_url("js/SessionHistory.js") . "\"></script>\n";
            break;
        case "Administer Phases":
            echo "<script src=\"" . get_internal_url("js/AdminPhases.js") . "\"></script>\n";
            break;
        case "Edit Custom Text":
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/EditCustomText.js") . "\"></script>\n";
            break;
        case "Edit Survey":
            echo "<script src=\"external/tabulator-4.9.3/js/tabulator.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/EditSurvey.js") . "\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/RenderSurvey.js") . "\"></script>\n";
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            break;
        case "Participant Survey":
            echo "<script src=\"" . get_internal_url("js/PartSurvey.js") . "\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/RenderSurvey.js") . "\"></script>\n";
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
        case "Preview Survey":
            echo "<script src=\"" . get_internal_url("js/RenderSurvey.js") . "\"></script>\n";
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            break;
        case "Session Search Results":
        case "Search Sessions":
            echo "<script src=\"" . get_internal_url("js/PartSearchSessionsSubmit.js") . "\"></script>\n";
            break;
        case "Administer Participants":
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/AdminParticipants.js") . "\"></script>\n";
            break;
        case "Administer Photos":
            echo "<script src=\"external/croppie.2.6.5/croppie.min.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/AdminPhotos.js") . "\"></script>\n";
            break;
        case "My Profile":
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/myProfile.js") . "\"></script>";
            break;
        case "General Interests":
            echo "<script src=\"" . get_internal_url("js/generalInterests.js") . "\"></script>";
            break;
        case "Change Password":
            echo "<script src=\"" . get_internal_url("js/changePassword.js") . "\"></script>";
            break;
        case "My Availability":
            echo "<script src=\"" . get_internal_url("js/myAvailability.js") . "\"></script>";
            break;
        case "My Photo":
            echo "<script src=\"external/croppie.2.6.5/croppie.min.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/myPhoto.js") . "\"></script>";
            break;
        case "Edit Session":
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/editCreateSession.js") . "\"></script>\n";
            break;
        case "Create New Session":
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/editCreateSession.js") . "\"></script>\n";
            break;
        case "Edit Configuration Tables":
            echo "<script src=\"external/tinymce-5.6.2/js/tinymce/tinymce.min.js\"></script>\n";
            echo "<script src=\"external/tabulator-4.9.3/js/tabulator.js\"></script>\n";
            echo "<script src=\"" . get_internal_url("js/EditConfigTables.js") . "\"></script>\n";
            break;
        case "Table Tents":
            echo "<script src=\"" . get_internal_url("js/tabletents.js") . "\"></script>\n";
            break;
        default:
            if ($isDataTables) {
                echo "<script src=\"" . get_internal_url("js/Reports.js") . "\"></script>\n";
            }
    }
?>
<script src="<?php echo get_internal_url("js/staffMaintainSchedule.js"); ?>"></script>
<script src="<?php echo get_internal_url("js/partPanelInterests.js"); ?>"></script>
<?php
}
?>