<?php
include_once './database/db_config.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'addEvent') {
        $eventName = sanitizeInput($_POST['eventName']);
        $eventDate = sanitizeInput($_POST['eventDate']);
        $eventDescription = sanitizeInput($_POST['eventDescription']);
        
        $sql = "INSERT INTO events (event_name, event_date, event_description) VALUES ('$eventName', '$eventDate', '$eventDescription')";
        if ($conn->query($sql) === TRUE) {
            echo "Event added successfully";
        } else {
            echo "Error adding event: " . $conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['action']) && $_GET['action'] == 'deleteEvent' && isset($_GET['eventId'])) {
        $eventId = $_GET['eventId'];
        
        $sql = "DELETE FROM events WHERE id='$eventId'";
        if ($conn->query($sql) === TRUE) {
            echo "Event deleted successfully";
        } else {
            echo "Error deleting event: " . $conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'updateEvent') {
        $eventId = $_POST['eventId'];
        $eventName = sanitizeInput($_POST['eventName']);
        $eventDate = sanitizeInput($_POST['eventDate']);
        $eventDescription = sanitizeInput($_POST['eventDescription']);
        
        $sql = "UPDATE events SET event_name='$eventName', event_date='$eventDate', event_description='$eventDescription' WHERE id='$eventId'";
        if ($conn->query($sql) === TRUE) {
            echo "Event updated successfully";
        } else {
            echo "Error updating event: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - College Sports Department</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Events</h2>
        <table id="eventsTable">
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Event Description</th>
                <th>Action</th>
            </tr>
            <?php
            $query = "SELECT * FROM events";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='event_".$row['id']."'>";
                    echo "<td contenteditable='true' id='eventName_".$row['id']."'>".$row['event_name']."</td>";
                    echo "<td contenteditable='true' id='eventDate_".$row['id']."'>".$row['event_date']."</td>";
                    echo "<td contenteditable='true' id='eventDescription_".$row['id']."'>".$row['event_description']."</td>";
                    echo "<td><button onclick='updateEvent(".$row['id'].")'>Update</button> <button onclick='deleteEvent(".$row['id'].")'>Delete</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No events found</td></tr>";
            }
            ?>
        </table>
        <button onclick="addNewRow('eventsTable')">Add Event</button>
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
            cell1.innerHTML = "<input type='text' name='eventName'>";
            cell2.innerHTML = "<input type='date' name='eventDate'>";
            cell3.innerHTML = "<textarea name='eventDescription'></textarea>";
            cell4.innerHTML = "<button onclick='saveNewEvent(this)'>Save</button>";
        }

        function saveNewEvent(button) {
            var row = button.parentNode.parentNode;
            var eventName = row.cells[0].getElementsByTagName("input")[0].value;
            var eventDate = row.cells[1].getElementsByTagName("input")[0].value;
            var eventDescription = row.cells[2].getElementsByTagName("textarea")[0].value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "events.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    location.reload(); 
                }
            };
            xhr.send("action=addEvent&eventName=" + eventName + "&eventDate=" + eventDate + "&eventDescription=" + eventDescription);
        }

        function updateEvent(eventId) {
            var eventName = document.getElementById("eventName_" + eventId).innerText;
            var eventDate = document.getElementById("eventDate_" + eventId).innerText;
            var eventDescription = document.getElementById("eventDescription_" + eventId).innerText;

            if (eventName && eventDate && eventDescription) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "events.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };

                xhr.send("action=updateEvent&eventId=" + eventId + "&eventName=" + eventName + "&eventDate=" + eventDate + "&eventDescription=" + eventDescription);
            }
        }

        function deleteEvent(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "events.php?action=deleteEvent&eventId=" + eventId, true);
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
