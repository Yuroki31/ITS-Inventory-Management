<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session if it has not been started already
}

if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

?>