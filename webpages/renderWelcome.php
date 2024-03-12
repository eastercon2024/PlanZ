<?php
// Copyright (c) 2008-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message, $message_error, $message2, $title;
// $participant_array is defined by file including this.
$title = "Participant View";
require_once('PartCommonCode.php');
populateCustomTextArray(); // title changed above, reload custom text with the proper page title
participant_header($title, false, 'Normal', true);
if ($message_error != "") {
    echo "<P class=\"alert alert-error\">$message_error</P>\n";
}
if ($message != "") {
    echo "<P class=\"alert alert-success\">$message</P>\n";
}
$chint = ($participant_array["interested"] == 0);


if (may_I('postcon')) { ?>
    <?php echo fetchCustomText('post_con'); ?>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--<?php echo CON_NAME; ?> Program and Events Committees</p>
    <?php
    participant_footer();
    exit();
}
?>

<?php
    echo fetchCustomText('alerts');
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p>Welcome, <?php echo $participant_array["firstname"]; echo " "; echo $participant_array["lastname"]; ?>, to the <?php echo CON_NAME; ?> Programming website.</p>
                <p>The deadline for signing up for programme participation has passed. We are now in the process of contacting people and scheduling when programme items will happen.</p>
                <p>If you think you should really be on a programme item but did not complete the form, please email us at <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a>. However, we can't make any garauntees we can fit you in.</p>
                <p>If you have been contacted about being on specific items, you can confirm on the <a href="/MySchedule.php">My Schedule</a> page. Please also consider filling out a bio on the <a href="/my_contact.php">Profile</a> page.</p>
                <p>All participants are expected to follow the convention <a href="https://eastercon2024.co.uk/code-of-conduct/">Code of Conduct</a>.</p>
                <p>
                    <?php if ($participant_array["regtype"] == null || $participant_array["regtype"] == '') { ?>
                        You are currently <b>not registered</b> for <?php echo CON_NAME; ?>. 
                        <?php if (defined("REGISTRATION_URL") && REGISTRATION_URL !== "") { ?>
                            <a href="<?php echo REGISTRATION_URL ?>">Register now</a>.
                        <?php } ?>
                    <?php } else { ?>
                        Your current membership type is <b><?php echo $participant_array["regtype"] ?></b>.
                    <?php } ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p> Use the "Profile" menu to:</p>
                <ul>
                    <li> Check your contact information. </li>
                    <?php if (RESET_PASSWORD_SELF == true) { ?>
                        <li> Change your password.</li>
                    <?php } ?>
                    <li> Indicate whether you will be participating in <?php echo CON_NAME; ?>.</li>
                    <li> Opt out of sharing your email address with other program participants.</li>
                    <?php if (may_I('EditBio')) { ?>
                        <li> Edit your name as you want to appear in our publications.</li>
                        <li> Enter a short bio for <?php echo CON_NAME; ?> publications.</li>
                    <?php } else { ?>
                        <li> The following items are currently read-only. If you need to make a change here, please email us: <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a></li>
                        <ul>
                            <li> View your name as you want to appear in our publications.</li>
                            <li> View your bio for <?php echo CON_NAME; ?> publications.</li>
                        </ul>
                    <?php } ?>
                    <?php if (PARTICIPANT_PHOTOS === TRUE) { ?>
                        <li> Upload a photo to use with our online program guide.</li>
                    <?php } ?>
                </ul>

                <ul>
                    <li> Indicate whether you have any accessibility issues we should be aware of.</li>
                    <li> Indicate your race, gender, sexual orientation, and pronouns.</li>
                    <li> Update other personal information.</li>
                    <li> NOTE: This optional information will be kept confidential and will be used to help create diverse panels.</li>
                </ul>

                <?php if ($_SESSION['survey_exists']) { ?>
                    <p> Use the "Survey" menu to:</p>
                    <ul>
                        <li>Provide optional demographic information to help us create a program that reflects diverse views.</li>
                        <li>Provide information on accessibility needs.</li>
                    </ul>
                <?php } ?>

                <?php if (may_I('my_availability')) { ?>
                    <p> Use the "Availability" menu to:</p>
                    <ul>
                        <li> State how many panels you are willing to do overall and/or by day.</li>
                        <li> List the times that you are available.</li>
                        <li> List other constraints that we should know about.</li>
                    </ul>
                    <p> Use the "General Interests" menu to:</p>
                    <ul>
                        <li> Describe the kinds of sessions you are interested in.</li>
                        <li> Suggest the people you would like to work with.</li>
                    </ul>
                <?php } ?>

                <?php if (may_I('search_panels')) { ?>
                    <p> Use the "Search Sessions" menu to:</p>
                    <ul>
                        <li> See suggested topics for <?php echo CON_NAME; ?> programming. </li>
                        <li> Indicate sessions you would like to participate on. </li>
                    </ul>
                <?php } ?>
                
                <?php if (may_I('my_schedule')) { ?>
                    <p> Use the "My Schedule" menu to:</p>
                    <ul>
                        <li> See what you have been scheduled to do at con.</li>
                        <li> If there are issues, conflict or questions please email us at 
                            <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a></li>
                    </ul>
                <?php } else { ?>
                    <p> The "My Schedule" menu is currently unavailable.  Check back later.</p>
                <?php } ?>
                
                <?php if (may_I('BrainstormSubmit')) { ?>
                    <p> Use the "Suggest a Session" menu to:</p>  
                    <ul>
                        <li> Enter the brainstorming view where you can submit panel, workshop and presentation ideas.
                        <li> You can return back to this page by clicking on "Participant View" tab in the upper right corner. 
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php 
    $add_part_overview = fetchCustomText("part_overview");
    if (strlen($add_part_overview) > 0) { ?>
<div class="row mt-4">
    <div class="col col-sm-12">
        <?php echo $add_part_overview; ?>
    </div>
</div>
<?php } ?>

<div class="row mt-4">
    <div class="col col-sm-12">
        <p>Thank you for your time, and we look forward to seeing you at <?php echo CON_NAME; ?>.</p> 
        <p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </p>
    </div>
</div>

<?php participant_footer(); ?>
