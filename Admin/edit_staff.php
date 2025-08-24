<?php
session_start();
include('../db/db_connection.php');

// Check if the email is provided
if (!isset($_GET['email'])) {
    die("Invalid Request");
}

$email = mysqli_real_escape_string($conn, $_GET['email']);

// Fetch the staff details
$query = "SELECT * FROM staff WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Staff not found");
}

$staff = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Update the staff record
    $updateQuery = "UPDATE staff SET name = '$name', phone_number = '$phone', department = '$department', role = '$role' WHERE email = '$email'";
    if (mysqli_query($conn, $updateQuery)) {
        header("Location: manage_staff.php?success=updated");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Staff</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($staff['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($staff['phone_number']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" id="department" name="department" value="<?= htmlspecialchars($staff['department']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <input type="text" class="form-control" id="role" name="role" value="<?= htmlspecialchars($staff['role']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="manage_staff.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
