<?php
include_once 'budget.php';

if (isset($_POST['eventMatch'], $_POST['eventType'])) {
    $eventMatchId = $_POST['eventMatch'];
    $eventType = $_POST['eventType'];

    $output = fetchBudgets($eventMatchId, $eventType);
    echo $output;
} else {
    echo "Error: Event/match ID or event type not provided";
}
?>
