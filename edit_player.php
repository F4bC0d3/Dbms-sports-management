<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['edit_player'])) {
        $playerId = sanitizeInput($_POST['player_id']);
        $newPlayerName = sanitizeInput($_POST['new_player_name']);
        $newdescription = sanitizeInput($_POST['new_description']);
        $newDepartment = sanitizeInput($_POST['new_department']);
        $newSemester = sanitizeInput($_POST['new_semester']);
        $newSection = sanitizeInput($_POST['new_section']);
        $newContactDetails = sanitizeInput($_POST['new_contact_details']);

        $update_player_query = "UPDATE players SET player_name='$newPlayerName', description='$newdescription', department='$newDepartment', semester='$newSemester', section='$newSection', contact_details='$newContactDetails' WHERE id=$playerId";
        if ($conn->query($update_player_query) === TRUE) {
            $update_player_count_query = "UPDATE teams SET player_count = player_count + 1 WHERE id = '$teamId'";
            $conn->query($update_player_count_query);
            echo "Player updated successfully!";
        } else {
            echo "Error updating player details: " . $conn->error;
        }
    }
}
?>
