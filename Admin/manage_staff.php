<?php
session_start();
include('../db/db_connection.php');

// Fetch total staff and students data for header/sidebar (if required)
$query_staff = "SELECT COUNT(*) as total_staff FROM staff";
$result_staff = mysqli_query($conn, $query_staff);
$total_staff = mysqli_fetch_assoc($result_staff)['total_staff'] ?? 0;

$query_students = "SELECT COUNT(*) as total_students FROM students";
$result_students = mysqli_query($conn, $query_students);
$total_students = mysqli_fetch_assoc($result_students)['total_students'] ?? 0;

// Fetch departments from the departments table
$departmentsQuery = "SELECT department_name FROM departments";
$departmentsResult = mysqli_query($conn, $departmentsQuery);
$departments = [];
if ($departmentsResult) {
    while ($row = mysqli_fetch_assoc($departmentsResult)) {
        $departments[] = $row['department_name'];
    }
}

// Initialize filter variables
$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$department = $_GET['department'] ?? '';

// Build the SQL query with filters
$query = "SELECT * FROM staff WHERE 1=1";

if (!empty($name)) {
    $query .= " AND name LIKE '%" . mysqli_real_escape_string($conn, $name) . "%'";
}

if (!empty($email)) {
    $query .= " AND email LIKE '%" . mysqli_real_escape_string($conn, $email) . "%'";
}

if (!empty($department)) {
    $query .= " AND department = '" . mysqli_real_escape_string($conn, $department) . "'";
}

// Execute the query
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .header {
            background-color: #343a40;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .header .toggle-bar {
            cursor: pointer;
            font-size: 1.5rem;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 200px;
            height: 100%;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            font-size: 1rem;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .container {
            margin-left: 220px;
            padding: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
        /* Add necessary media queries for the responsive sidebar */
        @media (max-width: 768px) {
            /* Sidebar and header adjustments for mobile view */
            .sidebar {
                width: 90%;
                height: auto;
                position: absolute;
                top: 0;
                left: -100%;
                transform: translateX(0);
                transition: transform 0.3s ease;
                padding-top: 10px;
                padding-left: 40px;
            }
            .sidebar.open {
                transform: translateX(100%);
            }
            .container {
                margin-left: 0;
            }
            .header {
                padding: 10px;
                justify-content: space-between;
            }
            .header .toggle-bar {
                font-size: 1.2rem;
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
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1>Manage Staff</h1>

        <!-- Filter Form -->
        <form action="manage_staff.php" method="GET">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="<?= htmlspecialchars($name) ?>">
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" value="<?= htmlspecialchars($email) ?>">
                </div>
                <div class="col-md-4">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept) ?>" <?= $department == $dept ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <br>
            <a href="add_staff.php" class="btn btn-success mt-3">Add Staff</a>
        </form>

        <!-- Staff Table -->
        <div class="table-container mt-5">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td>
                                    <select class="form-select status-dropdown" data-email="<?= htmlspecialchars($row['email']) ?>">
                                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="rejected" <?= $row['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                        <option value="approved" <?= $row['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                                    </select>
                                </td>
                                <td>
                                    <a href="edit_staff.php?email=<?= urlencode($row['email']) ?>" class="btn btn-primary">Edit</a>
                                    <a href="delete_staff.php?email=<?= urlencode($row['email']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No staff found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Toggle sidebar for mobile view
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.querySelector('.container').classList.toggle('shifted');
        }

        // Handle status change via AJAX
        document.querySelectorAll('.status-dropdown').forEach(function(select) {
            select.addEventListener('change', function() {
                const email = this.getAttribute('data-email');
                const status = this.value;

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'approve_staff.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        alert('Status updated successfully!');
                    } else {
                        alert('Error updating status.');
                    }
                };
                xhr.send('email=' + encodeURIComponent(email) + '&status=' + encodeURIComponent(status));
            });
        });
    </script>
</body>
</html>
