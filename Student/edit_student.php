<?php
session_start();
include('../db/db_connection.php');


$student_id = $_SESSION['rollno'];

// Fetch student details
$query = "SELECT * FROM students WHERE rollno = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $department_id = mysqli_real_escape_string($conn, $_POST['department_id']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $parent_name = mysqli_real_escape_string($conn, $_POST['parent_name']);

    $update_query = "UPDATE students SET 
        name = '$name', 
        email = '$email', 
        department_id = '$department_id', 
        section = '$section', 
        phone_number = '$phone_number', 
        parent_name = '$parent_name' 
        WHERE rollno = '$student_id'";

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        header("Location: student_dashboard.php");
        exit;
    } else {
        $error_message = "Error updating record: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f4f4;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #ccc;
        }
        .btn-save {
            background: #4C489D;
            color: white;
            border-radius: 25px;
            padding: 10px;
            width: 100%;
        }
        .btn-save:hover {
            background: #6A679E;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Edit Student Details</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="edit_student.php">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($student['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Department ID</label>
                <input type="text" name="department_id" class="form-control" value="<?= htmlspecialchars($student['department_id']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Section</label>
                <input type="text" name="section" class="form-control" value="<?= htmlspecialchars($student['section']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($student['phone_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Parent Name</label>
                <input type="text" name="parent_name" class="form-control" value="<?= htmlspecialchars($student['parent_name']); ?>" required>
            </div>
            <button type="submit" class="btn btn-save">Save Changes</button>
            <a href="student_dashboard.php" class="btn btn-secondary mt-3 w-100">Cancel</a>
        </form>
    </div>
</body>
</html>
