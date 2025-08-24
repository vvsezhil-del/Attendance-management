<?php
session_start();
include('../db/db_connection.php'); // Database connection

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$department = $_SESSION['department']; // Get department from session

// Fetch department_id for the department
$dept_query = "SELECT department_id FROM departments WHERE department_name = ?";
$dept_stmt = $conn->prepare($dept_query);
if (!$dept_stmt) {
    die("Prepare error: " . $conn->error);
}
$dept_stmt->bind_param("s", $department);
$dept_stmt->execute();
$dept_result = $dept_stmt->get_result();

if ($dept_result->num_rows > 0) {
    $dept_row = $dept_result->fetch_assoc();
    $department_id = $dept_row['department_id'];
} else {
    die("Department not found.");
}

// Fetch distinct years
$years_result = $conn->query("SELECT DISTINCT start_year FROM students WHERE department_id = '$department_id' ORDER BY start_year DESC");

// Fetch distinct sections
$sections_result = $conn->query("SELECT DISTINCT section FROM students WHERE department_id = '$department_id'");

// Fetch students based on year and section with parent phone number
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['year'], $_POST['section'])) {
    $year = $_POST['year'];
    $section = $_POST['section'];

    // Modified query to join with parents table
    $sql = "SELECT s.rollno, s.name, p.phone_number 
            FROM students s 
            LEFT JOIN parents p ON s.rollno = p.rollno 
            WHERE s.department_id = '$department_id' 
            AND s.start_year = '$year' 
            AND s.section = '$section'";
    $students_result = $conn->query($sql);
}

// Get today's date
$date = date('Y-m-d');

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    $attendance_data = $_POST['attendance'];

    foreach ($attendance_data as $rollno => $hours) {
        // Check if attendance already exists for this student on the given date
        $check_sql = "SELECT rollno FROM attendance WHERE rollno = ? AND date = ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            die('Prepare error: ' . $conn->error);
        }
        $check_stmt->bind_param("ss", $rollno, $date);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing record if attendance exists
            $update_sql = "UPDATE attendance SET hour_1 = ?, hour_2 = ?, hour_3 = ?, hour_4 = ?, hour_5 = ? WHERE rollno = ? AND date = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                die('Prepare error: ' . $conn->error);
            }
            $update_stmt->bind_param("iiiiiss", $hours[1], $hours[2], $hours[3], $hours[4], $hours[5], $rollno, $date);
            $update_stmt->execute();
        } else {
            // Insert new record if attendance does not exist for this student
            $insert_sql = "INSERT INTO attendance (rollno, date, hour_1, hour_2, hour_3, hour_4, hour_5) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                die('Prepare error: ' . $conn->error);
            }
            $insert_stmt->bind_param("ssiiiii", $rollno, $date, $hours[1], $hours[2], $hours[3], $hours[4], $hours[5]);
            $insert_stmt->execute();
        }
    }
    echo "Attendance recorded successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table-container {
            margin-top: 20px;
        }
        .btn-back { 
            background-color: black;
            color: white;
        }
        .btn-back:hover{
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Mark Attendance</h1>

        <div class="form-container mx-auto">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <label for="year" class="form-label">Year:</label>
                        <select name="year" id="year" class="form-select" required>
                            <?php while ($year_row = $years_result->fetch_assoc()): ?>
                                <option value="<?php echo $year_row['start_year']; ?>" <?php echo isset($_POST['year']) && $_POST['year'] == $year_row['start_year'] ? 'selected' : ''; ?>>
                                    <?php echo $year_row['start_year']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="section" class="form-label">Section:</label>
                        <select name="section" id="section" class="form-select" required>
                            <?php while ($section_row = $sections_result->fetch_assoc()): ?>
                                <option value="<?php echo $section_row['section']; ?>" <?php echo isset($_POST['section']) && $_POST['section'] == $section_row['section'] ? 'selected' : ''; ?>>
                                    <?php echo $section_row['section']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Load Students</button> <br> <br>
                <a href="professor_dashboard.php" class="btn btn-back">Back to Dashboard</a>
            </form>
        </div>

        <?php if (isset($students_result) && $students_result->num_rows > 0): ?>
            <div class="table-container">
                <form method="POST">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mt-4">
                            <thead class="table-dark">
                                <tr>
                                    <th>Roll Number</th>
                                    <th>Student Name</th>
                                    <?php for ($hour = 1; $hour <= 5; $hour++): ?>
                                        <th>Hour <?php echo $hour; ?></th>
                                    <?php endfor; ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $students_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['rollno']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <?php
                                            $attendance_query = "SELECT hour_1, hour_2, hour_3, hour_4, hour_5 FROM attendance WHERE rollno = ? AND date = ?";
                                            $attendance_stmt = $conn->prepare($attendance_query);
                                            $attendance_stmt->bind_param("ss", $row['rollno'], $date);
                                            $attendance_stmt->execute();
                                            $attendance_result = $attendance_stmt->get_result();

                                            $attendance_hours = ($attendance_result->num_rows > 0) ? $attendance_result->fetch_assoc() : ['hour_1' => 0, 'hour_2' => 0, 'hour_3' => 0, 'hour_4' => 0, 'hour_5' => 0];
                                        ?>
                                        <?php for ($hour = 1; $hour <= 5; $hour++): ?>
                                            <td>
                                                <select name="attendance[<?php echo $row['rollno']; ?>][<?php echo $hour; ?>]" class="form-select" required>
                                                    <option value="1" <?php echo $attendance_hours['hour_' . $hour] == 1 ? 'selected' : ''; ?>>Present</option>
                                                    <option value="0" <?php echo $attendance_hours['hour_' . $hour] == 0 ? 'selected' : ''; ?>>Absent</option>
                                                </select>
                                            </td>
                                        <?php endfor; ?>
                                        <td>
                                            <a href="report.php?rollno=<?php echo urlencode($row['rollno']); ?>" class="btn btn-danger btn-sm">Report</a>
                                            <?php
                                                // Calculate total absent hours for the day
                                                $absent_hours = 0;
                                                for ($hour = 1; $hour <= 5; $hour++) {
                                                    if ($attendance_hours['hour_' . $hour] == 0) {
                                                        $absent_hours++;
                                                    }
                                                }
                                                $is_disabled = ($absent_hours > 3) ? '' : 'disabled';
                                                $phone_number = !empty($row['phone_number']) ? $row['phone_number'] : '12345678901'; // Fallback number if parent phone not found
                                            ?>
                                            <a href="https://wa.me/<?php echo htmlspecialchars($phone_number); ?>?text=Your%20son/daughter%20was%20absent%20today" 
                                               class="btn btn-warning btn-sm <?php echo $is_disabled; ?>" 
                                               <?php echo $is_disabled ? '' : 'onclick="return false;"' ?>>
                                                Send Message
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="submit_attendance" class="btn btn-success w-100 mt-3">Submit Attendance</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>