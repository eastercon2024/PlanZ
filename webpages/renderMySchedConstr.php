<?php
// Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// $timesXML array defined on global scope
participant_header($title, false, 'Normal', true);
?>

<?php if (!empty($message_error)) { ?>
    <p class="alert alert-danger mt-2"><?php echo $message_error; ?></p>
<?php } ?>
<?php if (!empty($message)) { ?>
    <p class="alert alert-success mt-2"><?php echo $message; ?></p>
<?php } ?>

<div id="constraint">
        <div class="row mt-2">
            <div class="col-lg-4">
                <form id="generalAvailabilityForm">
                    <div class="card">
                        <div class="card-header">
                            <h2>Availability</h2>
                            <div id="generalAvailabilityResultBoxDIV"></div>
                        </div>
                        <div class="card-body">
                            <p> Please indicate the maximum number of sessions you are willing to be on.
                                You may indicate a total for each day as well as an overall maximum for
                                the whole con.</p>
                            <p><small>There is no need for the numbers to add up. We'll
                                use this for guidance when assigning and scheduling sessions.</small></p>
                            <div class="form-group row">                            
                                <label class="col-md-7 offset-md-1 col-form-label" for="maxprog">Preferred total number of sessions:</label>
                                <div class="col-md-3">
                                    <input class="form-control" type="number" min="0" max="<?php echo PREF_TTL_SESNS_LMT; ?>" size=3 name="maxprog" id="maxprog" value="<?php echo $partAvail["maxprog"]; ?>">
                                </div>
                            </div>
                            <div>
                                <?php
                                // Don't ask about day limits at all if only 1 day con
                                if (CON_NUM_DAYS > 1) {
                                    for ($i = 1; $i <= CON_NUM_DAYS; $i++) {
                                        echo "<div class=\"form-group row\">\n";
                                        $D = longDayNameFromInt($i);
                                        echo "<label class=\"col-md-7 offset-md-1 col-form-label\" for=\"maxprogday$i\">$D maximum:</label>\n";
                                        $N = isset($partAvail["maxprogday$i"]) ? $partAvail["maxprogday$i"] : '';
                                        echo "<div class=\"col-md-3\"><input type=\"number\" min=\"0\" max=\"" . PREF_DLY_SESNS_LMT . "\" class=\"form-control\" id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=$N></div>\n";
                                        echo "</div>\n";
                                    }
                                }
                                ?>
                            </div>

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Please don't schedule me for a session that conflicts with:</label>
                                    <textarea class="form-control" name="preventconflict" id="preventconflict" rows=3><?php
                                        echo htmlspecialchars($partAvail["preventconflict"], ENT_NOQUOTES); ?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Other constraints or conflicts that we should know about?</label>
                                    <textarea class="form-control" name="otherconstraints" id="otherconstraints" rows=3><?php
                                        echo htmlspecialchars($partAvail["otherconstraints"], ENT_NOQUOTES); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button class="btn btn-primary" type=submit value="Update">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

    <!-- SCHEDULE availability times -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h2>Times I Am Available</h2>
            </div>
            <div class="card-body">
                <p><?php echo fetchCustomText("note_above_times"); ?></p>
                <p> For each day you will be attending <?php echo CON_NAME; ?>, please
                    indicate the times when you will be available as a program panelist.
                    Entering a single time for the whole con is fine. Splitting a day into
                    multiple time slots also is fine. Change all items in a row to blank to delete the row. 
                    Keep in mind we will be using this only as guidance when scheduling your sessions.</p>
                <?php
                    $query = "SELECT timeid, timedisplay, avail_start, avail_end FROM Times WHERE avail_start = 1 or avail_end = 1 ORDER BY display_order";
                    $result = mysqli_query_with_error_handling($query, true);
                    $times = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        $times[$row["timeid"]] = $row;
                    }
                ?>                
                <table class="table table-sm w-auto" id="availableTimesTable">
                    <thead>
                        <tr>
                            <?php if (CON_NUM_DAYS > 1) {
                                echo "<td>Start Day</td>\n";
                            } ?>
                            <td>Start Time</td>
                            <td> &nbsp;</td>
                            <?php if (CON_NUM_DAYS > 1) {
                                echo "<td>End Day</td>\n";
                            } ?>
                            <td>End Time</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php renderTable($partAvail); ?>
                    </tbody>
                    <tfoot>
                        <tr id="addRow">
                            <td>
                                <select class="form-control" id="addStartDay">
                                    <option value="-1" selected="selected"></option>
                                    <?php for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                                        echo "<option value=\"$j\">" . longDayNameFromInt($j) . "</option>\n";
                                    } ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="addStartTime">
                                    <option value="-1" selected="selected"></option>
                                    <?php foreach ($times as $time) {
                                        if ($time["avail_start"] == 1) {
                                            echo "<option value=\"" . $time["timeid"] . "\">" . $time["timedisplay"] . "</option>\n";
                                        }
                                    } ?>
                                </select>
                            </td>
                            <td class="text-center">-</td>
                            <td>
                                <select class="form-control" id="addEndDay">
                                    <option value="-1" selected="selected"></option>
                                    <?php for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                                        echo "<option value=\"$j\">" . longDayNameFromInt($j) . "</option>\n";
                                    } ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="addEndTime">
                                    <option value="-1" selected="selected"></option>
                                    <?php foreach ($times as $time) {
                                        if ($time["avail_end"] == 1) {
                                            echo "<option value=\"" . $time["timeid"] . "\">" . $time["timedisplay"] . "</option>\n";
                                        }
                                    } ?>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary" type="button" name="add" disabled="disabled" id="addAvailabilityBttn">Add</button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-danger" id="availabilityTableError"></td>
                        </tr>
                        <tr id="editRow" style="display: none;">
                            <td>
                                <select class="form-control" id="editStartDay">
                                    <?php for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                                        echo "<option value=\"$j\">" . longDayNameFromInt($j) . "</option>\n";
                                    } ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="editStartTime">
                                    <?php foreach ($times as $time) {
                                        if ($time["avail_start"] == 1) {
                                            echo "<option value=\"" . $time["timeid"] . "\">" . $time["timedisplay"] . "</option>\n";
                                        }
                                    } ?>
                                </select>
                            </td>
                            <td class="text-center">-</td>
                            <td>
                                <select class="form-control" id="editEndDay">
                                    <?php for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                                        echo "<option value=\"$j\">" . longDayNameFromInt($j) . "</option>\n";
                                    } ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" id="editEndTime">
                                    <?php foreach ($times as $time) {
                                        if ($time["avail_end"] == 1) {
                                            echo "<option value=\"" . $time["timeid"] . "\">" . $time["timedisplay"] . "</option>\n";
                                        }
                                    } ?>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary" type="button" name="update" disabled="disabled" id="updateAvailabilityBttn">Update</button>
                                <button class="btn btn-link" type="button" name="cancel" id="cancelAvailabilityBttn">Cancel</button>
                            </td>
                        </tr>
                        <tr id="editRowError" style="display: none;">
                            <td colspan="6" class="text-danger"></td>
                        </td>
                    </tfoot>
                </table>
                <?php echo fetchCustomText("note_after_times"); ?>
            </div>
        </div>
    </div>
</div>
<?php participant_footer(); ?>
