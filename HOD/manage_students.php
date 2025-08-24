<?php
session_start();
include '../db/db_connection.php'; // Include your database connection file

// Ensure the department is set in the session
if (!isset($_SESSION['department'])) {
    die("Unauthorized access.");
}

$hod_department_name = $_SESSION['department']; // Get department name from session

// Fetch department ID from the departments table
$sql = "SELECT department_id FROM departments WHERE department_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hod_department_name); // Bind department name as string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $department = $result->fetch_assoc();
    $hod_department_id = $department['department_id'];
} else {
    die("Invalid department.");
}

// Fetch students belonging to the HOD's department
$sql = "SELECT * FROM students WHERE department_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hod_department_id); // Bind department ID as integer
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f8f9fa;
    }
    .container {
        max-width: 1200px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
        color: #333;
    }
    .btn {
        display: inline-block;
        padding: 8px 15px;
        margin: 10px 5px;
        border: none;
        border-radius: 5px;
        color: white;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-add { background-color: #28a745; }
    .btn-search { background-color: #007bff; }
    .btn-edit { background-color: #ffc107; }
    .btn-delete { background-color: #dc3545; }
    .btn-back { background-color:rgb(39, 40, 41); } /* Back to Dashboard */
    .btn:hover { opacity: 0.8; }
    input[type="text"] {
        padding: 8px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .table-container {
        width: 100%;
        overflow-x: auto; /* Enables horizontal scrolling */
        margin-top: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap; /* Prevents text wrapping in table cells */
    }
    th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #007bff;
        color: white;
    }
    /* Responsive Styles */
    @media (max-width: 768px) {
        .table-container {
            border-radius: 0; /* Remove rounded corners for mobile */
        }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Manage Students</h2>
    <a href="add_students.php" class="btn btn-add">Add Student</a>
    <input type="text" id="searchInput" placeholder="Search by Roll No or Name">
    <button class="btn btn-search" onclick="searchStudent()">Search</button>
    <a href="hod_dashboard.php" class="btn btn-back">Back to Dashboard</a>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Start Year</th>
                    <th>End Year</th>
                    <th>Section</th>
                    <th>Phone</th>
                    <th>Parent Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTable">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['rollno']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_year']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_year']); ?></td>
                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
                    <td>
                        <a href="edit_student.php?rollno=<?php echo urlencode($row['rollno']); ?>" class="btn btn-edit">Edit</a>
                        <a href="delete_student.php?rollno=<?php echo urlencode($row['rollno']); ?>" class="btn btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function searchStudent() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let tableRows = document.getElementById("studentTable").getElementsByTagName("tr");

    for (let i = 0; i < tableRows.length; i++) {
        let rollNo = tableRows[i].getElementsByTagName("td")[0].innerText.toLowerCase();
        let name = tableRows[i].getElementsByTagName("td")[1].innerText.toLowerCase();

        if (rollNo.includes(input) || name.includes(input)) {
            tableRows[i].style.display = "";
        } else {
            tableRows[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>
