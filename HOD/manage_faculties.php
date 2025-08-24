<?php
session_start();
require_once '../db/db_connection.php'; // Include your database connection file

// Check if the user is logged in and has a department
if (!isset($_SESSION['department'])) {
    echo "Access denied. Please log in.";
    exit;
}

$logged_in_department = $_SESSION['department'];

// Handle status update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $user_id = $_POST['user_id'];
    $new_status = $_POST['status'];

    if (!empty($user_id) && !empty($new_status)) {
        $query = "UPDATE staff SET status = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("si", $new_status, $user_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Status updated successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to update the status. Please try again.";
            }

            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Failed to prepare the query: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Invalid input. Please provide valid data.";
    }
}

// Handle the search query
$search_query = "";
if (isset($_POST['search']) && !empty($_POST['search_query'])) {
    $search_query = $_POST['search_query'];
}

// Prepare the SQL query to display assistant professors
$query = "
    SELECT u.user_id, u.name, u.email, u.status, d.department_name 
    FROM staff u
    INNER JOIN Departments d ON u.department = d.department_name
    WHERE u.role = 'Asst.professor' AND d.department_name = ?
";

if (!empty($search_query)) {
    $query .= " AND (u.name LIKE ? OR u.email LIKE ?)";
}

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

// Bind parameters based on whether there's a search query
if (!empty($search_query)) {
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("sss", $logged_in_department, $search_param, $search_param);
} else {
    $stmt->bind_param("s", $logged_in_department);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query execution failed: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculties</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            color: #000;
        }

        .btn-back {
            background-color: black;
            color: white;
        }

        .btn-back:hover {
            border: 1px solid black;
        }

        .table th {
            background-color: #000;
            color: #fff;
            border: 1px solid #000;
        }

        .table td {
            border: 1px solid #000;
        }

        .action-buttons .btn {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <!-- Add Faculty and Search -->
        <div class="d-flex justify-content-between mb-3">
            <a href="hod_dashboard.php" class="btn btn-back">Back to Dashboard</a>
            <form method="POST" class="d-flex w-50">
                <input type="text" name="search_query" class="form-control"
                    placeholder="Search faculty by name or email..."
                    value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" name="search" class="btn btn-dark ms-2">Search</button>
            </form>
        </div>

        <!-- Display Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Faculties Table -->
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Faculty ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                        <select name="status" class="form-select">
                                            <option value="approved" <?php if ($row['status'] == 'approved')
                                                echo 'selected'; ?>>
                                                Approved</option>
                                            <option value="pending" <?php if ($row['status'] == 'pending')
                                                echo 'selected'; ?>>
                                                Pending</option>
                                        </select>
                                        <button type="submit" name="update_status"
                                            class="btn btn-success btn-sm ms-2">Update</button>
                                    </form>
                                </td>
                                <td class="action-buttons">
                                   
                                    <a href="delete_faculty.php?user_id=<?php echo $row['user_id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this faculty?');">Delete</a>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">No faculties found in your department.</p>
        <?php endif; ?>

        <?php
        $stmt->close();
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>