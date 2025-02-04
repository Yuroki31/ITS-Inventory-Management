<?php
  include 'init.php'; 
  include 'db_connect.php';

  // Query to fetch items that are not currently borrowed
  $query = "
      SELECT i.item_id, i.item_no, i.item_name 
      FROM items i
      LEFT JOIN (
          SELECT item_no 
          FROM borrowing 
          WHERE status = 'borrowed'
      ) b ON i.item_id = b.item_no
      WHERE b.item_no IS NULL";
  $result = mysqli_query($con, $query);

  if (!$result) {
      die("Query failed: " . mysqli_error($con));
  }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="borrow.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> <!-- Include Select2 CSS -->
</head>
<body>

    <div class="imgcontainer-left">
        <img src="perps-logo.gif" alt="Left Avatar" class="avatar-left">
    </div>

    <div class="heading-container">
        <h1>UPHSL - ITS Inventory System</h1>
        <div class="imgcontainer">
            <img src="assets/uploads/its logo.png" alt="Avatar" class="avatar">
        </div>
    </div>

    <div class="container">
        <!-- Searchable Dropdown for Item -->
        <select id="item" name="item" class="dropdown form-select" style="margin: 0 auto; display: block;">
            <option value="">Select Item</option>
            <?php 
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['item_id'] . "'>" . $row['item_no'] . ' - ' . $row['item_name'] . ' - ' . $row['item_brand'] . "</option>";
            }
            ?>
        </select>

        <select id="department" name="department" class="dropdown">
            <option value="">Select Department/Office</option>
            <option value="CAS">College of Arts and Sciences</option>
            <option value="CBA">College of Business Administration</option>
            <option value="CCS">College of Computer Studies</option>
            <option value="CRIM">College of Criminology</option>
            <option value="COE">College of Education</option>
            <option value="COA">College of Engineering and Architecture</option>
            <option value="CIHM">College of International Hospitality Management</option>
            <option value="CME">College of Maritime Education</option>
            <option value="AVIATION">School of Aviation</option>
        </select>

        <input type="text" id="room" placeholder="Enter Room" name="room" required>

        <div class="button-container">
            <button type="button" class="button1" onclick="proceed()">Proceed</button>
            <button type="button" class="button2 cancelbtn" onclick="cancel()">Cancel</button>
        </div>
    </div>

    <!-- RFID Scan Window -->
    <div id="rfidPrompt" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>PLEASE TAP YOUR ID</h3>
        <input type="text" id="rfidInput" placeholder="Scan your RFID" autofocus oninput="processRFID()">
    </div>
</div>

<!-- Confirmation Window -->
<div id="confirmationWindow" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Confirmation</h3>
        <p id="studentDetails"></p>
        <p id="itemDetails"></p>
        <p id="roomDetails"></p>
        <button onclick="closeModal('confirmationWindow')">Close</button>
    </div>
</div>

    <footer>
        &copy; 2024 UPHSL - ITS Inventory System. All Rights Reserved.
        <a href="https://uphsl.edu.ph" target="_blank">Visit Website</a>
        <a href="https://www.facebook.com/uphslits" target="_blank" class="facebook-link">
            <img src="fbicon.png" alt="Facebook" class="facebook-icon">
        </a>
    </footer>

    <!-- Include jQuery and Select2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2 on the dropdown
        $(document).ready(function() {
            $('#item').select2({
                placeholder: "Select an item", // Optional placeholder
                allowClear: true             // Optional clear button
            });
        });

        let typingTimer; 
        const typingDelay = 100; 

        function processRFID() {
            clearTimeout(typingTimer); 

            typingTimer = setTimeout(() => {
                var rfid = document.getElementById('rfidInput').value.trim();

                if (rfid.length === 10) { 
                    var formData = new FormData();
                    formData.append('item_no', document.getElementById('item').value);
                    formData.append('department', document.getElementById('department').value);
                    formData.append('room', document.getElementById('room').value);
                    formData.append('rfid', rfid);

                    fetch('borrow_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            var studentName = data.name;
                            var studentId = data.student_no;
                            var department = data.department;
                            var selectedItem = document.getElementById('item').selectedOptions[0].text;
                            var roomNumber = document.getElementById('room').value;

                            document.getElementById('studentDetails').innerText = 
                                "Student Name: " + studentName + " | Student ID: " + studentId;
                            document.getElementById('itemDetails').innerText = "Item: " + selectedItem;
                            document.getElementById('roomDetails').innerText = "Room No.: " + roomNumber;

                            document.getElementById('rfidPrompt').style.display = 'none';
                            document.getElementById('confirmationWindow').style.display = 'block';

                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 3000);
                        } else {
                            alert("Student not recognized for this RFID.");
                        }
                    });
                }
            }, typingDelay); 
        }

        function proceed() {
        document.querySelector('.container').style.display = 'none';
        document.getElementById('rfidPrompt').style.display = 'flex'; // Display modal
        }

        function cancel() {
        window.location.href = "index.php";
        }

        function showConfirmationModal() {
        document.getElementById('confirmationWindow').style.display = 'flex'; // Centered with flex
        }

        function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById("openModalButton").onclick = function() {
            document.getElementById("rfidPrompt").style.display = "block";
        };

        document.querySelector(".close").onclick = function() {
            document.getElementById("rfidPrompt").style.display = "none";
        };

        window.onclick = function(event) {
            let modal = document.getElementById("rfidPrompt");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

    </script>

</body>
</html>