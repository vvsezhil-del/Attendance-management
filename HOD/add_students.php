<?php
session_start();
include('../db/db_connection.php');

// Debugging: Check the session value for 'department'
if (isset($_SESSION['department'])) {
    $hod_department = $_SESSION['department'];
} else {
    echo "Error: Department is not set in the session.";
    exit();
}

// Fetch department ID from the department table
$query_department = "SELECT department_id FROM departments WHERE department_name = ?";
$stmt_department = $conn->prepare($query_department);
$stmt_department->bind_param('s', $hod_department);
$stmt_department->execute();
$result_department = $stmt_department->get_result();
if ($result_department->num_rows > 0) {
    $department_data = $result_department->fetch_assoc();
    $hod_department_id = $department_data['department_id'];
} else {
    echo "<div class='alert alert-danger'>Department not found in the database!</div>";
    exit();
}

// Fetch students from the same department
$query_students = "SELECT * FROM students WHERE department_id = ?";
$stmt_students = $conn->prepare($query_students);
$stmt_students->bind_param('i', $hod_department_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();

// Add a new student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rollno = $_POST['rollno'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $start_year = $_POST['start_year'];
    $end_year = $_POST['end_year'];
    $section = $_POST['section'];
    $phone_number = $_POST['phone_number'];
    $parent_name = $_POST['parent_name'];

    // Insert student into database
    $insert_query = "INSERT INTO students (rollno, name, email, start_year, end_year, department, department_id, section, phone_number, parent_name, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param(
        'ssssiissss',  // Changed first 'i' to 's' for rollno
        $rollno,
        $name,
        $email,
        $start_year,
        $end_year,
        $hod_department,
        $hod_department_id,
        $section,
        $phone_number,
        $parent_name
    );

    if ($insert_stmt->execute()) {
        // Store necessary data in session for parent_details.php
        $_SESSION['student_data'] = [
            'rollno' => $rollno,
            'parent_name' => $parent_name,
            'phone_number' => $phone_number,
            'email' => $email
        ];
        
        // Show success message and redirect
        echo "<script>
                alert('Student added successfully!');
                window.location.href = 'parent_details.php';
              </script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error adding student: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .btn-back { 
            background-color: black;
            color: white;
        }
        .btn-back:hover{
            border: 1px solid black;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Add Student</h2>
    <form method="POST" class="row g-3 mb-5">
        <div class="col-md-6">
            <label for="rollno" class="form-label">Roll Number</label>
            <input type="text" name="rollno" id="rollno" class="form-control" required> <!-- Changed from type="number" to type="text" -->
        </div>
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="start_year" class="form-label">Start Year</label>
            <input type="number" name="start_year" id="start_year" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="end_year" class="form-label">End Year</label>
            <input type="number" name="end_year" id="end_year" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="section" class="form-label">Section</label>
            <input type="text" name="section" id="section" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="parent_name" class="form-label">Parent Name</label>
            <input type="text" name="parent_name" id="parent_name" class="form-control" required>
        </div>
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary">Add Student</button>
            <a href="hod_dashboard.php" class="btn btn-back">Back to Dashboard</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>