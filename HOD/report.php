<?php
include('../db/db_connection.php'); // Database connection

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if rollno is provided
if (isset($_GET['rollno'])) {
    $rollno = $_GET['rollno'];

    // Fetch student details
    $student_sql = "SELECT name FROM students WHERE rollno = ?";
    $student_stmt = $conn->prepare($student_sql);
    $student_stmt->bind_param("s", $rollno);
    $student_stmt->execute();
    $student_result = $student_stmt->get_result();

    if ($student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc();
        $student_name = $student['name'];
    } else {
        $student_name = "Unknown Student";
    }

    // Fetch attendance report
    $report_sql = "SELECT date, hour_1, hour_2, hour_3, hour_4, hour_5 FROM attendance WHERE rollno = ? ORDER BY date DESC";
    $report_stmt = $conn->prepare($report_sql);
    $report_stmt->bind_param("s", $rollno);
    $report_stmt->execute();
    $report_result = $report_stmt->get_result();
} else {
    die("Roll number not provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center text-primary mb-4">Attendance Report</h1>
        <h2 class="text-center text-secondary">Student Name: <?php echo htmlspecialchars($student_name); ?> (Roll No: <?php echo htmlspecialchars($rollno); ?>)</h2>
        
        <div class="text-center my-3">
            <button class="btn btn-secondary" onclick="goBack()">Back</button>
        </div>

        <?php if (isset($report_result) && $report_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Hour 1</th>
                            <th>Hour 2</th>
                            <th>Hour 3</th>
                            <th>Hour 4</th>
                            <th>Hour 5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $report_result->fetch_assoc()): ?>
                            <tr class="attendance-row">
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <?php for ($hour = 1; $hour <= 5; $hour++): ?>
                                    <td><?php echo $row['hour_' . $hour] == 1 ? 'Present' : 'Absent'; ?></td>
                                <?php endfor; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center my-4">
                <button class="btn btn-primary" onclick="showAttendanceCard()">Calculate Attendance</button>
            </div>

            <div class="card mx-auto" id="attendanceCard" style="max-width: 500px; display: none;">
                <div class="card-body">
                    <h5 class="card-title text-center">Calculate Attendance</h5>
                    <form>
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Start Date:</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">End Date:</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-success" onclick="calculateAttendance()">Calculate</button>
                        </div>
                        <p id="attendanceResult" class="mt-3 text-center text-info"></p>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-danger">No attendance records found for this student.</p>
        <?php endif; ?>
    </div>

    <script>
        function goBack() {
            window.location.href = 'manage_attendance.php'; // Change to the actual search page
        }

        function showAttendanceCard() {
            document.getElementById('attendanceCard').style.display = 'block';
        }

        function calculateAttendance() {
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(document.getElementById('endDate').value);

            if (isNaN(startDate) || isNaN(endDate) || startDate > endDate) {
                alert('Please select valid start and end dates.');
                return;
            }

            const rows = document.querySelectorAll('table .attendance-row');
            let totalDays = 0, presentDays = 0;

            rows.forEach(row => {
                const date = new Date(row.cells[0].innerText);
                if (date >= startDate && date <= endDate) {
                    totalDays++;
                    for (let i = 1; i <= 5; i++) {
                        if (row.cells[i].innerText === 'Present') {
                            presentDays++;
                        }
                    }
                }
            });

            const attendancePercentage = totalDays ? ((presentDays / (totalDays * 5)) * 100).toFixed(2) : 0;
            document.getElementById('attendanceResult').innerText = `Total Working Days: ${totalDays}, Present Hours: ${presentDays}, Attendance: ${attendancePercentage}%`;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
