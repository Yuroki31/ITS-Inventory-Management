<?php
    include 'db_connect.php';
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $room = $_POST['room'];

    $sql = "SELECT available_quantity FROM consumables WHERE id = $item_id";
    $result = $con->query($sql);
    $row = $result->fetch_assoc();

    if ($row['available_quantity'] >= $quantity) {
        $new_quantity = $row['available_quantity'] - $quantity;
        $con->query("UPDATE consumables SET available_quantity = $new_quantity WHERE id = $item_id");
        $con->query("INSERT INTO consumable_logs (item_id, quantity_taken, room) VALUES ($item_id, $quantity, '$room')");
        echo "Item issued successfully!";
    } else {
        echo "Not enough stock available!";
    }
?>