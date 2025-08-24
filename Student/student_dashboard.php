<?php
session_start();
include('../db/db_connection.php');



$student_id = $_SESSION['rollno'];

// Fetch student details
$query = "SELECT * FROM students WHERE rollno = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f4f4;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .nav-link {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Student Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_progress.php">My Progress</a></li>
                    <li class="nav-item"><a class="nav-link" href="attendance.php">My Attendance</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../Login/logout_student.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center">Student Details</h4>
                    <table class="table">
                        <tr><th>Name:</th><td><?= htmlspecialchars($student['name']) ?></td></tr>
                        <tr><th>Roll Number:</th><td><?= htmlspecialchars($student['rollno']) ?></td></tr>
                        <tr><th>Email:</th><td><?= htmlspecialchars($student['email']) ?></td></tr>
                        <tr><th>Department ID:</th><td><?= htmlspecialchars($student['department_id']) ?></td></tr>
                        <tr><th>Section:</th><td><?= htmlspecialchars($student['section']) ?></td></tr>
                        <tr><th>Phone Number:</th><td><?= htmlspecialchars($student['phone_number']) ?></td></tr>
                        <tr><th>Parent Name:</th><td><?= htmlspecialchars($student['parent_name']) ?></td></tr>
                    </table>
                    <div class="text-center">
                        <a href="edit_student.php" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
