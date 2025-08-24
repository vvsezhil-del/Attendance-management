<?php
session_start();
include('../db/db_connection.php');

if (!isset($_GET['rollno'])) {
    die("Roll number is required.");
}

// Sanitize roll number
$rollno = htmlspecialchars($_GET['rollno']);

// Fetch student details using prepared statement
$stmt = $conn->prepare("SELECT name, department_id FROM students WHERE rollno = ?");
$stmt->bind_param("s", $rollno);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}

$department_id = $student['department_id'];

// Fetch subjects using prepared statement
$stmt = $conn->prepare("SELECT subject_id, subject_code FROM subjects WHERE department_id = ?");
$stmt->bind_param("s", $department_id);
$stmt->execute();
$subjects_result = $stmt->get_result();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semester = $_POST['semester'];
    $subject_id = $_POST['subject_code'];
    $cia_total = $_POST['total_cia_marks'];
    $sem_exam_marks = $_POST['sem_exam_marks'];
    $final_total = floatval($cia_total) + floatval($sem_exam_marks);

    // Server-side validation for semester exam marks (changed to > 75)
    if ($sem_exam_marks > 75) {
        echo "<script>alert('Semester Exam Marks must be less than or equal to 75!'); window.history.back();</script>";
        exit;
    }

    // Check for duplicate entry
    $stmt = $conn->prepare("SELECT * FROM semester_marks WHERE rollno = ? AND subject_code = ? AND semester = ?");
    $stmt->bind_param("ssi", $rollno, $subject_id, $semester);
    $stmt->execute();
    $check_result = $stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo "<script>alert('Marks already entered for this student, subject, and semester!'); window.history.back();</script>";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Insert data into semester_marks table
    $stmt = $conn->prepare("INSERT INTO semester_marks (rollno, semester, subject_code, cia_total, sem_exam_marks, final_total) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisddd", $rollno, $semester, $subject_id, $cia_total, $sem_exam_marks, $final_total);
    
    if ($stmt->execute()) {
        echo "<script>alert('Marks added successfully!'); window.location.href='manage_marks.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Marks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add Marks</h2>
        <form method="POST" class="mt-3" id="marksForm">
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
                <select name="semester" id="semester" class="form-control" required>
                    <option value="">Select Semester</option>
                    <?php for ($i = 1; $i <= 8; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Subject Code</label>
                <select name="subject_code" id="subject_code" class="form-control" required>
                    <option value="">Select Subject</option>
                    <?php while ($row = $subjects_result->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['subject_id']); ?>">
                            <?php echo htmlspecialchars($row['subject_code']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Total CIA Marks</label>
                <input type="text" name="total_cia_marks" id="total_cia_marks" class="form-control" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Semester Exam Marks (Max: 75)</label>
                <input type="number" name="sem_exam_marks" id="sem_exam_marks" class="form-control" 
                       required step="0.01" min="0" max="75">
            </div>
            <div class="mb-3">
                <label class="form-label">Final Total</label>
                <input type="text" id="final_total" class="form-control" readonly required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Marks</button>
            <a href="manage_marks.php" class="btn btn-secondary">Back</a>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#semester, #subject_code').change(function() {
                var semester = $('#semester').val();
                var subject_code = $('#subject_code').val();
                var rollno = "<?php echo htmlspecialchars($rollno); ?>";

                if (semester && subject_code) {
                    $.ajax({
                        url: 'fetch_cia_marks.php',
                        type: 'POST',
                        data: { rollno: rollno, semester: semester, subject_code: subject_code },
                        success: function(response) {
                            $('#total_cia_marks').val(response);
                            calculateFinalTotal();
                        },
                        error: function() {
                            alert('Error fetching CIA marks');
                        }
                    });
                } else {
                    $('#total_cia_marks').val('');
                    calculateFinalTotal();
                }
            });

            $('#sem_exam_marks').on('input', function() {
                calculateFinalTotal();
            });

            $('#marksForm').on('submit', function(e) {
                var semExamMarks = parseFloat($('#sem_exam_marks').val());
                if (semExamMarks > 75) {
                    e.preventDefault();
                    alert('Semester Exam Marks must be less than or equal to 75!');
                    return false;
                }
            });

            function calculateFinalTotal() {
                var cia_total = parseFloat($('#total_cia_marks').val()) || 0;
                var sem_exam_marks = parseFloat($('#sem_exam_marks').val()) || 0;
                if (sem_exam_marks > 75) {
                    $('#sem_exam_marks').val('75');
                    sem_exam_marks = 75;
                }
                $('#final_total').val((cia_total + sem_exam_marks).toFixed(2));
            }
        });
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>