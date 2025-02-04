<?php
// Include database connection
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $newPassword = md5($_POST['password']); // Encrypt the password using MD5
    $resetKey = $_POST['key'];

    // Validate the reset key (in this example, it's static)
    if ($resetKey === "STATIC_RESET_KEY") {
        // Update the password for the admin user
        $query = $con->prepare("UPDATE users SET password = ? WHERE username = ?");
        $query->bind_param("ss", $newPassword, $username);

        if ($query->execute()) {
            header("Location: login.php?success=1");
            exit;
        } else {
            echo "<script>alert('Failed to reset password');</script>";
        }
    } else {
        echo "Invalid reset key.";
    }
}
?>
