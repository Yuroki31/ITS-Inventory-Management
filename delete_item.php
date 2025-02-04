<?php
include 'db_connect.php';
include 'init.php';

if (isset($_GET['item_no'])) {
    $item_no = $_GET['item_no'];

    // Delete the item from the database
    $stmt = $con->prepare("DELETE FROM items WHERE item_no = ?");
    $stmt->bind_param("i", $item_no);

    if ($stmt->execute()) {
        header("Location: dashboard.php"); // Redirect back to inventory
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
}
?>
