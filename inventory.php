<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function getPlayersDropdown($selectedPlayerId, $equipmentId) {
    global $conn;
    $output = "<select id='player_id".$equipmentId."' name='playerId'>";
    $query = "SELECT id, player_name FROM players";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['id'] == $selectedPlayerId) {
                $output .= "<option value='" . $row['id'] . "' selected>" . $row['player_name'] . "</option>";
            } else {
                $output .= "<option value='" . $row['id'] . "'>" . $row['player_name'] . "</option>";
            }
        }
    } else {
        $output .= "<option value=''>No players found</option>";
    }

    $output .= "</select>";
    return $output;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'addEquipment') {
        $equipment = sanitizeInput($_POST['equipment']);
        $sport = sanitizeInput($_POST['sport']);
        $playerId = sanitizeInput($_POST['playerId']);
        $quantity = sanitizeInput($_POST['quantity']);
        $description = sanitizeInput($_POST['description']);
        
        $sql = "INSERT INTO equipment (equipment_name, sport, player_id, quantity, description) VALUES ('$equipment', '$sport', '$playerId', '$quantity', '$description')";
        if ($conn->query($sql) === TRUE) {
            echo "Equipment added successfully";
        } else {
            echo "Error adding equipment: " . $conn->error;
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['action']) && $_GET['action'] == 'deleteEquipment' && isset($_GET['equipmentId'])) {
        $equipmentId = $_GET['equipmentId'];
        
        $sql = "DELETE FROM equipment WHERE id='$equipmentId'";
        if ($conn->query($sql) === TRUE) {
            echo "Equipment deleted successfully";
        } else {
            echo "Error deleting equipment: " . $conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'updateEquipment') {
        $equipmentId = $_POST['equipmentId'];
        $equipment = sanitizeInput($_POST['equipment']);
        $sport = sanitizeInput($_POST['sport']);
        $playerId = sanitizeInput($_POST['playerId']);
        $quantity = sanitizeInput($_POST['quantity']);
        $description = sanitizeInput($_POST['description']);
        
        $sql = "UPDATE equipment SET equipment_name='$equipment', sport='$sport', player_id='$playerId', quantity='$quantity', description='$description' WHERE id='$equipmentId'";
        if ($conn->query($sql) === TRUE) {
            echo "Equipment updated successfully";
        } else {
            echo "Error updating equipment: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Inventory - College Sports Department</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="container">
        <h2>Equipment Inventory</h2>
        <table id="equipmentTable">
            <tr>
                <th>Equipment</th>
                <th>Sport</th>
                <th>Player</th>
                <th>Quantity</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php
            $query = "SELECT * FROM equipment";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td contenteditable='true' id='equipment_".$row['id']."'>".$row['equipment_name']."</td>";
                    echo "<td contenteditable='true' id='sport_".$row['id']."'>".$row['sport']."</td>";
                    echo "<td id='playerCell_".$row['id']."'>" . getPlayersDropdown($row['player_id'], $row['id']) . "</td>";
                    echo "<td contenteditable='true' id='quantity_".$row['id']."'>".$row['quantity']."</td>";
                    echo "<td contenteditable='true' id='description_".$row['id']."'>".$row['description']."</td>";
                    echo "<td><button onclick='updateEquipment(".$row['id'].")'>Update</button> <button onclick='deleteEquipment(".$row['id'].")'>Delete</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No equipment found</td></tr>";
            }
            ?>
        </table>
        <button onclick="addNewRow('equipmentTable')">Add Equipment</button>
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
            cell1.innerHTML = "<input type='text' name='equipment'>";
            cell2.innerHTML = "<input type='text' name='sport'>";
            cell3.innerHTML = "<?php echo getPlayersDropdown('', ''); ?>";
            cell4.innerHTML = "<input type='number' name='quantity'>";
            cell5.innerHTML = "<input type='text' name='description'>";
            cell6.innerHTML = "<button onclick='saveNewEquipment(this)'>Save</button>";
        }

        function saveNewEquipment(button) {
            var row = button.parentNode.parentNode;
            var equipment = row.cells[0].getElementsByTagName("input")[0].value;
            var sport = row.cells[1].getElementsByTagName("input")[0].value;
            var playerId = row.cells[2].getElementsByTagName("select")[0].value;
            var quantity = row.cells[3].getElementsByTagName("input")[0].value;
            var description = row.cells[4].getElementsByTagName("input")[0].value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "inventory.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    location.reload(); 
                }
            };
            xhr.send("action=addEquipment&equipment=" + equipment + "&sport=" + sport + "&playerId=" + playerId + "&quantity=" + quantity + "&description=" + description);
        }

        function updateEquipment(equipmentId) {
            var equipment = document.getElementById("equipment_"+equipmentId).innerText;
            var sport = document.getElementById("sport_"+equipmentId).innerText;
            var playerId = document.getElementById("playerCell_"+equipmentId).querySelector("select").value;
            var quantity = document.getElementById("quantity_"+equipmentId).innerText;
            var description = document.getElementById("description_"+equipmentId).innerText;

            if (sport && playerId && quantity && description) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "inventory.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                xhr.send("action=updateEquipment&equipmentId=" + equipmentId + "&equipment=" + equipment + "&sport=" + sport + "&playerId=" + playerId + "&quantity=" + quantity + "&description=" + description);
            }
        }

        function deleteEquipment(equipmentId) {
            if (confirm("Are you sure you want to delete this equipment?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "inventory.php?action=deleteEquipment&equipmentId=" + equipmentId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                xhr.send();
            }
        }
    </script>
</body>
</html>
