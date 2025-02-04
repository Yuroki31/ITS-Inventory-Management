<?php
// Include database connection
include 'db_connect.php';
include 'init.php';

// Initialize variables
$totalItems = mysqli_query($con, "SELECT COUNT(*) as count FROM items");
$totalReports = mysqli_query($con, "SELECT COUNT(*) as count FROM report");
$totalBorrowed = mysqli_query($con, "SELECT COUNT(*) as count FROM borrowing WHERE status = 'borrowed'");

// Fetch counts with error handling
$totalItems = $totalItems ? $totalItems->fetch_assoc()['count'] : 0;
$totalReports = $totalReports ? $totalReports->fetch_assoc()['count'] : 0;
$totalBorrowed = $totalBorrowed ? $totalBorrowed->fetch_assoc()['count'] : 0;

// Fetch all recently borrowed items
$recentlyBorrowed = [];
$result = mysqli_query($con, "
    SELECT items.item_id, items.item_no, items.item_name, borrowing.status,
    IF(borrowing.status = 'borrowed', borrowing.time_borrowed, borrowing.time_returned) AS display_time
    FROM borrowing
    INNER JOIN items ON borrowing.item_no = items.item_id
    ORDER BY borrowing.time_borrowed DESC
");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recentlyBorrowed[] = "{$row['item_no']} - {$row['item_name']} {$row['status']} on {$row['display_time']}";
    }
} else {
    echo "Error fetching borrowing history: " . mysqli_error($con);
}

// Fetch data for the monthly graph
$graphData = mysqli_query($con, "
    SELECT items.item_name, COUNT(borrowing.item_no) as borrow_count, MONTH(borrowing.time_borrowed) as borrow_month
    FROM borrowing
    INNER JOIN items ON borrowing.item_no = items.item_id
    WHERE YEAR(borrowing.time_borrowed) = YEAR(CURDATE())
    GROUP BY items.item_name, borrow_month
    ORDER BY borrow_month ASC, borrow_count DESC
");

$chartData = [];
if ($graphData) {
    while ($row = mysqli_fetch_assoc($graphData)) {
        $chartData[] = $row;
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <video autoplay muted loop id="backgroundVideo">
        <source src="bg.mp4" type="video/mp4">
    </video>

    <div class="inventory-ui">
        <div class="content">
            <div class="imgcontainer">
                <img src="assets/uploads/its logo.png" alt="Avatar" class="avatar">
            </div>
            <div class="button-container">
                <form action="dashboard.php">
                    <button type="submit" class="button1">Dashboard</button>
                </form>

                <form action="borrow.php">
                    <button type="submit" class="button1">Borrow</button>
                </form>

                <form action="consumables.php">
                    <button type="submit" class="button1">Consumables</button>
                </form>

                <form action="return.php">
                    <button type="submit" class="button1">Return</button>
                </form>

                <form action="logout.php" method="post">
                    <button type="submit" class="button2">Log out</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-4">
    <div class="row totals-row justify-content-start gap-4"> <!-- Add totals-row class here -->
        <!-- Total Items Card -->
        <div class="col-lg-3 col-md-4 col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">Total Items</h5>
                    <p class="card-text display-6 fw-bold"><?= $totalItems ?></p>
                </div>
            </div>
        </div>

        <!-- Total Reports Card -->
        <div class="col-lg-3 col-md-4 col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">Total Reports</h5>
                    <p class="card-text display-6 fw-bold"><?= $totalReports ?></p>
                </div>
            </div>
        </div>

        <!-- Total Borrowed Card -->
        <div class="col-lg-3 col-md-4 col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">Total Borrowed</h5>
                    <p class="card-text display-6 fw-bold"><?= $totalBorrowed ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Recently Borrowed Section -->
        <div class="recently-borrowed-card position-absolute" style="right: 20px; top: 20px; bottom: 20px;">
            <div class="card shadow-lg h-100 d-flex flex-column">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Recently Borrowed</h5>
                </div>
                <div class="card-body overflow-auto">
                    <ul class="list-group list-group-flush" id="recentBorrowedList">
                        <?php if (!empty($recentlyBorrowed)): ?>
                            <?php foreach ($recentlyBorrowed as $item): ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">No recent borrowing history available.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Monthly Borrowing Trends -->
        <div class="graph-container top-center">
            <h3>Monthly Borrowed Items</h3>
        <canvas id="borrowingChart" width="400" height="200"></canvas>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const recentBorrowedList = document.getElementById("recentBorrowedList");
            const maxItems = 5;

            if (recentBorrowedList) {
                function addBorrowedItem(itemText) {
                    const listItem = document.createElement("li");
                    listItem.className = "list-group-item";
                    listItem.textContent = itemText;

                    recentBorrowedList.appendChild(listItem);

                    if (recentBorrowedList.children.length > maxItems) {
                        recentBorrowedList.removeChild(recentBorrowedList.firstChild);
                    }
                }

                <?php foreach ($recentlyBorrowed as $item): ?>
                    addBorrowedItem("<?= htmlspecialchars($item) ?>");
                <?php endforeach; ?>
            }

            // Chart.js configuration for Monthly Borrowing Trends
            const chartData = <?= json_encode($chartData) ?>;
            if (chartData.length > 0) {
                const labels = [];
                const datasets = {};

                chartData.forEach(item => {
                    const month = new Date(2023, item.borrow_month - 1).toLocaleString('default', { month: 'long' });

                    if (!labels.includes(month)) labels.push(month);

                    if (!datasets[item.item_name]) {
                        datasets[item.item_name] = Array(labels.length).fill(0);
                    }

                    datasets[item.item_name][labels.indexOf(month)] = item.borrow_count;
                });

                const data = {
                    labels: labels,
                    datasets: Object.entries(datasets).map(([itemName, dataPoints]) => ({
                        label: itemName,
                        data: dataPoints,
                        fill: false,
                        borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                        tension: 0.1,
                    })),
                };

                const config = {
                    type: 'line',
                    data: data,
                };

                new Chart(document.getElementById('borrowingChart'), config);
            } else {
                document.getElementById('borrowingChart').parentElement.innerHTML = "<p>No borrowing data available for the chart.</p>";
            }
        });
    </script>
</body>
</html>