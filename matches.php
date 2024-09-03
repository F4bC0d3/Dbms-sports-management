<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function getTeamsDropdown($selectedTeamId, $eventId, $teamNumber) {
    global $conn;
    $output = "<select id='teamId_".$eventId."_".$teamNumber."' name='teamId'>";
    $query = "SELECT id, team_name FROM teams";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['id'] == $selectedTeamId) {
                $output .= "<option value='" . $row['id'] . "' selected>" . $row['team_name'] . "</option>";
            } else {
                $output .= "<option value='" . $row['id'] . "'>" . $row['team_name'] . "</option>";
            }
        }
    } else {
        $output .= "<option value=''>No teams found</option>";
    }

    $output .= "</select>";
    return $output;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'addMatch') {
        $matchDate = sanitizeInput($_POST['matchDate']);
        $sportName = sanitizeInput($_POST['sportName']);
        $team1Id = sanitizeInput($_POST['team1Id']);
        $team2Id = sanitizeInput($_POST['team2Id']);
        $venue = sanitizeInput($_POST['venue']);

        $sql = "INSERT INTO matches (match_date, sport_name, team1_id, team2_id, venue) VALUES ('$matchDate', '$sportName', '$team1Id', '$team2Id', '$venue')";
        if ($conn->query($sql) === TRUE) {
            echo "Match added successfully";
        } else {
            echo "Error adding match: " . $conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['action']) && $_GET['action'] == 'deleteMatch' && isset($_GET['matchId'])) {
        $matchId = $_GET['matchId'];

        $sql = "DELETE FROM matches WHERE match_id='$matchId'";
        if ($conn->query($sql) === TRUE) {
            echo "Match deleted successfully";
        } else {
            echo "Error deleting match: " . $conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'updateMatch') {
        $matchId = $_POST['matchId'];
        $matchDate = sanitizeInput($_POST['matchDate']);
        $sportName = sanitizeInput($_POST['sportName']);
        $team1Id = sanitizeInput($_POST['team1Id']);
        $team2Id = sanitizeInput($_POST['team2Id']);
        $venue = sanitizeInput($_POST['venue']);
        
        $sql = "UPDATE matches SET match_date='$matchDate', sport_name='$sportName', team1_id='$team1Id', team2_id='$team2Id', venue='$venue' WHERE match_id='$matchId'";
        if ($conn->query($sql) === TRUE) {
            echo "Match updated successfully";
        } else {
            echo "Error updating match: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches - College Sports Department</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Matches</h2>
        <table id="matchesTable">
            <tr>
                <th>Match Date</th>
                <th>Sport</th>
                <th>Team 1</th>
                <th>Team 2</th>
                <th>Venue</th>
                <th>Action</th>
            </tr>
            <?php
            $query = "SELECT m.match_id, m.match_date, m.sport_name, m.team1_id, m.team2_id, m.venue, t1.team_name AS team1_name, t2.team_name AS team2_name
                      FROM matches m
                      INNER JOIN teams t1 ON m.team1_id = t1.id
                      INNER JOIN teams t2 ON m.team2_id = t2.id";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td contenteditable='true' id='matchDate_".$row['match_id']."'>".$row['match_date']."</td>";
                    echo "<td contenteditable='true' id='sportName_".$row['match_id']."'>".$row['sport_name']."</td>";
                    echo "<td id='team1Cell_".$row['match_id']."' onclick='showDropdown(event, ".$row['match_id'].", 1)'>" . getTeamsDropdown($row['team1_id'], $row['match_id'], 1) . "</td>";
                    echo "<td id='team2Cell_".$row['match_id']."' onclick='showDropdown(event, ".$row['match_id'].", 2)'>" . getTeamsDropdown($row['team2_id'], $row['match_id'], 2) . "</td>";
                    echo "<td contenteditable='true' id='venue_".$row['match_id']."'>".$row['venue']."</td>";
                    echo "<td><button onclick='updateMatch(".$row['match_id'].")'>Update</button> <button onclick='deleteMatch(".$row['match_id'].")'>Delete</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No matches found</td></tr>";
            }
            ?>
        </table>
        <button onclick="addNewRow('matchesTable')">Add Match</button>
    </div>

    <?php include('includes/footer.php'); ?>

    <script>
        function addNewRow(tableId) {
            var table = document.getElementById(tableId);
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            cell1.innerHTML = "<input type='date' name='matchDate'>";
            cell2.innerHTML = "<input type='text' name='sportName'>";
            cell3.innerHTML = "<?php echo getTeamsDropdown('', '', 1); ?>";
            cell4.innerHTML = "<?php echo getTeamsDropdown('', '', 2); ?>";
            cell5.innerHTML = "<input type='text' name='venue'>";
            cell6.innerHTML = "<button onclick='saveNewMatch(this)'>Save</button>";
        }

        function saveNewMatch(button) {
            var row = button.parentNode.parentNode;
            var matchDate = row.cells[0].getElementsByTagName("input")[0].value;
            var sportName = row.cells[1].getElementsByTagName("input")[0].value;
            var team1Id = row.cells[2].getElementsByTagName("select")[0].value;
            var team2Id = row.cells[3].getElementsByTagName("select")[0].value;
            var venue = row.cells[4].getElementsByTagName("input")[0].value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "matches.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    location.reload();
                }
            };
            var params = "action=addMatch&matchDate=" + encodeURIComponent(matchDate) + "&sportName=" + encodeURIComponent(sportName) + "&team1Id=" + encodeURIComponent(team1Id) + "&team2Id=" + encodeURIComponent(team2Id) + "&venue=" + encodeURIComponent(venue);
            xhr.send(params);
        }

        function updateMatch(matchId) {
            var matchDate = document.getElementById("matchDate_"+matchId).innerText;
            var sportName = document.getElementById("sportName_"+matchId).innerText;
            var team1Id = document.getElementById("team1Cell_"+matchId).querySelector("select").value;
            var team2Id = document.getElementById("team2Cell_"+matchId).querySelector("select").value;
            var venue = document.getElementById("venue_"+matchId).innerText;

            if (matchDate && sportName && team1Id && team2Id && venue) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "matches.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                var params = "action=updateMatch&matchId=" + matchId + "&matchDate=" + encodeURIComponent(matchDate) + "&sportName=" + encodeURIComponent(sportName) + "&team1Id=" + encodeURIComponent(team1Id) + "&team2Id=" + encodeURIComponent(team2Id) + "&venue=" + encodeURIComponent(venue);
                xhr.send(params);
            }
        }

        function deleteMatch(matchId) {
            if (confirm("Are you sure you want to delete this match?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "matches.php?action=deleteMatch&matchId=" + matchId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                xhr.send();
            }
        }


        function selectTeam(teamId) {
            var matchId = document.querySelector(".team-dropdown").dataset.matchId;
            var teamNumber = document.querySelector(".team-dropdown").dataset.teamNumber;
            var cell = document.getElementById("team"+teamNumber+"Cell_"+matchId);
            var dropdown = document.getElementById("teamDropdown_"+matchId+"_"+teamNumber);
            cell.innerHTML = getTeamsDropdown(teamId, matchId, teamNumber);
            dropdown.style.display = "none";
            window.removeEventListener("click", hideDropdown);
        }
    </script>
</body>
</html>
