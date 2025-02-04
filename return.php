<?php
include 'db_connect.php';
include 'init.php';

// Fetch borrowing history
$sql = "SELECT b.borrowing_id, i.item_id, i.item_no, i.item_name, b.borrower_name, b.department, b.time_borrowed, b.time_returned, b.status
    FROM borrowing b
    JOIN items i ON b.item_no = i.item_id 
    ORDER BY time_borrowed DESC";
    
$result = $con->query($sql);

if (!$result) {
    die("Error fetching borrowing history: " . $con->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $return_id = $_POST['return_id'];
    $time_returned = date("Y-m-d H:i:s");
    $updateSql = "UPDATE borrowing 
                  SET time_returned = '$time_returned', status = 'Returned' 
                  WHERE borrowing_id = $return_id";

    if ($con->query($updateSql)) {
        header("Location: return.php"); // Refresh the page
        exit;
    } else {
        echo "Error updating record: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="return.css">
</head>
<body>

    <div class="back-button">
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

<div class="container mt-5">
    <h1>Borrowing History</h1>
    <table class="table table-bordered mt-3">
        <thead>
        <tr>
            <th>Item Number</th>
            <th>Borrower Name</th>
            <th>Department</th>
            <th>Time Borrowed</th>
            <th>Time Returned</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_no']) ?></td>
                    <td><?= htmlspecialchars($row['borrower_name']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['time_borrowed']) ?></td>
                    <td><?= $row['time_returned'] ? htmlspecialchars($row['time_returned']) : "Not yet returned" ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php if ($row['status'] !== 'Returned'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="return_id" value="<?= $row['borrowing_id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Mark as Returned</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>Already Returned</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No borrowing history found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $con->close(); ?>
