<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_player'])) {
        $playerId = sanitizeInput($_POST['player_id']);

        $delete_player_query = "DELETE FROM players WHERE id=$playerId";
        if ($conn->query($delete_player_query) === TRUE) {
            echo "Player deleted successfully!";
            $update_player_count_query = "UPDATE teams SET player_count = player_count + 1 WHERE id = '$teamId'";
            $conn->query($update_player_count_query);
        } else {
            echo "Error deleting player: " . $conn->error;
        }
    }
}
?>
