<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    // Update the status to 'fixed'
    $stmt = $con->prepare("UPDATE report SET action = 'fixed' WHERE report_id = ?");
    $stmt->bind_param("i", $report_id);

    if ($stmt->execute()) {
        // After updating, redirect back to the reports page
        header("Location: dashboard.php?page=report");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$con->close();
?>