<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if(isset($_POST['update_team'])) {
    $teamId = sanitizeInput($_POST['team_id']);
    $teamName = sanitizeInput($_POST['team_name']);
    $coachName = sanitizeInput($_POST['coach_name']);

    $update_team_query = "UPDATE teams SET team_name='$teamName', coach_name='$coachName' WHERE id=$teamId";
    if($conn->query($update_team_query) === TRUE) {
        echo "Team details updated successfully!";
    } else {
        echo "Error updating team details: " . $conn->error;
    }
}
?>
