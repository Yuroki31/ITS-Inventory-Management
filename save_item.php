<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_no = $_POST['item_no'];
    $item_name = $_POST['item_name'];
    $item_brand = $_POST['item_brand'];
    $room = $_POST['room'];
    $item_model = $_POST['item_model'];
    $item_serial = $_POST['item_serial'];

    // Insert item without specifying item_no
    $sql = "INSERT INTO items (item_no, item_name, item_brand, room, item_model, item_serial) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssss", $item_no, $item_name, $item_brand, $room, $item_model, $item_serial);
        if ($stmt->execute()) {
            echo "<script>alert('Item added successfully!'); window.location.href = 'dashboard.php';</script>";
        } else {
            // Escape the error message for use in JavaScript
            $error_message = addslashes($stmt->error);
            echo "<script>alert('Error: $error_message');</script>";
        }
        $stmt->close();
    } else {
        // Escape the SQL error message
        $error_message = addslashes($con->error);
        echo "<script>alert('SQL Error: $error_message');</script>";
    }
}

$con->close();
?>
