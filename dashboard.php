<?php

    include 'db_connect.php';
    include 'init.php';

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="path/to/sidebar.css" rel="stylesheet">
    <link href="path/to/dash-board.css" rel="stylesheet">
    <title>Inventory Management</title>
</head>
<body>

<div class="layout">
    <!-- Sidebar -->
    <aside class="main-sidebar">
        <?php include 'sidebar.php'; ?>
    </aside>

    <!-- Main content area -->
    <div class="main-content">
        <!-- Topbar -->
        <?php include 'topbar.php'; ?>

        <!-- Dynamic content window -->
        <div id="dynamic-content" class="content">
            <?php
            // Include the appropriate content based on the page parameter
            $page = isset($_GET['page']) ? $_GET['page'] : 'item_list'; // Default to 'item_list'
            if (file_exists("content/$page.php")) {
                include "content/$page.php";  // Dynamically include the page
            } else {
                echo "Page not found.";
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="dashboard.js"></script> <!-- Include the JS for dynamic content -->
</body>
</html>
