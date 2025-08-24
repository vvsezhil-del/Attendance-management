<?php
session_start();
include('../db/db_connection.php'); // Include database connection file

// Check if the student is logged in
if (!isset($_SESSION['rollno'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>You must be logged in to view your progress.</div></div>";
    exit;
}

$rollno = $_SESSION['rollno']; // Fetch student's roll number from session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="card shadow p-4">
            <h2 class="text-center text-primary">My Progress</h2>

            <div class="mb-3">
                <label for="semester" class="form-label">Select Semester:</label>
                <select id="semester" name="semester" class="form-select">
                    <option value="">-- Select Semester --</option>
                    <?php for ($i = 1; $i <= 8; $i++) { ?>
                        <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                    <?php } ?>
                </select> <br>
                <a href="student_dashboard.php" class="btn btn-secondary ms-2">Back</a>
            </div>

            <div id="marks-data" class="mt-4"></div> <!-- Marks data will be displayed here -->
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#semester").change(function() {
                var semester = $(this).val();
                if (semester !== "") {
                    $.ajax({
                        url: "fetch_marks.php",
                        method: "POST",
                        data: { semester: semester },
                        success: function(response) {
                            $("#marks-data").html(response);
                        }
                    });
                } else {
                    $("#marks-data").html("");
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
