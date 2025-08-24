<?php
session_start();
include '../db/db_connection.php';

// Check if roll number is passed
if (!isset($_GET['rollno'])) {
    die("Invalid request.");
}

$rollno = $_GET['rollno'];

// Fetch the existing student details
$sql = "SELECT * FROM students WHERE rollno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rollno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

// Update the student details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $start_year = $_POST['start_year'];
    $end_year = $_POST['end_year'];
    $section = $_POST['section'];
    $phone_number = $_POST['phone_number'];
    $parent_name = $_POST['parent_name'];

    $update_sql = "UPDATE students SET name = ?, email = ?, start_year = ?, end_year = ?, section = ?, phone_number = ?, parent_name = ?, updated_at = NOW() WHERE rollno = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssiiissi", $name, $email, $start_year, $end_year, $section, $phone_number, $parent_name, $rollno);

    if ($update_stmt->execute()) {
        echo "<div class='alert alert-success'>Student details updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating student: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Edit Student</h2>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="start_year" class="form-label">Start Year</label>
            <input type="number" name="start_year" id="start_year" class="form-control" value="<?php echo htmlspecialchars($student['start_year']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="end_year" class="form-label">End Year</label>
            <input type="number" name="end_year" id="end_year" class="form-control" value="<?php echo htmlspecialchars($student['end_year']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="section" class="form-label">Section</label>
            <input type="text" name="section" id="section" class="form-control" value="<?php echo htmlspecialchars($student['section']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?php echo htmlspecialchars($student['phone_number']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="parent_name" class="form-label">Parent Name</label>
            <input type="text" name="parent_name" id="parent_name" class="form-control" value="<?php echo htmlspecialchars($student['parent_name']); ?>" required>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
