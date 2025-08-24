<?php
include('../db/db_connection.php');

if (isset($_POST['rollno'], $_POST['semester'], $_POST['subject_code'])) {
    $rollno = mysqli_real_escape_string($conn, $_POST['rollno']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);

    $query = "SELECT total_cia_marks FROM cia_marks WHERE rollno='$rollno' AND semester='$semester' AND subject_code='$subject_code'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    echo $row['total_cia_marks'] ?? 0; // Default to 0 if no marks found
}
?>
