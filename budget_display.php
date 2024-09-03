<?php
include_once 'budget.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management - College Sports Department</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2>Budget Management</h2>
        <div id="eventMatchDropdown">
            <?php echo fetchEventsMatches(); ?>
        </div>
        
        <div id="budgetInfo">
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        fetchEventsMatches();
    });

    function fetchEventsMatches() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById("eventMatchDropdown").innerHTML = xhr.responseText;
                getBudgets();
            }
        };
        xhr.open("GET", "fetch_events_matches.php", true);
        xhr.send();
    }

    function getBudgets() {
    var eventMatchId = document.getElementById("eventMatchSelect").value;
    var eventType = document.getElementById("eventMatchSelect").options[document.getElementById("eventMatchSelect").selectedIndex].getAttribute('data-type');
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var budgetInfo = document.getElementById("budgetInfo");
            if (xhr.responseText.trim() === '') {
                var message = "<p>No budget information found for selected Event/Match.</p>";
                var table = "<table id='budgetTable'><tr><th>Event/Match</th><th>Budget Allocation</th><th>Description</th><th>Action</th></tr></table>";
                budgetInfo.innerHTML = message + table;
            } else {
                budgetInfo.innerHTML = xhr.responseText;
            }
            appendAddRowButton();
        }
    };
    xhr.open("POST", "fetch_budgets.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("eventMatch=" + eventMatchId + "&eventType=" + eventType);
}



    function updateBudget(budgetId) {
        var newBudgetAllocation = document.getElementById("budget_"+budgetId).innerText;
        var newDescription = document.getElementById("description_"+budgetId).innerText;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
            }
        };
        xhr.open("POST", "update_budget.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("updateBudget=1&budgetId=" + budgetId + "&newBudgetAllocation=" + newBudgetAllocation + "&newDescription=" + newDescription);
    }

    function deleteBudget(budgetId) {
        if (confirm("Are you sure you want to delete this budget?")) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText);
                    getBudgets();
                }
            };
            xhr.open("POST", "delete_budget.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("deleteBudget=1&budgetId=" + budgetId);
        }
    }

    function addRow() {
        var table = document.getElementById("budgetTable");
        var lastRow = table.rows.length;
        var newRow = table.insertRow(lastRow);
        var cells = [];

        cells.push(newRow.insertCell(0));
        cells.push(newRow.insertCell(1));
        cells.push(newRow.insertCell(2));
        cells.push(newRow.insertCell(3));

        var eventMatch = document.getElementById("eventMatchSelect").options[document.getElementById("eventMatchSelect").selectedIndex].text;
        cells[0].innerHTML = eventMatch;

        cells[1].innerHTML = "<input type='number' id='budgetAllocation_" + lastRow + "' class='budget' />";
        cells[2].innerHTML = "<div contenteditable='true' class='description' id='description_" + lastRow + "'></div>";
        cells[3].innerHTML = "<button onclick='saveNewBudget(" + lastRow + ")'>Save</button>";
    }

    function saveNewBudget(rowNumber) {
        var eventMatchId = document.getElementById("eventMatchSelect").value;
        var budgetAllocation = document.getElementById("budgetAllocation_" + rowNumber).value;
        var description = document.getElementById("description_" + rowNumber).innerText;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                getBudgets();
            }
        };
        xhr.open("POST", "add_budget.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("eventMatch=" + eventMatchId + "&budgetAllocation=" + budgetAllocation + "&description=" + description + "&submit=1");
    }

    function appendAddRowButton() {
        var budgetInfo = document.getElementById("budgetInfo");
        var addButton = document.createElement("button");
        addButton.setAttribute("id", "addRowBtn");
        addButton.setAttribute("onclick", "addRow()");
        addButton.textContent = "Add Budget";
        budgetInfo.appendChild(addButton);
    }
    </script>

</body>
</html>