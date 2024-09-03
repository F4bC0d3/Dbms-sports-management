<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_player'])) {
        $teamId = sanitizeInput($_POST['team_id']);
        $playerName = sanitizeInput($_POST['player_name']);
        $description = sanitizeInput($_POST['description']);
        $department = sanitizeInput($_POST['department']);
        $semester = sanitizeInput($_POST['semester']);
        $section = sanitizeInput($_POST['section']);
        $contactDetails = sanitizeInput($_POST['contact_details']);

        $add_player_query = "INSERT INTO players (team_id, player_name, description, department, semester, section, contact_details) VALUES ('$teamId', '$playerName', '$description', '$department', '$semester', '$section', '$contactDetails')";
        $update_player_count_query = "UPDATE teams SET player_count = player_count + 1 WHERE id = '$teamId'";
            $conn->query($update_player_count_query);
    }
    if ($conn->query($add_player_query) === TRUE) {
            echo "New player added successfully!";
        } else {
            echo "Error adding player: " . $conn->error;
        }
}
?>