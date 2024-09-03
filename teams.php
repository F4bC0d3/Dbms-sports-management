<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function fetchTeams($sport) {
    global $conn;
    $sport = sanitizeInput($sport);
    $output = "";

    $teams_query = "SELECT * FROM teams WHERE sport='$sport'";
    $teams_result = $conn->query($teams_query);

    if ($teams_result->num_rows > 0) {
        $output .= "<h3>Teams for $sport:</h3>";
        $output .= "<table>";
        $output .= "<tr><th>Team Name</th><th>Coach</th><th>Players</th><th>Action</th></tr>";
        while ($team_row = $teams_result->fetch_assoc()) {
            $teamId = $team_row['id'];
            $output .= "<tr>";
            $output .= "<td contenteditable='true' id='teamName_".$teamId."'>".$team_row['team_name']."</td>";
            $output .= "<td contenteditable='true' id='coach_".$teamId."'>".$team_row['coach_name']."</td>";
            $output .= "<td><a href='player_details.php?team_id=".$teamId."'>View Players</a></td>";
            $output .= "<td><button onclick='updateTeam($teamId)'>Update</button> <button onclick='deleteTeam($teamId)'>Delete</button></td>";
            $output .= "</tr>";
        }
        $output .= "</table>";
    } else {
        $output .= "No teams found for $sport";
    }
    return $output;
}

if(isset($_POST['add_team'])) {
    $teamName = sanitizeInput($_POST['team_name']);
    $coachName = sanitizeInput($_POST['coach_name']);
    $sport = sanitizeInput($_POST['sport']);

    $add_team_query = "INSERT INTO teams (team_name, coach_name, sport) VALUES ('$teamName', '$coachName', '$sport')";
    if($conn->query($add_team_query) === TRUE) {
        header("Location: ".$_SERVER['PHP_SELF']."?sport=".$sport);
        exit();
    } else {
        echo "Error adding team: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams - College Sports Department</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>]
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Teams</h2>
        <form id="sportForm" method="GET">
            <label for="sportSelect">Choose a sport:</label>
            <select id="sportSelect" name="sport" onchange="this.form.submit()">
                <option value="">Select Sport</option>
                <?php
                $sports_query = "SELECT DISTINCT sport FROM teams";
                $sports_result = $conn->query($sports_query);

                if ($sports_result->num_rows > 0) {
                    while ($row = $sports_result->fetch_assoc()) {
                        $sport = $row['sport'];
                        $selected = (isset($_GET['sport']) && $_GET['sport'] == $sport) ? "selected" : "";
                        echo "<option value='$sport' $selected>$sport</option>";
                    }
                }
                ?>
            </select>
        </form>

        <?php
        if (isset($_GET['sport'])) {
            $selected_sport = sanitizeInput($_GET['sport']);
            echo fetchTeams($selected_sport);
        }
        ?>

        <button onclick="toggleNewTeamForm()">Add New Team</button>
        
        <form id="addTeamForm" method="POST" style="display:none;">
            <input type="text" name="team_name" placeholder="Team Name"><br>
            <input type="text" name="coach_name" placeholder="Coach Name"><br>
            <input type="text" name="sport" placeholder="Sport Name"><br>
            <input type="submit" name="add_team" value="Add Team">
        </form>
    </div>

    <?php include('includes/footer.php'); ?>

    <script>
        function toggleNewTeamForm() {
            var form = document.getElementById("addTeamForm");
            if (form.style.display === "none") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
        <!-- JavaScript for updating and deleting teams -->
    function updateTeam(teamId) {
        var teamName = document.getElementById("teamName_" + teamId).innerText;
        var coachName = document.getElementById("coach_" + teamId).innerText;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
            }
        };
        xhr.open("POST", "update_team.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("update_team=1&team_id=" + teamId + "&team_name=" + teamName + "&coach_name=" + coachName);
    }

    function deleteTeam(teamId) {
        if (confirm("Are you sure you want to delete this team?")) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText);
                    location.reload();
                }
            };
            xhr.open("POST", "delete_team.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("delete_team=1&team_id=" + teamId);
        }
    }
</script>

</body>
</html>
