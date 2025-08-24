<?php
session_start();
include('../db/db_connection.php');

$user_email = $_SESSION['email'];
// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Unauthorized access. You do not have permission to view this page.";
    exit;
}

// Fetch total staff count
$query_staff = "SELECT COUNT(*) as total_staff FROM staff";
$result_staff = mysqli_query($conn, $query_staff);
if (!$result_staff) {
    die("Error executing query: " . mysqli_error($conn));
}
$total_staff = mysqli_fetch_assoc($result_staff)['total_staff'];

// Fetch total students count
$query_students = "SELECT COUNT(*) as total_students FROM students";
$result_students = mysqli_query($conn, $query_students);
if (!$result_students) {
    die("Error executing query: " . mysqli_error($conn));
}
$total_students = mysqli_fetch_assoc($result_students)['total_students'];

// Fetch total pending staff count
$query_pending = "SELECT COUNT(*) as total_pending FROM staff WHERE status = 'Pending'";
$result_pending = mysqli_query($conn, $query_pending);
if (!$result_pending) {
    die("Error executing query: " . mysqli_error($conn));
}
$total_pending = mysqli_fetch_assoc($result_pending)['total_pending'];

// Fetch students by department for pie chart
$query_students_dept = "SELECT department, COUNT(*) as student_count FROM students GROUP BY department";
$result_students_dept = mysqli_query($conn, $query_students_dept);
if (!$result_students_dept) {
    die("Error executing query: " . mysqli_error($conn));
}

$students_dept_data = [];
while ($row = mysqli_fetch_assoc($result_students_dept)) {
    $students_dept_data[] = $row;
}

// Fetch staff by department for pie chart
$query_staff_dept = "SELECT department, COUNT(*) as staff_count FROM staff GROUP BY department";
$result_staff_dept = mysqli_query($conn, $query_staff_dept);
if (!$result_staff_dept) {
    die("Error executing query: " . mysqli_error($conn));
}

$staff_dept_data = [];
while ($row = mysqli_fetch_assoc($result_staff_dept)) {
    $staff_dept_data[] = $row;
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJv+3Ol5YxpmX5r+fM4eb+7lW3YxiM9KHZG1ol5RQjjTkz+tr6o8Txy+q4z3" crossorigin="anonymous">
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
            height: 4000vh;
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
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
        }

        .dashboard-cards {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .card {
            background-color: #4CAF50;
            color: white;
            width: 25%;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 2px solid #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .charts {
            display: flex;
            justify-content: space-between;
            flex-wrap: nowrap;
        }

        .chart-container {
            width: 48%;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            text-align: center;
            border: 2px solid #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        canvas {
            max-width: 100%;
            height: 200px;
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                width: 100%;
                padding-right: 20px !important;
            }

            .dashboard-cards {
                flex-direction: column;
                padding-right: 50px !important;
            }

            .card {
                width: 100%;
                margin-bottom: 20px;
                padding-right: 20px !important;
            }

            .charts {
                flex-direction: column;
                align-items: center;
            }

            .chart-container {
                width: 100%;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 992px) {
            .container {
                margin-left: 0;
            }

            .dashboard-cards {
                flex-direction: column;
            }

            .card {
                width: 100%;
                margin-bottom: 20px;
            }

            .charts {
                flex-direction: column;
            }

            .chart-container {
                width: 100%;
                margin-bottom: 20px;
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
    <h1>Welcome <?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?>!</h1>

    <!-- Dashboard Cards -->
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Staff</h3>
            <p><?= $total_staff; ?></p>
        </div>
        <div class="card">
            <h3>Total Students</h3>
            <p><?= $total_students; ?></p>
        </div>
        <div class="card">
            <h3>Total Pending Staff</h3>
            <p><?= $total_pending; ?></p>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts">
        <div class="chart-container">
            <h3>Students by Department</h3>
            <canvas id="studentsChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Staff by Department</h3>
            <canvas id="staffChart"></canvas>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.querySelector('.container').classList.toggle('shifted');
    }

    var studentsLabels = <?php echo json_encode(array_column($students_dept_data, 'department')); ?>;
    var studentsData = <?php echo json_encode(array_column($students_dept_data, 'student_count')); ?>;

    var staffLabels = <?php echo json_encode(array_column($staff_dept_data, 'department')); ?>;
    var staffData = <?php echo json_encode(array_column($staff_dept_data, 'staff_count')); ?>;

    new Chart(document.getElementById('studentsChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: studentsLabels,
            datasets: [{
                data: studentsData,
                backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#A133FF'] // Add more colors if needed
            }]
        }
    });

    new Chart(document.getElementById('staffChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: staffLabels,
            datasets: [{
                data: staffData,
                backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#A133FF'] // Add more colors if needed
            }]
        }
    });
</script>

</body>
</html>
