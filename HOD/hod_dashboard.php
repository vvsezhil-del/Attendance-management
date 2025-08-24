<?php
session_start();
include('../db/db_connection.php');


$name = $_SESSION['name'];
$role = $_SESSION['role'];
$department = $_SESSION['department'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background-color: #5D54A4; }
        .navbar a, .navbar-brand { color: white; }
        .welcome { margin-top: 20px; text-align: center; }
        .timetable { margin-top: 30px; }
        .timetable th, .timetable td { text-align: center; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">BM Techx</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_students.php">Manage Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_faculties.php">Manage Faculties</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_attendance.php">Manage Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_marks.php">Manage Marks</a></li>
                    <li class="nav-item"><a class="nav-link" href="../login/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
            <p>Role: <?php echo htmlspecialchars($role); ?></p>
            <p>Department: <?php echo htmlspecialchars($department); ?></p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

