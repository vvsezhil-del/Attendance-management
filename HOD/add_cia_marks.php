<?php
session_start();
include('../db/db_connection.php');

if (!isset($_GET['rollno'])) {
    die("Roll number is required.");
}

$rollno = mysqli_real_escape_string($conn, $_GET['rollno']);

// Fetch student details
$student_query = "SELECT name, department_id FROM students WHERE rollno = '$rollno'";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);

if (!$student) {
    die("Student not found.");
}

$department_id = $student['department_id'];

// Fetch subjects
$subjects_query = "SELECT subject_id, subject_code FROM subjects WHERE department_id = '$department_id'";
$subjects_result = mysqli_query($conn, $subjects_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $subject_id = mysqli_real_escape_string($conn, $_POST['subject_code']);

    $cia_1 = isset($_POST['cia1_marks']) ? (float)$_POST['cia1_marks'] : 0;
    $cia_2 = isset($_POST['cia2_marks']) ? (float)$_POST['cia2_marks'] : 0;
    $cia_3 = isset($_POST['cia3_marks']) ? (float)$_POST['cia3_marks'] : 0;

    // Server-side validation: Ensure marks do not exceed 25
    if ($cia_1 > 25 || $cia_2 > 25 || $cia_3 > 25) {
        echo "<script>alert('Marks cannot be greater than 25'); window.history.back();</script>";
        exit;
    }

    // Check for duplicate entry
    $check_query = "SELECT * FROM cia_marks WHERE rollno='$rollno' AND subject_code='$subject_id' AND semester='$semester'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Marks already entered for this student, subject, and semester!'); window.history.back();</script>";
        exit;
    }

    // Calculate total marks
    $total = round(($cia_1 + $cia_2 + $cia_3) / 3, 2);

    
    // Insert data into cia_marks table
    $insert_query = "INSERT INTO cia_marks (rollno, semester, subject_code, cia1_marks, cia2_marks, cia3_marks, total_cia_marks) 
                     VALUES ('$rollno', '$semester', '$subject_id', '$cia_1', '$cia_2', '$cia_3', '$total')";

    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Marks added successfully!'); window.location.href='manage_marks.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Marks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function calculateTotal() {
            let cia1 = parseFloat(document.getElementById("cia_1").value) || 0;
            let cia2 = parseFloat(document.getElementById("cia_2").value) || 0;
            let cia3 = parseFloat(document.getElementById("cia_3").value) || 0;
            let total = (cia1 + cia2 + cia3) / 3;
            document.getElementById("total").value = total.toFixed(2);
        }

        function validateMarks(input) {
            if (input.value > 25) {
                alert("Marks cannot be greater than 25");
                input.value = "";
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add Marks</h2>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Roll Number</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($rollno); ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Student Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-control" required>
                    <option value="">Select Semester</option>
                    <?php for ($i = 1; $i <= 8; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Subject Code</label>
                <select name="subject_code" class="form-control" required>
                    <option value="">Select Subject</option>
                    <?php while ($row = mysqli_fetch_assoc($subjects_result)) { ?>
                        <option value="<?php echo htmlspecialchars($row['subject_id']); ?>">
                            <?php echo htmlspecialchars($row['subject_code']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">CIA 1 Marks</label>
                <input type="number" class="form-control" id="cia_1" name="cia1_marks" oninput="calculateTotal(); validateMarks(this);" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CIA 2 Marks</label>
                <input type="number" class="form-control" id="cia_2" name="cia2_marks" oninput="calculateTotal(); validateMarks(this);" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CIA 3 Marks</label>
                <input type="number" class="form-control" id="cia_3" name="cia3_marks" oninput="calculateTotal(); validateMarks(this);" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total (Average of CIA 1, 2, 3)</label>
                <input type="text" class="form-control" id="total" name="total" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Submit Marks</button>
            <a href="manage_marks.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>
