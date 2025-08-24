<?php
session_start();
include('../db/db_connection.php');

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit();
}

// Handle adding a new department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $department_name = mysqli_real_escape_string($conn, $_POST['department_name']);
    
    // Insert the new department into the database
    $query = "INSERT INTO departments (department_name) VALUES ('$department_name')";
    if (mysqli_query($conn, $query)) {
        header("Location: manage_department.php?success=added");
        exit();
    } else {
        echo "Error adding department: " . mysqli_error($conn);
    }
}

// Handle deleting a department
if (isset($_GET['delete_id'])) {
    $department_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Delete the department from the database
    $query = "DELETE FROM departments WHERE department_id = '$department_id'";
    if (mysqli_query($conn, $query)) {
        header("Location: manage_department.php?success=deleted");
        exit();
    } else {
        echo "Error deleting department: " . mysqli_error($conn);
    }
}

// Fetch all departments from the database
$query = "SELECT * FROM departments";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Manage Departments</h1>
    
    <!-- Success or Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_GET['success'] == 'added' ? 'Department added successfully.' : 'Department deleted successfully.'; ?>
        </div>
    <?php endif; ?>

    <!-- Add Department Form -->
    <h3>Add New Department</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="department_name" class="form-label">Department Name</label>
            <input type="text" class="form-control" id="department_name" name="department_name" required>
        </div>
        <button type="submit" name="add_department" class="btn btn-primary">Add Department</button>
    </form>
    
    <!-- Departments Table -->
    <h3 class="mt-5">Existing Departments</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Department Name</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['department_id']; ?></td>
                    <td><?php echo $row['department_name']; ?></td>
                    <td>
                        <!-- Delete Department -->
                        <a href="manage_department.php?delete_id=<?php echo $row['department_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
