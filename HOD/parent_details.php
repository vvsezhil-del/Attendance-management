<?php
session_start();
include('../db/db_connection.php');

// Check if student data exists in session
if (!isset($_SESSION['student_data'])) {
    header("Location: add_student.php"); // Redirect back if no data
    exit();
}

$student_data = $_SESSION['student_data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $relationship = $_POST['relationship'];
    $rollno = $student_data['rollno'];
    $parent_name = $student_data['parent_name'];

    // Insert into parents table
    $insert_query = "INSERT INTO parents (rollno, parent_name, phone_number, email, relationship, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param(
        'issss',
        $rollno,
        $parent_name,
        $phone_number,
        $email,
        $relationship
    );

    if ($insert_stmt->execute()) {
        // Clear the session data
        unset($_SESSION['student_data']);
        echo "<script>
                alert('Parent details added successfully!');
                window.location.href = 'hod_dashboard.php';
              </script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error adding parent details: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Add Parent Details</h2>
    <form method="POST" class="row g-3 mb-5">
        <div class="col-md-6">
            <label for="rollno" class="form-label">Student Roll Number</label>
            <input type="text" class="form-control" value="<?php echo $student_data['rollno']; ?>" disabled>
        </div>
        <div class="col-md-6">
            <label for="parent_name" class="form-label">Parent Name</label>
            <input type="text" class="form-control" value="<?php echo $student_data['parent_name']; ?>" disabled>
        </div>
        <div class="col-md-6">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" value="" required>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo $student_data['email']; ?>" required>
        </div>
        <div class="col-md-6">
            <label for="relationship" class="form-label">Relationship</label>
            <select name="relationship" id="relationship" class="form-control" required>
                <option value="">Select Relationship</option>
                <option value="Father">Father</option>
                <option value="Mother">Mother</option>
                <option value="Guardian">Guardian</option>
            </select>
        </div>
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary">Save Parent Details</button>
            <a href="hod_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>