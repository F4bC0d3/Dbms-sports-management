<?php
include_once './database/db_config.php';

if(isset($_POST['deleteBudget'])) {
    $budgetId = $_POST['budgetId'];

    $delete_budget_query = "DELETE FROM budget WHERE budget_id='$budgetId'";
    if($conn->query($delete_budget_query) === TRUE) {
        echo "Budget deleted successfully.";
    } else {
        echo "Error deleting budget: " . $conn->error;
    }
}
?>
