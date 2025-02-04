<?php
// Include database connection
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);

    // Check if the admin username exists
    $query = $con->prepare("SELECT user_id FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // You can generate a token or use a static reset key
        $resetKey = "STATIC_RESET_KEY"; // Use a predefined static reset key

        // Redirect to the reset password page with the key
        header("Location: reset_password.php?key=$resetKey");
        exit;
    } else {
        echo "Invalid admin username.";
    }
}
?>
