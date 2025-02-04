<?php
    include 'init.php';
    include 'db_connect.php';

    // Fetch data from the database
    $sql = "SELECT b.borrowing_id, i.item_id, i.item_no, i.item_name, b.borrower_name, b.department, b.time_borrowed, b.time_returned, b.status
    FROM borrowing b
    JOIN items i ON b.item_no = i.item_id";
    $result = $con->query($sql);

    // Fetch distinct years from the database
    $yearsQuery = "SELECT DISTINCT YEAR(time_borrowed) AS year FROM borrowing ORDER BY year DESC";
    $yearsResult = $con->query($yearsQuery);

    // Fetch distinct months from the database
    $monthsQuery = "SELECT DISTINCT MONTH(time_borrowed) AS month FROM borrowing ORDER BY month ASC";
    $monthsResult = $con->query($monthsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow History</title>
    <link rel="stylesheet" href="items.css"> <!-- Optional: Link to a CSS file for styling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6h5/tmj6sYx" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <h2>Borrow History</h2>

    <div class="mb-3">
        <label for="filterYear" class="form-label">Filter by Year:</label>
        <select id="filterYear" class="form-select">
            <option value="">All Years</option>
            <?php
            while ($row = $yearsResult->fetch_assoc()) {
                echo "<option value='{$row['year']}'>{$row['year']}</option>";
            }
            ?>
        </select>

        <label for="filterMonth" class="form-label mt-3">Filter by Month:</label>
        <select id="filterMonth" class="form-select">
            <option value="">All Months</option>
            <?php
            $months = [
                1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May",
                6 => "June", 7 => "July", 8 => "August", 9 => "September",
                10 => "October", 11 => "November", 12 => "December"
            ];
            while ($row = $monthsResult->fetch_assoc()) {
                $monthNumber = $row['month'];
                echo "<option value='" . str_pad($monthNumber, 2, "0", STR_PAD_LEFT) . "'>{$months[$monthNumber]}</option>";
            }
            ?>
        </select>
    </div>

    <div>
        <button class="btn btn-secondary" onclick="printReports()">Print</button>
        <button class="btn btn-secondary" onclick="downloadPDF()">Download PDF</button>
            </div>
    </div>
    <table id="borrowingTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Item No.</th>
                <th>Item Name</th>
                <th>Borrower</th>
                <th>Department</th>
                <th>Time Borrowed</th>
                <th>Time Returned</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['item_no'] . "</td>";
                    echo "<td>" . $row['item_name'] . "</td>";
                    echo "<td>" . $row['borrower_name'] . "</td>";
                    echo "<td>" . $row['department'] . "</td>";
                    echo "<td>" . $row['time_borrowed'] . "</td>";
                    echo "<td>" . $row['time_returned'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>

    function printReports() {
    // Simply get the table's outerHTML for now (including header and content)
    const tableContent = document.querySelector('#borrowingTable').outerHTML;
    
    // Open the print window
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.open();
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Borrow History</title>
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
                <h3>Borrow History</h3>
                ${tableContent}
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
        const table = document.querySelector('#borrowingTable');

        if (table) {
            // Extract table rows and headers
            const rows = Array.from(table.querySelectorAll('tbody tr')).map(row =>
                Array.from(row.querySelectorAll('td')).map(cell => cell.innerText)
            );
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText);

            // Add a title to the PDF
            pdf.text("Borrow History", 14, 15);

            // Use autoTable to render the table
            pdf.autoTable({
                head: [headers],
                body: rows,
                startY: 20,  // Position where the table starts
                theme: 'grid',  // Add grid to the table
                margin: { top: 30 },  // Adjust margin for the header
            });

            // Save the PDF
            pdf.save("ITS Borrow History.pdf");
        } else {
            alert("Error: Table element not found.");
        }
    }
    
        $(document).ready(function() {
        const table = $('#borrowingTable').DataTable({
            "pageLength": 15,
            "paging": false,
            "scrollY": "300px",
            "scrollCollapse": true,
            "info": false,
            "searching": true
        });

        // Custom filtering function
        $.fn.dataTable.ext.search.push(function(settings, data) {
            const year = $('#filterYear').val();
            const month = $('#filterMonth').val();
            const timeBorrowed = data[4]; // Assuming "Time Borrowed" is in the 5th column

            if (year || month) {
                const date = new Date(timeBorrowed);
                const dateYear = date.getFullYear().toString();
                const dateMonth = ("0" + (date.getMonth() + 1)).slice(-2); // Format as "01", "02", etc.

                if (year && year !== dateYear) return false;
                if (month && month !== dateMonth) return false;
            }

            return true;
        });

        // Apply filter automatically when dropdown values change
        $('#filterYear, #filterMonth').on('change', function() {
            table.draw();
        });
    });

</script>

</body>
</html>