<?php
    include 'init.php';
    include 'db_connect.php'; // Include your database connection
?>


<head>
    <link rel="stylesheet" href="dash-board.css">
</head>

<nav class="main-header navbar navbar-expand navbar-primary navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <?php if(isset($_SESSION['login_id'])): ?>
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
            </li>
        <?php endif; ?>
        <li>
            <a class="nav-link text-white" href="./" role="button"> <large><b>Dashboard</b></large></a>
        </li>
    </ul>
</nav>
