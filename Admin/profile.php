<?php
session_start();
include('../db/db_connection.php');

// Check if the session 'email' is set
if (!isset($_SESSION['email'])) {
    die("Error: User not logged in.");
}

$user_email = $_SESSION['email'];

// Fetch user profile data based on the session email
$query_user = "SELECT * FROM staff WHERE email = '$user_email'";
$result_user = mysqli_query($conn, $query_user);

if (!$result_user) {
    die("Error fetching user data: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result_user);

// Check if user was found
if (!$user) {
    die("User not found.");
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated form values
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    // Update the user profile in the database
    $update_query = "
        UPDATE staff 
        SET name = '$name', role = '$role', department = '$department', status = '$status', phone_number = '$phone_number' 
        WHERE email = '$user_email'
    ";

    if (mysqli_query($conn, $update_query)) {
        // Update successful, reload the page to reflect changes
        header('Location: profile.php');
        exit();
    } else {
        // Error updating profile
        $error_message = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            background-color: #333;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            padding-left: 20px;
        }
        .toggle-bar {
            font-size: 30px;
            padding-right: 20px;
            cursor: pointer;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #444;
            color: white;
            position: fixed;
            top: 0;
            left: -250px;
            transition: left 0.3s;
            padding-top: 70px;
            z-index: 999;
        }
        .sidebar.open {
            left: 0;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #555;
        }
        .container {
            margin-top: 70px;
            margin-left: 450px;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
        }
        .profile-card {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            border: 2px solid #ddd;
            width: 60%;
        }
        .profile-card h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-info {
            display: flex;
            flex-wrap: wrap;
        }
        .profile-info div {
            width: 50%;
            padding: 10px;
        }
        .profile-info div label {
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding-right: 20px !important;
            }
            .profile-info div {
                width: 100%;
                margin-bottom: 10px;
            }
            .profile-card {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo">Admin Panel</div>
    <div class="toggle-bar" onclick="toggleSidebar()">&#9776;</div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="manage_students.php">Manage Students</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="manage_department.php">Manage Department</a>
    <a href="../Login/logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="container">
    <h1>Profile</h1>

    <!-- Error Message -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- Profile Card -->
    <div class="profile-card">
        <h3>Profile Information</h3>
        <div class="profile-info">
            <div>
                <label>Name:</label>
                <p><?= htmlspecialchars($user['name']); ?></p>
            </div>
            <div>
                <label>Email:</label>
                <p><?= htmlspecialchars($user['email']); ?></p>
            </div>
            <div>
                <label>Role:</label>
                <p><?= htmlspecialchars($user['role']); ?></p>
            </div>
            <div>
                <label>Department:</label>
                <p><?= htmlspecialchars($user['department']); ?></p>
            </div>
            <div>
                <label>Status:</label>
                <p><?= htmlspecialchars($user['status']); ?></p>
            </div>
            <div>
                <label>Phone Number:</label>
                <p><?= htmlspecialchars($user['phone_number']); ?></p>
            </div>
        </div>
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#updateModal">Update</button>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" name="role" value="<?= htmlspecialchars($user['role']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department" value="<?= htmlspecialchars($user['department']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status" name="status" value="<?= htmlspecialchars($user['status']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }
</script>

</body>
</html>
