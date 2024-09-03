<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_GET['team_id'])) {
    $teamId = sanitizeInput($_GET['team_id']);

 
    $team_query = "SELECT * FROM teams WHERE id=$teamId";
    $team_result = $conn->query($team_query);
    if ($team_result->num_rows > 0) {
        $team_row = $team_result->fetch_assoc();
        $teamName = $team_row['team_name'];
        $coachName = $team_row['coach_name'];
    } else {
       
        header("Location: teams.php");
        exit();
    }

   
    $players_query = "SELECT * FROM players WHERE team_id=$teamId";
    $players_result = $conn->query($players_query);
} else {
    
    header("Location: teams.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Details - <?php echo isset($teamName) ? $teamName : ''; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        td[contenteditable='true'] {
            background-color: #fff;
            cursor: pointer;
        }

        #addRowButton {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Player Details - <?php echo isset($teamName) ? $teamName : ''; ?></h2>
        <h3>Team Information</h3>
        <p><strong>Team Name:</strong> <?php echo isset($teamName) ? $teamName : ''; ?></p>
        <p><strong>Coach:</strong> <?php echo isset($coachName) ? $coachName : ''; ?></p>

        <h3>Players</h3>
        <?php if (isset($players_result) && $players_result->num_rows > 0): ?>
            <table id="playerTable">
                <tr>
                    <th>Player Name</th>
                    <th>Description</th>
                    <th>Department</th>
                    <th>Semester</th>
                    <th>Section</th>
                    <th>Contact Details</th>
                    <th>Action</th>
                </tr>
                <?php while ($player_row = $players_result->fetch_assoc()): ?>
                    <tr>
                        <td contenteditable='true' class='editable playerName' data-player-id="<?php echo $player_row['id']; ?>"><?php echo $player_row['player_name']; ?></td>
                        <td contenteditable='true' class='editable description' data-player-id="<?php echo $player_row['id']; ?>"><?php echo $player_row['description']; ?></td>
                        <td contenteditable='true' class='editable department' data-player-id="<?php echo $player_row['id']; ?>"><?php echo $player_row['department']; ?></td>
                        <td contenteditable='true' class='editable semester' data-player-id="<?php echo $player_row['id']; ?>"><?php echo $player_row['semester']; ?></td>
                        <td contenteditable='true' class='editable section' data-player-id="<?php echo $player_row['id']; ?>"><?php echo $player_row['section']; ?></td>
                        <td contenteditable='true' class='editable contactDetails' data-player-id="<?php echo $player_row['id']; ?>"><?php echo $player_row['contact_details']; ?></td>
                        <td>
                            <button onclick="updatePlayer(<?php echo $player_row['id']; ?>)">Update</button>
                            <button onclick="deletePlayer(<?php echo $player_row['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No players found for <?php echo isset($teamName) ? $teamName : ''; ?></p>
        <?php endif; ?>

        <button id="addRowButton" onclick="addNewRow()">Add Player</button>
    </div>

    <?php include('includes/footer.php'); ?>

    <script>
        function updatePlayer(playerId) {
            var playerName = document.querySelector(`#playerTable .playerName[data-player-id='${playerId}']`).innerText;
            var description = document.querySelector(`#playerTable .description[data-player-id='${playerId}']`).innerText;
            var department = document.querySelector(`#playerTable .department[data-player-id='${playerId}']`).innerText;
            var semester = document.querySelector(`#playerTable .semester[data-player-id='${playerId}']`).innerText;
            var section = document.querySelector(`#playerTable .section[data-player-id='${playerId}']`).innerText;
            var contactDetails = document.querySelector(`#playerTable .contactDetails[data-player-id='${playerId}']`).innerText;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Player updated successfully!");
                }
            };
            xhr.open("POST", "edit_player.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("edit_player=1&player_id=" + playerId + "&new_player_name=" + playerName + "&new_description=" + description + "&new_department=" + department + "&new_semester=" + semester + "&new_section=" + section + "&new_contact_details=" + contactDetails);
        }

        function deletePlayer(playerId) {
            if (confirm("Are you sure you want to delete this player?")) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert("Player deleted successfully!");
                        location.reload();
                    }
                };
                xhr.open("POST", "delete_player.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send("delete_player=1&player_id=" + playerId);
            }
        }

        function addNewRow() {
            var table = document.getElementById("playerTable");
            if (!table) {
                table = document.createElement("table");
                table.id = "playerTable";
                table.innerHTML = `
                    <tr>
                        <th>Player Name</th>
                        <th>Description</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Section</th>
                        <th>Contact Details</th>
                        <th>Action</th>
                    </tr>
                `;
                document.querySelector(".container").appendChild(table);
            }

            var newRow = table.insertRow(-1);
            var cells = [];

            for (var i = 0; i < table.rows[0].cells.length - 1; i++) {
                cells.push(newRow.insertCell(i));
                cells[i].contentEditable = true;
                cells[i].setAttribute("class", "editable");
            }

            cells.push(newRow.insertCell(table.rows[0].cells.length - 1));
            cells[cells.length - 1].innerHTML = "<button onclick='saveNewPlayer(this.parentNode.parentNode)'>Save</button>";
        }

        function saveNewPlayer(newRow) {
            var playerName = newRow.cells[0].innerText;
            var description = newRow.cells[1].innerText;
            var department = newRow.cells[2].innerText;
            var semester = newRow.cells[3].innerText;
            var section = newRow.cells[4].innerText;
            var contactDetails = newRow.cells[5].innerText;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("New player added successfully!");
                    location.reload();
                }
            };
            xhr.open("POST", "add_player.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("add_player=1&player_name=" + playerName + "&description=" + description + "&department=" + department + "&semester=" + semester + "&section=" + section + "&contact_details=" + contactDetails + "&team_id=<?php echo $teamId; ?>");
        }
    </script>
</body>
</html>
