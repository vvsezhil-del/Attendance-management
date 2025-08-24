<?php
session_start();
include("../db/db_connection.php");

if (!isset($_GET['rollno'])) {
    die("Roll number is required.");
}

$rollno = $_GET['rollno'];

// Fetch student details
$query = "SELECT * FROM students WHERE rollno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rollno);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
            line-height: 1.6;
        }

        .btn-group {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn.delete {
            background-color: #dc3545;
        }

        .btn.delete:hover {
            background-color: #a71d2a;
        }

        .btn.back {
            background-color: #6c757d;
        }

        .btn.back:hover {
            background-color: #4e555b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Details</h1>
        <p><strong>Roll Number:</strong> <?= htmlspecialchars($student['rollno']) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
        <p><strong>Section:</strong> <?= htmlspecialchars($student['section']) ?></p>
        <p><strong>Phone Number:</strong> <?= htmlspecialchars($student['phone_number']) ?></p>
        <p><strong>Department:</strong> <?= htmlspecialchars($student['department']) ?></p>
        <p><strong>Parent Name:</strong> <?= htmlspecialchars($student['parent_name']) ?></p>

        <div class="btn-group">
            <a class="btn" href="edit_student.php?rollno=<?= urlencode($student['rollno']) ?>">Edit</a>
            <a class="btn delete" href="delete_student.php?rollno=<?= urlencode($student['rollno']) ?>" 
               onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
            <a class="btn back" href="manage_students.php">Back</a>
        </div>
    </div>
</body>
</html>
