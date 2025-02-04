<?php
    include 'db_connect.php';
    include 'init.php';

    $sql = "SELECT c_id, cname, cmodel, total_quantity, available_quantity
    FROM consumables";
    $result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- CSS for Consumables --> 
    <link rel="stylesheet" href="consumables.css"> 

    <title>Consumable Inventory</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

    <a href="index.php" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <a href="consumable_logs.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Logs
    </a>

    <h3>Consumable Inventory</h3>
    <table id="consumableTable" class="table table-striped">
        <thead>
            <tr>
                <th>Item No.</th>
                <th>Item Name</th>
                <th>Item Brand</th>
                <th>Total Quantity</th>
                <th>Available Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['c_id'] ?> </td>
                    <td><?= $row['cname'] ?></td>
                    <td><?= $row['cmodel'] ?></td>
                    <td><?= $row['total_quantity'] . " " . "Meters" ?></td>
                    <td><?= $row['available_quantity'] . " " . "Meters" ?></td>
                    <td><button class="btn btn-primary" onclick="openIssueModal(<?= $row['id'] ?>, '<?= $row['item_name'] ?>')">Issue</button></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Issue Item Modal -->
    <div id="issueModal" style="display:none;">
        <h4>Issue Item</h4>
        <form id="issueForm">
            <input type="hidden" id="itemId" name="item_id">
            <p id="itemName"></p>
            <label>Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>
            <label>Room:</label>
            <input type="text" id="room" name="room" required>
            <button type="submit">Issue</button>
        </form>
    </div>

    <script>
    function openIssueModal(id, name) {
        $('#itemId').val(id);
        $('#itemName').text('Item: ' + name);
        $('#issueModal').show();
    }

    $('#issueForm').submit(function(e) {
        e.preventDefault();
        $.post('issue_item.php', $(this).serialize(), function(response) {
            alert(response);
            location.reload();
        });
    });
    </script>
</body>
</html>