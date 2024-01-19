<?php
    // Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
    //This file should be requested from post on "session interests(ranks)" form
    require ('PartCommonCode.php'); // initialize db; check login; set $badgeid
    require('PartPanelInterests_FNC.php');
    require('PartPanelInterests_Render.php');

    global $session_interests, $session_interest_index, $title, $message;

    if (!may_I('my_panel_interests')) {
        $message = "You do not currently have permission to view this page.<br />\n";
        Render500ErrorAjax($message);
        exit();
    }#

    $session_interest_count = get_session_interests_from_post();
    if (validate_session_interests($session_interest_count)===false) {
        Render500ErrorAjax($message);
        exit();
    } else {
        update_session_interests_in_db($badgeid, $session_interest_count);
        $session_interest_count = get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
    }

    get_si_session_info_from_db($session_interest_count); // Will render its own errors 

    render_session_interests_body($session_interest_count, false);
?>
