<?php
session_start();
include('../db/db_connection.php');

// Get the roll number from the URL
$rollno = $_GET['rollno'] ?? '';

// If no rollno is provided, redirect to manage students page
if (empty($rollno)) {
    header('Location: manage_students.php');
    exit;
}

// Delete the student record from the database
$deleteQuery = "DELETE FROM students WHERE rollno = '" . mysqli_real_escape_string($conn, $rollno) . "'";

// Execute the delete query
if (mysqli_query($conn, $deleteQuery)) {
    // Redirect to manage students page with a success message
    header('Location: manage_students.php?status=deleted');
} else {
    // If there's an error, display an error message
    echo "Error deleting student: " . mysqli_error($conn);
}
?>
