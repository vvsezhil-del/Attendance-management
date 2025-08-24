<?php
session_start();
include '../db/db_connection.php';

// Check if roll number is passed
if (!isset($_GET['rollno'])) {
    die("Invalid request.");
}

$rollno = $_GET['rollno'];

// Delete the student record
$sql = "DELETE FROM students WHERE rollno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rollno);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Student deleted successfully.";
} else {
    $_SESSION['error_message'] = "Error deleting student: " . $conn->error;
}

// Redirect back to manage students page after deletion
header("Location: manage_students.php");
exit();
?>
