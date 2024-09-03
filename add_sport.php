<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['add_sport'])) {
    $newSportName = sanitizeInput($_POST['new_sport_name']);
    $response = "";

    $add_sport_query = "INSERT INTO teams (sport) VALUES ('$newSportName')";
    if ($conn->query($add_sport_query) === TRUE) {
        $response = "New sport added successfully!";
    } else {
        $response = "Error adding sport: " . $conn->error;
    }

    echo json_encode(array("message" => $response));
}
?>
