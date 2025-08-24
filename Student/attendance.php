<?php
session_start();
require '../db/db_connection.php'; // Include your database connection file

if (!isset($_SESSION['rollno'])) {
    die("Access Denied: Roll number not found in session.");
}

$rollno = $_SESSION['rollno'];
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
$sql = "SELECT * FROM attendance WHERE rollno = ? AND DATE_FORMAT(date, '%Y-%m') = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $rollno, $selected_month);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total days and attendance hours
$total_days = $result->num_rows;
$total_hours_present = 0;
$total_hours_absent = 0;

while ($row = $result->fetch_assoc()) {
    for ($i = 1; $i <= 5; $i++) {
        if ($row["hour_$i"] == 'Present') {
            $total_hours_present++;
        } elseif ($row["hour_$i"] == 'Absent') {
            $total_hours_absent++;
        }
    }
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container mt-4">
    <h2 class="text-center">Student Attendance Records</h2>
    
    <form method="POST" class="mb-3 d-flex justify-content-center">
        <input type="month" name="month" class="form-control w-auto me-2" value="<?php echo htmlspecialchars($selected_month); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="student_dashboard.php" class="btn btn-secondary ms-2">Back</a>
    </form>
    
    <div class="alert alert-info text-center">
        <p><strong>Total Days Calculated:</strong> <?php echo $total_days; ?></p>
        <p><strong>Total Hours Present:</strong> <?php echo $total_hours_present; ?></p>
        <p><strong>Total Hours Absent:</strong> <?php echo $total_hours_absent; ?></p>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Roll Number</th>
                    <th>Date</th>
                    <th>Hour 1</th>
                    <th>Hour 2</th>
                    <th>Hour 3</th>
                    <th>Hour 4</th>
                    <th>Hour 5</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { 
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rollno']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['hour_1']); ?></td>
                            <td><?php echo htmlspecialchars($row['hour_2']); ?></td>
                            <td><?php echo htmlspecialchars($row['hour_3']); ?></td>
                            <td><?php echo htmlspecialchars($row['hour_4']); ?></td>
                            <td><?php echo htmlspecialchars($row['hour_5']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr><td colspan="8" class="text-center">No records found</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
