<?php
    include 'db_connect.php';
    include 'init.php';

    $sql = "SELECT c_id, cname, cmodel, total_quantity, available_quantity
    FROM consumable_logs";
    $result = $con->query($sql);
?>

