<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['submit'])) {
    $eventMatchId = sanitizeInput($_POST['eventMatch']);
    $budgetAllocation = sanitizeInput($_POST['budgetAllocation']);
    $description = sanitizeInput($_POST['description']);

    $eventQuery = "SELECT COUNT(*) AS count FROM events WHERE id='$eventMatchId'";
    $matchQuery = "SELECT COUNT(*) AS count FROM matches WHERE match_id='$eventMatchId'";
    
    $eventResult = $conn->query($eventQuery);
    $matchResult = $conn->query($matchQuery);
    
    $roweCount = 0;
    $rowmCount = 0;

    if ($eventResult->num_rows > 0) {
        $row = $eventResult->fetch_assoc();
        $roweCount = $row['count'];
    }

    if ($matchResult->num_rows > 0) {
        $row = $matchResult->fetch_assoc();
        $rowmCount = $row['count'];
    }

    if ($roweCount > 0) {
        $insertQuery = "INSERT INTO budget (event_id, allocation, description) VALUES ('$eventMatchId', '$budgetAllocation', '$description')";
    } elseif ($rowmCount > 0) {
        $insertQuery = "INSERT INTO budget (match_id, allocation, description) VALUES ('$eventMatchId', '$budgetAllocation', '$description')";
    }

    if (!empty($insertQuery)) {
        if ($conn->query($insertQuery) === TRUE) {
            echo "Budget added successfully!";
        } else {
            echo "Error adding budget: " . $conn->error;
        }
    } else {
        echo "Invalid event or match ID!";
    }
}
?>
