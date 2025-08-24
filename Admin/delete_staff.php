<?php
session_start();
include('../db/db_connection.php');

// Check if the email is provided
if (!isset($_GET['email'])) {
    die("Invalid Request");
}

$email = mysqli_real_escape_string($conn, $_GET['email']);

// Delete the staff record
$query = "DELETE FROM staff WHERE email = '$email'";
if (mysqli_query($conn, $query)) {
    header("Location: manage_staff.php?success=deleted");
    exit();
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>
