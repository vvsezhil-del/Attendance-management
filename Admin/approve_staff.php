<?php
session_start();
include('../db/db_connection.php');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $status = $_POST['status'] ?? '';

    if (!empty($email) && in_array($status, ['pending', 'rejected', 'approved'])) {
        // Update the status in the staff table
        $query = "UPDATE staff SET status = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $status, $email);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo 'Status updated successfully';
        } else {
            echo 'Error updating status';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo 'Invalid data';
    }
}

mysqli_close($conn);
?>
