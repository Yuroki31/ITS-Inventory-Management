<?php
    include 'db_connect.php';

    $item_no = $_GET['item_no'];
    $sql = "SELECT * FROM items WHERE item_no = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $item_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Item not found."]);
    }

    $stmt->close();
    $con->close();
?>
