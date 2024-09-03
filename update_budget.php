<?php
include_once './database/db_config.php';

if(isset($_POST['updateBudget'])) {
    $budgetId = $_POST['budgetId'];
    $newBudgetAllocation = $_POST['newBudgetAllocation'];
    $newDescription = $_POST['newDescription'];

    $update_budget_query = "UPDATE budget SET allocation='$newBudgetAllocation', description='$newDescription' WHERE budget_id='$budgetId'";
    if($conn->query($update_budget_query) === TRUE) {
        echo "Budget updated successfully.";
    } else {
        echo "Error updating budget: " . $conn->error;
    }
}
?>
