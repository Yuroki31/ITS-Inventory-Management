<?php
include 'db_connect.php';
include 'init.php';

// Fetch reports from the `reports` table
$sql = "SELECT report_id, item_no, item_name, fault_type, note, date_reported, action FROM report";
$result = $con->query($sql);

if (!$result) {
    die("Error fetching reports: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="report.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Reports</h1>
        <div>
            <button class="btn btn-secondary" onclick="printReports()">Print Reports</button>
            <button class="btn btn-secondary" onclick="downloadPDF()">Download PDF</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal">
                Add Report
            </button>
        </div>
    </div>
        <table id="reportTable" class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Item Number</th>
                <th>Item Name</th>
                <th>Fault Type</th>
                <th>Note</th>
                <th>Date Reported</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_no']) ?></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['fault_type']) ?></td>
                        <td><?= htmlspecialchars($row['note']) ?></td>
                        <td><?= htmlspecialchars($row['date_reported']) ?></td>
                        <td>
                            <?php echo ($row['action'] === 'fixed') ? 'Fixed' : 'Pending'; ?>
                        </td>
                        <td>
                            <?php if ($row['action'] !== 'fixed'): ?>
                                <a href="mark_fixed.php?id=<?= $row['report_id'] ?>" class="btn btn-success btn-sm">Mark Fixed</a>
                            <?php else: ?>
                                <span class="btn btn-secondary btn-sm">Fixed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<!-- Add Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reportForm" method="POST" action="save_report.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Add Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Dropdown for Item -->
                    <div class="mb-3">
                        <label for="itemDetails" class="form-label">Item</label>
                        <select class="form-select" id="itemDetails" name="item_details" required>
                            <option value="">Select Item</option>
                            <?php
                            // Fetch items dynamically
                            $itemSql = "SELECT item_no, item_name FROM items";
                            $itemResult = $con->query($itemSql);
                            while ($itemRow = $itemResult->fetch_assoc()): ?>
                                <option value="<?= $itemRow['item_no'] . '|' . $itemRow['item_name'] ?>">
                                    <?= $itemRow['item_no'] ?> - <?= $itemRow['item_name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- Dropdown for Fault Type -->
                    <div class="mb-3">
                        <label for="faultType" class="form-label">Fault Type</label>
                        <select class="form-select" id="faultType" name="fault_type" required>
                            <option value="">Select Fault</option>
                            <option value="damaged">Damaged</option>
                            <option value="missing">Missing</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <!-- Note Field -->
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
        // Print functionality
    function printReports() {
        const content = document.querySelector('.table').outerHTML; // Select the table only for printing
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Reports</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                    <style>
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 8px;
                            text-align: left;
                        }
                        @media print {
                            body {
                                font-family: Arial, sans-serif;
                            }
                        }
                    </style>
                </head>
                <body>
                    <h3>Reports</h3>
                    ${content}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }


    // Download PDF functionality
    function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        // Select the table element
        const table = document.querySelector('#reportTable');

        if (table) {
            // Extract table rows and headers
            const rows = Array.from(table.querySelectorAll('tbody tr')).map(row =>
                Array.from(row.querySelectorAll('td')).map(cell => cell.innerText)
            );
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText);

            // Add a title to the PDF
            pdf.text("Reports", 14, 15);

            // Use autoTable to render the table
            pdf.autoTable({
                head: [headers],
                body: rows,
                startY: 20,  // Position where the table starts
                theme: 'grid',  // Add grid to the table
                margin: { top: 30 },  // Adjust margin for the header
            });

            // Save the PDF
            pdf.save("Reports.pdf");
        } else {
            alert("Error: Table element not found.");
        }
    }


    $(document).ready(function() {
        $('#reportTable').DataTable({
            "pageLength": 15,   // Limit number of rows displayed to 15
            "paging": false,    // Disable pagination
            "scrollY": "300px", // Set vertical scroll height (change to your preference)
            "scrollCollapse": true, // Allow collapsing of scroll area
            "info": false,      // Remove info (e.g. "Showing 1 to 15 of 100 entries")
            "searching": true   // Keep the search functionality active
        });
    });

        $('.markFixedBtn').click(function() {
        var reportId = $(this).data('id');  // Get report ID from the button's data-id attribute

        $.ajax({
            url: 'mark_fixed.php',
            method: 'GET',
            data: { id: reportId },
            success: function(response) {
                // After marking the report as fixed, refresh the table or the action
                location.reload();  // Refresh the page to show updated status
            }
        });
    });


</script>

</body>
</html>

<?php $con->close(); ?>