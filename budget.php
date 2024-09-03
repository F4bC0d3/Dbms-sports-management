<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function fetchEventsMatches() {
    global $conn;
    $output = "";

    $events_query = "SELECT id, CONCAT(event_name, ' - Event') AS name, event_date FROM events";
    $matches_query = "SELECT match_id, CONCAT(sport_name, ' Match on ', match_date) AS name, match_date FROM matches";

    $events_result = $conn->query($events_query);
    $matches_result = $conn->query($matches_query);

    if ($events_result->num_rows > 0 || $matches_result->num_rows > 0) {
        $output .= "<h3>Select Event/Match:</h3>";
        $output .= "<select id='eventMatchSelect' name='eventMatch' onchange='getBudgets()'>";
        $output .= "<option value='' selected>Select Event/Match</option>";
        
        if ($events_result->num_rows > 0) {
            while ($row = $events_result->fetch_assoc()) {
                $output .= "<option value='" . $row['id'] . "' data-type='event'>" . $row['name'] . " - " . $row['event_date'] . "</option>";
            }
        }

        if ($matches_result->num_rows > 0) {
            while ($row = $matches_result->fetch_assoc()) {
                $output .= "<option value='" . $row['match_id'] . "' data-type='match'>" . $row['name'] . " - " . $row['match_date'] . "</option>";
            }
        }

        $output .= "</select>";
    } else {
        $output .= "<p>No events or matches found.</p>";
    }
    
    return $output;
}

function fetchBudgets($eventMatchId, $eventType) {
    global $conn;
    $output = "";

    $idColumn = ($eventType === 'event') ? 'event_id' : 'match_id';

    $budget_query = "SELECT * FROM budget WHERE $idColumn='$eventMatchId'";
    $budget_result = $conn->query($budget_query);

    if ($budget_result->num_rows > 0) {
        $output .= "<h3>Budget Information:</h3>";
        $output .= "<table id='budgetTable'>";
        $output .= "<tr><th>Event/Match</th><th>Budget Allocation</th><th>Description</th><th>Action</th></tr>";
        while ($row = $budget_result->fetch_assoc()) {
            $output .= "<tr>";
            $output .= "<td>" . getEventMatchName($row[$idColumn]) . "</td>";
            $output .= "<td contenteditable='true' class='budget' id='budget_".$row['budget_id']."'>".$row['allocation']."</td>";
            $output .= "<td contenteditable='true' class='description' id='description_".$row['budget_id']."'>".$row['description']."</td>";
            $output .= "<td><button onclick='updateBudget(".$row['budget_id'].")'>Update</button> <button onclick='deleteBudget(".$row['budget_id'].")'>Delete</button></td>";
            $output .= "</tr>";
        }
        $output .= "</table>";
    } else {
        $output .= "";
    }
    
    return $output;
}

function getEventMatchName($eventMatchId) {
    global $conn;
    $query = "SELECT CONCAT(event_name, ' - Event') AS name FROM events WHERE id='$eventMatchId' UNION SELECT CONCAT(sport_name, ' Match on ', match_date) AS name FROM matches WHERE match_id='$eventMatchId'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Unknown";
    }
}
?>
