<?php
include 'init.php';
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $item_no = $_POST['item_no'];
    $department = $_POST['department'];
    $room = $_POST['room'];
    $rfid = $_POST['rfid'];

    // Fetch student details using the RFID
    $query = "SELECT student_name, student_no, department FROM rfid WHERE rfid_id = '$rfid'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $student = mysqli_fetch_assoc($result);
        $student_name = $student['student_name'];
        $student_no = $student['student_no'];
        $student_department = $student['department'];

        // Insert the borrow data into the `borrowing` table
        $borrow_query = "INSERT INTO borrowing (item_no, borrower_name, department, time_borrowed, status)
                         VALUES ('$item_no', '$student_name', '$department', NOW(), 'borrowed')";

        if (mysqli_query($con, $borrow_query)) {
            echo json_encode(["success" => true, "name" => $student_name, "student_no" => $student_no, "department" => $student_department]);
        } else {
            echo json_encode(["success" => false, "message" => "Error saving borrow data"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Student not found"]);
    }
}
?>
