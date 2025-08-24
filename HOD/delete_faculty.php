<?php
session_start();
require_once '../db/db_connection.php'; // Include your database connection file

// Check if the user is logged in and has access
if (!isset($_SESSION['department'])) {
    echo "Access denied. Please log in.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Delete the faculty
    $query = "DELETE FROM staff WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Faculty deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete the faculty.";
        }
    } else {
        $_SESSION['error_message'] = "Failed to prepare the query.";
    }

    header("Location: manage_faculties.php");
    exit;
} else {
    echo "Invalid request.";
    exit;
}
?>
