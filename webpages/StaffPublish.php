<?php

    $title="Staff - Export Schedule";
    require_once('db_functions.php');
    require_once('error_functions.php');
    require_once('render_functions.php');
    require_once('StaffCommonCode.php');
    require_once('konOpas_func.php');
    require_once('StaffPublish_FNC.php');

    staff_header($title, true);
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="alert alert-warning">
                These changes <em>must</em> be manually copied over to RingCentral Events. Do not publish without first copying the changes over. This can be done by the tech team.
            </div>
        </div>
        <div class="card-body">
<?php


    $cur_schedule = retrieveKonOpasData(2, 1);

    $new_program = json_decode($cur_schedule["program"]);
    $new_people = json_decode($cur_schedule["people"]);

    $pub_sched_id = md5(json_encode($new_program) . json_encode($new_people));
    $changes = get_diff($new_program, $new_people);

    if (empty($changes)) {
        echo "<p>No unpublished changes</p>";
    } else {
        $query = "INSERT IGNORE INTO PublishedSchedule(pub_sched_id, program_json, people_json) VALUES(?, ?, ?)";
        mysqli_query_with_prepare_and_exit_on_error($query, "sss", array($pub_sched_id, json_encode($new_program, JSON_THROW_ON_ERROR), json_encode($new_people, JSON_THROW_ON_ERROR)));

        foreach ($changes as $change) {
            if ($change["type"] == "new") {
?>
            <div class="card p-2 mb-2">
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Action</div>
                    <div class="col">New item</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Title</div>
                    <div class="col"><?php echo $change["new"]->title; ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Tags</div>
                    <div class="col"><?php echo implode(', ', $change["new"]->tags); ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Date</div>
                    <div class="col"><?php echo date('l', strtotime($change["new"]->date)); ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Time</div>
                    <div class="col"><?php echo $change["new"]->time; ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Duration (mins)</div>
                    <div class="col"><?php echo $change["new"]->mins; ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Room</div>
                    <div class="col"><?php echo $change["new"]->loc[0]; ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">People</div>
                    <div class="col">
                        <ul>
                            <?php
                            if ($change["new"]->people != null) {
                                foreach ($change["new"]->people as $person) {
                                    echo "<li>$person->name</li>";
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Description</div>
                    <div class="col"><?php echo htmlspecialchars($change["new"]->desc); ?></div>
                </div>
            </div>
<?php
            } else if ($change["type"] == "change") {
                $edits = [];
                if ($change["old"]->title != $change["new"]->title) {
                    $edits[] = ["Title", $change["old"]->title, $change["new"]->title];
                }
                if ($change["old"]->tags != $change["new"]->tags) {
                    $edits[] = ["Tags", implode(', ', $change["old"]->tags), implode(', ', $change["new"]->tags)];
                }
                if ($change["old"]->date != $change["new"]->date) {
                    $edits[] = ["Date", date('l', strtotime($change["old"]->date)), date('l', strtotime($change["new"]->date))];
                }
                if ($change["old"]->time != $change["new"]->time) {
                    $edits[] = ["Time", $change["old"]->time, $change["new"]->time];
                }
                if ($change["old"]->mins != $change["new"]->mins) {
                    $edits[] = ["Duration (mins)", $change["old"]->mins, $change["new"]->mins];
                }
                if ($change["old"]->loc != $change["new"]->loc) {
                    $edits[] = ["Room", $change["old"]["loc"][0], $change["new"]->loc[0]];
                }
                if ($change["old"]->people != $change["new"]->people) {
                    $old_people = [];
                    foreach ($change["old"]->people as $person) {
                        $old_people[] = $person->name;
                    }
                    $new_people = [];
                    foreach ($change["new"]->people as $person) {
                        $new_people[] = $person->name;
                    }
                    $edits[] = ["People", implode(', ', $old_people), implode(', ', $new_people)];
                }
                if ($change["old"]->desc != $change["new"]->desc) {
                    $edits[] = ["Description", htmlspecialchars($change["old"]->desc), htmlspecialchars($change["new"]->desc)];
                }
?>
            <div class="card p-2 mb-2">
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Action</div>
                    <div class="col">Updated item</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Title (old)</div>
                    <div class="col"><?php echo $change["old"]->title; ?></div>
                </div>
                <?php
                foreach ($edits as $edit) {
                ?>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold"><?php echo $edit[0]; ?></div>
                    <div class="col"><?php echo $edit[1]; ?></div>
                    <div class="col-sm-1"> -&gt; </div>
                    <div class="col"><?php echo $edit[2]; ?></div>
                </div>
                <?php
                }
                ?>
            </div>
<?php
            } else if ($change["type"] == "delete") {
?>
            <div class="card p-2 mb-2">
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Action</div>
                    <div class="col">Cancelled item</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Title</div>
                    <div class="col"><?php echo $change["old"]->title; ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Date</div>
                    <div class="col"><?php echo date('l', strtotime($change["old"]->date)); ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 font-weight-bold">Time</div>
                    <div class="col"><?php echo $change["old"]->time; ?></div>
                </div>
            </div>
<?php
            } else if ($change["type"] == "change_people") {
    ?>
                <div class="card p-2 mb-2">
                    <div class="row">
                        <div class="col-sm-2 font-weight-bold">Action</div>
                        <div class="col">People changed</div>
                    </div>
                </div>
    <?php
                }        }
    }
    ?>
        </div>
        <div class="card-footer">
            <div id="publishForm">
                <!-- TODO: ERROR handling -->
                <form hx-post="StaffPublish_POST.php" hx-target="#publishForm">
                    <input type="hidden" name="pub_sched_id" value="<?php echo $pub_sched_id; ?>">
                    <button type="submit" class="btn btn-primary">Publish<?php echo count($changes) == 0 ? " anyway" : ""; ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php


staff_footer();

?>
