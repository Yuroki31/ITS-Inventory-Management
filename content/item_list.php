<?php
// Include database connection
include 'init.php';
include 'db_connect.php';

// Fetch data from the database
$sql = "SELECT item_no, item_name, room, item_brand, item_model, item_serial FROM items";
$result = $con->query($sql);

if ($result === false) {
    die("Query failed: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory List</title>
    <link rel="stylesheet" href="items.css"> <!-- Optional: Link to a CSS file for styling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Inventory List</h3>
        <button class="btn btn-secondary" onclick="printTable()">Print</button>
        <button class="btn btn-success" id="downloadPdf">Download PDF</button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            Add Item
        </button>
    </div>

    <!-- Scrollable Table Container -->
    <div class="table-container">
        <table id="inventoryTable" class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Room No.</th>
                    <th>Item Classification</th>
                    <th>Item Brand</th>
                    <th>Item Model</th>
                    <th>Item Serial Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
               <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['item_no'] . "</td>";
                        echo "<td>" . $row['room'] . "</td>";
                        echo "<td>" . $row['item_name'] . "</td>";
                        echo "<td>" . $row['item_brand'] . "</td>";
                        echo "<td>" . (!empty($row['item_model']) ? $row['item_model'] : "N/A") . "</td>";
                        echo "<td>" . (!empty($row['item_serial']) ? $row['item_serial'] : "N/A") . "</td>";
                        echo "<td>
                                <button class='btn btn-primary btn-sm' onclick='editItem(" . json_encode($row) . ")'>Edit</button>
                                <button class='btn btn-danger btn-sm' onclick='deleteItem(" . $row['item_no'] . ")'>Delete</button>
                                <button class='btn btn-secondary btn-sm' disabled>Future Action</button>
                              </td>";
                        echo "</tr>";
                    }
                } 
            ?>
            </tbody>
        </table>
    </div>

</div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addItemForm" method="POST" action="save_item.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="itemNo" class="form-label">Item No</label>
                            <input type="text" class="form-control" id="itemNo" name="item_no" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemName" class="form-label">Item Classification</label>
                            <input type="text" class="form-control" id="itemName" name="item_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemBrand" class="form-label">Item Brand</label>
                            <input type="text" class="form-control" id="itemBrand" name="item_brand" required>
                        </div>
                        <div class="mb-3">
                            <label for="room" class="form-label">Room</label>
                            <select class="form-control" id="room" name="room">
                                <option value="">-- Select Room --</option>
                                <option value="CLab - 1">CLab - 1</option>
                                <option value="CLab - 2">CLab - 2</option>
                                <option value="CLab - 3">CLab - 3</option>
                                <option value="Mac Lab">Mac Lab</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="itemModel" class="form-label">Item Model</label>
                            <input type="text" class="form-control" id="itemModel" name="item_model" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemSerial" class="form-label">Item Serial Number</label>
                            <input type="text" class="form-control" id="itemSerial" name="item_serial" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editItemForm" method="POST" action="update_item.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="editItemNo" name="item_no">
                        <div class="mb-3">
                            <label for="editItemName" class="form-label">Item Classification</label>
                            <input type="text" class="form-control" id="editItemName" name="item_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editItemBrand" class="form-label">Item Brand</label>
                            <input type="text" class="form-control" id="editItemBrand" name="item_brand" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRoom" class="form-label">Room</label>
                            <select class="form-control" id="editRoom" name="room">
                                <option value="">-- Select Room --</option>
                                <option value="CLab - 1">CLab - 1</option>
                                <option value="CLab - 2">CLab - 2</option>
                                <option value="CLab - 3">CLab - 3</option>
                                <option value="Mac Lab">Mac Lab</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editItemModel" class="form-label">Item Model</label>
                            <input type="text" class="form-control" id="editItemModel" name="item_model" required>
                        </div>
                        <div class="mb-3">
                            <label for="editItemSerial" class="form-label">Item Serial Number</label>
                            <input type="text" class="form-control" id="editItemSerial" name="item_serial" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<!-- Print and Download PDF Scripts -->
<script>

    // Edit item function
    function editItem(item) {
        const itemNo = item.item_no; // Extract the item_no from the object

        // Fetch item details via AJAX
        $.ajax({
            url: 'item_details.php', // Endpoint to fetch item details
            type: 'GET',
            data: { item_no: itemNo }, // Pass only the item_no
            success: function (data) {
                const item = JSON.parse(data); // Assuming JSON response
                $('#editItemNo').val(item.item_no);
                $('#editItemName').val(item.item_name);
                $('#editItemBrand').val(item.item_brand);
                $('#editRoom').val(item.room);
                $('#editItemModel').val(item.item_model);
                $('#editItemSerial').val(item.item_serial);
                $('#editItemModal').modal('show'); // Show the modal
            },
            error: function () {
                alert('Failed to fetch item details.');
            }
        });
    }


    // Delete item function
    function deleteItem(itemNo) {
        if (confirm(`Are you sure you want to delete Item No: ${itemNo}?`)) {
            // Send delete request via AJAX
            $.ajax({
                url: 'delete_item.php', // Endpoint to delete item
                type: 'POST',
                data: { item_no: itemNo },
                success: function (response) {
                    alert(response.message || 'Item deleted successfully.');
                    location.reload(); // Refresh the page to reflect changes
                },
                error: function () {
                    alert('Failed to delete item.');
                }
            });
        }
    }


    // Print Table
    function printTable() {
        const printContent = document.querySelector('#inventoryTable').outerHTML;
        const newWindow = window.open('', '_blank', 'width=800,height=600');
        newWindow.document.write(`
            <html>
            <head>
                <title>Print Inventory List</title>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    table, th, td {
                        border: 1px solid black;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                    }
                </style>
            </head>
            <body>
                <h3>Inventory List</h3>
                ${printContent}
            </body>
            </html>
        `);
        newWindow.document.close();
        newWindow.print();
    }

    // Download PDF
    document.getElementById('downloadPdf').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Add title
        doc.text("Inventory List", 10, 10);

        // Get table data
        const table = document.querySelector("#inventoryTable");
        const rows = Array.from(table.rows);
        const data = rows.map(row => Array.from(row.cells).map(cell => cell.innerText));

        // Add table to PDF
        doc.autoTable({
            head: [data[0]], // Table header
            body: data.slice(1), // Table rows
            startY: 20,
        });

        // Save the PDF
        doc.save("Inventory List.pdf");
    });

    // DataTables Initialization
    $(document).ready(function() {
        $('#inventoryTable').DataTable({
            "pageLength": 15,   // Limit number of rows displayed to 15
            "paging": false,    // Disable pagination
            "scrollY": "300px", // Set vertical scroll height (change to your preference)
            "scrollCollapse": true, // Allow collapsing of scroll area
            "info": false,      // Remove info (e.g. "Showing 1 to 15 of 100 entries")
            "searching": true   // Keep the search functionality active
        });
    });
</script>

</body>
</html>

<?php
// Close the database connection
$con->close();
?>