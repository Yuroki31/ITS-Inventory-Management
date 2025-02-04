<?php
include 'db_connect.php';
include 'init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $itemDetails = $_POST['item_details'] ?? '';
    $fault_type = $_POST['fault_type'] ?? '';
    $note = $fault_type === "others" ? $_POST['note'] : "";

    if (empty($itemDetails) || empty($fault_type)) {
        echo "Error: All fields are required.";
        exit;
    }

    // Split item details into item_no and item_name
    list($item_no, $item_name) = explode('|', $itemDetails);

    $stmt = $con->prepare("INSERT INTO report (item_no, item_name, fault_type, note, date_reported) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $item_no, $item_name, $fault_type, $note);

    if ($stmt->execute()) {
        echo "Report added successfully.";
        header("Location: dashboard.php?page=report"); // Redirect back to the report page
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
}
?>