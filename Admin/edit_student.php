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

// Fetch the student data from the database
$query = "SELECT * FROM students WHERE rollno = '" . mysqli_real_escape_string($conn, $rollno) . "'";
$result = mysqli_query($conn, $query);

// If the student does not exist, redirect to manage students page
if (mysqli_num_rows($result) == 0) {
    header('Location: manage_students.php');
    exit;
}

$student = mysqli_fetch_assoc($result);

// Fetch departments for the department dropdown
$departmentsQuery = "SELECT department_name FROM departments";
$departmentsResult = mysqli_query($conn, $departmentsQuery);
$departments = [];
if ($departmentsResult) {
    while ($row = mysqli_fetch_assoc($departmentsResult)) {
        $departments[] = $row['department_name'];
    }
}

// Handle form submission for updating student details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $start_year = $_POST['start_year'] ?? '';
    $end_year = $_POST['end_year'] ?? '';
    $department = $_POST['department'] ?? '';

    // Update query
    $updateQuery = "UPDATE students SET 
                    name = '" . mysqli_real_escape_string($conn, $name) . "',
                    email = '" . mysqli_real_escape_string($conn, $email) . "',
                    phone_number = '" . mysqli_real_escape_string($conn, $phone_number) . "',
                    start_year = '" . mysqli_real_escape_string($conn, $start_year) . "',
                    end_year = '" . mysqli_real_escape_string($conn, $end_year) . "',
                    department = '" . mysqli_real_escape_string($conn, $department) . "'
                    WHERE rollno = '" . mysqli_real_escape_string($conn, $rollno) . "'";

    if (mysqli_query($conn, $updateQuery)) {
        header('Location: manage_students.php?status=success');
        exit;
    } else {
        $error_message = "Error updating student data: " . mysqli_error($conn);
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
        <h1>Edit Student</h1>

        <!-- Display error message if any -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Edit Student Form -->
        <form action="edit_student.php?rollno=<?= urlencode($rollno) ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($student['phone_number']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="start_year" class="form-label">Start Year</label>
                <select class="form-select" id="start_year" name="start_year" required>
                    <option value="">Select Start Year</option>
                    <?php for ($year = 2016; $year <= date('Y'); $year++): ?>
                        <option value="<?= $year ?>" <?= $student['start_year'] == $year ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="end_year" class="form-label">End Year</label>
                <select class="form-select" id="end_year" name="end_year" required>
                    <option value="">Select End Year</option>
                    <?php for ($year = date('Y'); $year <= 2030; $year++): ?>
                        <option value="<?= $year ?>" <?= $student['end_year'] == $year ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department" name="department" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept) ?>" <?= $student['department'] == $dept ? 'selected' : '' ?>><?= htmlspecialchars($dept) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Student</button>
        </form>
    </div>
</body>
</html>
