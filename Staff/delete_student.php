<?php
session_start();
include("../db/db_connection.php");

if (!isset($_GET['rollno'])) {
    die("Roll number is required.");
}

$rollno = $_GET['rollno'];

// Delete the student
$query = "DELETE FROM students WHERE rollno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rollno);

if ($stmt->execute()) {
    header("Location: manage_students.php?message=Student deleted successfully");
    exit;
} else {
    echo "Error deleting student.";
}
?>
