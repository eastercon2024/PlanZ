<?php
// Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function render_session_interests($session_interest_count,$message,$message_error, $pageIsDirty, $showNotAttendingWarning) {
    global $session_interests, $title;
    participant_header($title, false, 'Normal', true);
    if ($message_error) {
        echo "<p class=\"alert alert-error\">Database not updated.<br />" . $message_error . "</p>";
    }
    if ($message) {
        echo "<p class=\"alert alert-success\">" . $message . "</p>";
    }
    if ($pageIsDirty) {
        echo "<input type=\"hidden\" id=\"pageIsDirty\" value=\"true\" />\n";
    }
    // "Update Ranks" Section
    echo "<form id=\"sessionFRM\" class=\"form container px-0 mt-2 mb-4\" name=\"sessionform\">\n";
    echo "<input type=\"hidden\" name=\"submitranks\" value=\"1\" />\n";
    echo "<div class=\"card\">\n";
    echo "<div class=\"card-header\">\n";
    echo "<h2>Session Interests</h2>\n";
    echo "<p>You are limited to 3 sessions each of preference.</p>\n";
    if ($showNotAttendingWarning) {
        echo "<div class=\"alert alert-primary\" style=\"margin:15px 0;\">\n";
        echo "    <h4>Warning!</h4>\n";
        echo "    <span>\n";
        echo "        You have not indicated in <a href=\"my_contact.php\">your profile</a> that you will be attending " . CON_NAME . ".\n";
        echo "        You will not be able to save your panel choices until you so do.\n";
        echo "    </span>\n";
        echo "</div>\n";
        $disabled = "disabled=\"disabled\" ";
    } else {
        $disabled = "";
        echo "<div id=\"update-warning\" class=\"alert alert-primary\">You must click Update to save your preferences.</div>\n";
        echo "<div id=\"resultBoxDIV\"></div>\n";
        echo "\n";
    }
    echo "<div class=\"submit\"><button class=\"btn btn-primary pull-right\" type=\"submit\" $disabled>Update</button></div>\n";
    echo "</div>\n";
    echo "<div class=\"card-body\">\n";
    echo "<div id=\"interests_body\" class=\"row-fluid\">\n";
    render_session_interests_body($session_interest_count, $showNotAttendingWarning);
    echo "</div>\n";
    echo "</div>\n";
    echo "<div class=\"card-footer\">\n";
    echo "<div class=\"submit\"><button class=\"btn btn-primary pull-right\" type=\"submit\" $disabled>Update</button></div>\n";
    echo "<input type=\"hidden\" id=\"autosaveHID\" name=\"autosave\" value=\"0\" />\n";
    echo "</div>\n";
    echo "</div>\n";
    echo "</form>\n";
    echo "<div id=\"addButDirtyMOD\" class=\"modal hide\" data-backdrop=\"static\">\n";
    echo "  <div class=\"modal-header\">\n";
    echo "    <button type=\"button\" class=\"close\" onclick=\"panelInterests.dismissAutosaveWarn();\" aria-hidden=\"true\">&times;</button>\n";
    echo "    <h3>Unsaved edits</h3>\n";
    echo "  </div>\n";
    echo "  <div class=\"modal-body\">\n";
    echo "    <p>You have unsaved edits which will be lost by adding a new session to your list.  Please save your edits first.</p>\n";
    echo "  </div>\n";
    echo "  <div class=\"modal-footer\">\n";
    echo "    <a href=\"#\" class=\"btn btn-primary\" onclick=\"panelInterests.doAutosave();\">Save changes</a>\n";
    echo "    <a href=\"#\" class=\"btn\" onclick=\"$('#addFRM').get(0).submit();\">Continue without saving</a>\n";
    echo "    <a href=\"#\" class=\"btn\" data-dismiss=\"modal\">Cancel</a>\n";
    echo "  </div>\n";
    echo "</div>\n";
    echo "<div id=\"autosaveMOD\" class=\"modal hide\" data-backdrop=\"static\">\n";
    echo "  <div class=\"modal-header\">\n";
    echo "    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>\n";
    echo "    <h3>Please save</h3>\n";
    echo "  </div>\n";
    echo "  <div class=\"modal-body\">\n";
    echo "    <p>You have been editing your responses for 10 minutes or more without saving your work.  Please save now.</p>\n";
    echo "  </div>\n";
    echo "  <div class=\"modal-footer\">\n";
    echo "    <a href=\"#\" class=\"btn btn-primary\" onclick=\"panelInterests.doAutosave();\">Save changes</a>\n";
    echo "    <a href=\"#\" class=\"btn\" onclick=\"panelInterests.dismissAutosaveWarn();\" >Dismiss</a>\n";
    echo "  </div>\n";
    echo "</div>\n";
    participant_footer();
}

function render_session_interests_body($session_interest_count, $showNotAttendingWarning) {
    global $session_interests;

    if ($showNotAttendingWarning) {
        $disabled = "disabled=\"disabled\" ";
    } else {
        $disabled = "";
    }

    $has_shown = 0;

    $j = 1; //use $j so that skipped sessions don't skip numbering
    for ($i = 1; $i <= $session_interest_count; $i++) {
        if (!isset($session_interests[$i]['title'])) continue;
        if (!$session_interests[$i]['title']) continue;
        $has_shown += 1;

        echo "<div class=\"card p-2 mb-2\">\n";
        echo "    <input type=\"hidden\" name=\"sessionid$j\" class=\"mycontrol\" value=\"{$session_interests[$i]['sessionid']}\" />\n";
        echo "    <div class=\"row\">\n";
        echo "        <div class=\"col-auto\"><h5>" . htmlspecialchars($session_interests[$i]['title'], ENT_NOQUOTES) . "</h5></div>\n";
        echo "    </div>\n";
        echo "    <div class=\"row mb-1\">\n";
        echo "        <div class=\"col-auto\">\n";
        echo $session_interests[$i]['typename'] . " &bull; " . $session_interests[$i]['duration'];
        if (!empty($session_interests[$i]['tags'])) {
            echo " &bull; " . $session_interests[$i]['tags'];
        }
        echo "        </div>\n";
        echo "    </div>\n";
        echo "    <div class=\"row\">\n";
        echo "        <div class=\"col-auto\">\n";
        echo "            <select id=\"rankINP_$j\" name=\"rank$j\" class=\"mycontrol form-control\" $disabled>\n";
        echo "                <option value=\"\" " . ((!isset($session_interests[$i]['rank'])) ? "selected=\"selected\"" : "") . ">- Select rank -</option>\n";
        echo "                <option value=\"1\" " . (($session_interests[$i]['rank'] == 1) ? "selected=\"selected\"" : "") . ">1 - Ooh! Ooh! Pick me!</option>\n";
        echo "                <option value=\"2\" " . (($session_interests[$i]['rank'] == 2) ? "selected=\"selected\"" : "") . ">2 - I'd like to if I can</option>\n";
        echo "                <option value=\"3\" " . (($session_interests[$i]['rank'] == 3) ? "selected=\"selected\"" : "") . ">3 - It would be nice</option>\n";
        echo "                <option value=\"4\" " . (($session_interests[$i]['rank'] == 4) ? "selected=\"selected\"" : "") . ">4 - I'm kind of interested</option>\n";
        echo "            </select>\n";
        echo "        </div>\n";
        echo "    </div>\n";
        echo "    <div class=\"row mt-2\">\n";
        echo "        <div class=\"col-auto\">\n";
        echo "            <input type=\"checkbox\" id=\"modCHK_$j\" class=\"checkbox mycontrol\" value=\"1\" name=\"mod$j\" ".(($session_interests[$i]['willmoderate'])?"checked":"")." $disabled/>\n";
        echo "            <label class=\"inline\" for=\"modCHK_$j\">I'd like to moderate this session </label>\n";
        echo "        </div>\n";
        echo "    </div>\n";
        echo "    <div class=\"row\">\n";
        echo "        <div class=\"col-auto\">\n";
        echo "            <input type=\"checkbox\" id=\"deleteCHK_$j\" class=\"checkbox mycontrol\" value=\"1\" name=\"delete$j\" $disabled/>\n";
        echo "            <label class=\"inline \" for=\"deleteCHK_$j\">Remove this session from my list </label>\n";
        echo "        </div>\n";
        echo "    </div>\n";
        echo "    <div class=\"row\">\n";
        echo "        <label class=\"col-auto\">Use this space to convince us why you would be fabulous on this session: </label>\n";
        echo "    </div>\n";
        echo "    <div class=\"row\">\n";
        echo "        <div class=\"col-auto\">\n";
        echo "            <textarea id=\"commentsTXTA_$j\" class=\"span12 sessionWhyMe mycontrol form-control\" cols=\"80\" name=\"comments$j\" $disabled>". htmlspecialchars( $session_interests[$i]['comments'],ENT_COMPAT)."</textarea>\n";
        echo "        </div>\n";
        echo "    </div>\n";
        echo "    <div class=\"row mt-2\">\n";
        echo "        <div class=\"col-auto\">" . htmlspecialchars($session_interests[$i]['progguiddesc'], ENT_NOQUOTES) . "</div>\n";
        echo "    </div>\n";
        if ($session_interests[$i]['persppartinfo']) {
            echo "    <div class=\"row mt-2\">\n";
            echo "        <div class=\"col-auto\">" . htmlspecialchars($session_interests[$i]['persppartinfo'], ENT_NOQUOTES) . "</div>\n";
            echo "    </div>\n";
        }    
        echo "</div>\n";
        $j++;
    }

    if ($has_shown == 0) {
        echo "<p>You have not selected any sessions yet.</p>\n";
        echo "<p>Find some sessions on the <a href=\"PartSearchSessions.php\">Search Sessions</a> page and check the \"I am interested\" box.</p>\n";
    }
}
?>
