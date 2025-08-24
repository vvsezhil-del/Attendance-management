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

// Get the current year
$currentYear = date('Y');

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
$rollno = $_GET['rollno'] ?? '';
$start_year = $_GET['start_year'] ?? '';
$end_year = $_GET['end_year'] ?? '';
$department = $_GET['department'] ?? '';

// Build the SQL query with filters
$query = "SELECT * FROM students WHERE 1=1";

if (!empty($rollno)) {
    $query .= " AND rollno LIKE '%" . mysqli_real_escape_string($conn, $rollno) . "%'";
}

if (!empty($start_year)) {
    $query .= " AND start_year = '" . mysqli_real_escape_string($conn, $start_year) . "'";
}

if (!empty($end_year)) {
    $query .= " AND end_year = '" . mysqli_real_escape_string($conn, $end_year) . "'";
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJv+3Ol5YxpmX5r+fM4eb+7lW3YxiM9KHZG1ol5RQjjTkz+tr6o8Txy+q4z3" crossorigin="anonymous">
  
   <style>
    body {
    font-family: Arial, sans-serif;
    margin: 10;
    padding: 10;
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
    margin-left: 200px;
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
        left: -100%; /* Initially off-screen */
        transform: translateX(0);
        transition: transform 0.3s ease;
        padding-top: 10px;
        padding-left: 40px;
    }

    .sidebar.open {
        transform: translateX(100%); /* Bring sidebar on-screen */
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
        <a href="../Login/logout.php">Logout</a>
    </div>



<!-- Filter Form -->
  <form action="manage_students.php" method="GET">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="rollno" class="form-label">Roll No</label>
                    <input type="text" class="form-control" id="rollno" name="rollno" placeholder="Enter Roll No" value="<?= htmlspecialchars($rollno) ?>">
                </div>
                <div class="col-md-3">
                    <label for="start_year" class="form-label">Start Year</label>
                    <select class="form-select" id="start_year" name="start_year">
                        <option value="">Select Start Year</option>
                        <?php for ($year = 2016; $year <= $currentYear; $year++): ?>
                            <option value="<?= $year ?>" <?= $start_year == $year ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="end_year" class="form-label">End Year</label>
                    <select class="form-select" id="end_year" name="end_year">
                        <option value="">Select End Year</option>
                        <?php for ($year = $currentYear; $year <= 2030; $year++): ?>
                            <option value="<?= $year ?>" <?= $end_year == $year ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
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
        </form>

        <!-- Students Table -->
        <div class="table-container mt-5">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Start Year</th>
                        <th>End Year</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['rollno']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                <td><?= htmlspecialchars($row['start_year']) ?></td>
                                <td><?= htmlspecialchars($row['end_year']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td>
                                    <a href="edit_student.php?rollno=<?= urlencode($row['rollno']) ?>" class="btn btn-primary">Edit</a>
                                    <a href="delete_student.php?rollno=<?= urlencode($row['rollno']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No students found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
   function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.querySelector('.container').classList.toggle('shifted');
    }
    </script>
    </body>
</html>
