<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if(isset($_POST['delete_team'])) {
    $teamId = sanitizeInput($_POST['team_id']);

    $delete_team_query = "DELETE FROM teams WHERE id=$teamId";
    if($conn->query($delete_team_query) === TRUE) {
        $delete_players_query = "DELETE FROM players WHERE team_id=$teamId";
        if($conn->query($delete_players_query) === TRUE) {
            echo "Team and associated players deleted successfully!";
        } else {
            echo "Error deleting players: " . $conn->error;
        }
    } else {
        echo "Error deleting team: " . $conn->error;
    }
}
?>
