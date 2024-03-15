<?php

require_once('konOpas_func.php');

$offset = $_GET["offset"] ?: 0;
$room = $_GET["room"];
$future_only = $_GET["future_only"] == "true" ?: false;
$virtual_only = $_GET["virtual_only"] == "true" ?: false;

if (!$room) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "error" => "No room specified"
    ]);
    return;
}

// TODO: Load from the published version instead
$cur_schedule = retrieveKonOpasData(2, 1);
$program = json_decode($cur_schedule["program"]);

$now = strtotime('+' . $offset . ' hours');

$matches = array();
foreach ($program as $item) {
    $item_end_date_time = strtotime($item->date . ' ' . $item->time . '+' . $item->mins . ' minutes');
    $should_add = $item->loc[0] == $room && (!$future_only || $item_end_date_time >= $now);
    if ($should_add) {
        if ($virtual_only) {
            // If any of the tags are Tag:In person only, then skip
            foreach ($item->tags as $tag) {
                if ($tag == "Tag:In person only") {
                    $should_add = false;
                    break;
                }
            }
        }
        if ($should_add) {
            $matches[] = $item;
        }
    }
}

usort($matches, function($a, $b) {
    return strtotime($a->date . ' ' . $a->time) - strtotime($b->date . ' ' . $b->time);
});

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "room" => $room,
    "now" => date('Y-m-d H:i', $now),
    "program" => $matches
]);

?>