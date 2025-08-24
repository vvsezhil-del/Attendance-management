<?php
session_start();
include('../db/db_connection.php');

$name = $_SESSION['name'];
$role = $_SESSION['role'];
$department = $_SESSION['department'];

// Fetch department_id based on logged-in user's department
$dept_query = "SELECT department_id FROM departments WHERE department_name = '$department'";
$dept_result = mysqli_query($conn, $dept_query);

if (!$dept_result) {
    die("Error fetching department: " . mysqli_error($conn));
}

$dept_row = mysqli_fetch_assoc($dept_result);
$department_id = $dept_row['department_id'];

// Fetch unique start years and sections for the dropdowns
$start_year_query = "SELECT DISTINCT start_year FROM students WHERE department_id = '$department_id'";
$start_year_result = mysqli_query($conn, $start_year_query);

$section_query = "SELECT DISTINCT section FROM students WHERE department_id = '$department_id'";
$section_result = mysqli_query($conn, $section_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Marks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        #btn{
            padding-top: 20px !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Manage Marks</h2>
        <form method="POST" class="mt-3">
            <div class="row">
                <div class="col-md-5">
                    <label for="start_year" class="form-label">Start Year</label>
                    <select name="start_year" id="start_year" class="form-control">
                        <option value="">Select Start Year</option>
                        <?php while ($row = mysqli_fetch_assoc($start_year_result)) { ?>
                            <option value="<?php echo htmlspecialchars($row['start_year']); ?>">
                                <?php echo htmlspecialchars($row['start_year']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="section" class="form-label">Section</label>
                    <select name="section" id="section" class="form-control">
                        <option value="">Select Section</option>
                        <?php while ($row = mysqli_fetch_assoc($section_result)) { ?>
                            <option value="<?php echo htmlspecialchars($row['section']); ?>">
                                <?php echo htmlspecialchars($row['section']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                    

                </div>
                <div id="btn">
                <a href="hod_dashboard.php" class="btn btn-secondary">Back</a>
            </di>
        </form>

        <?php
        if (isset($_POST['search'])) {
            $start_year = mysqli_real_escape_string($conn, $_POST['start_year']);
            $section = mysqli_real_escape_string($conn, $_POST['section']);
            
            $student_query = "SELECT rollno, name FROM students WHERE department_id = '$department_id'";
            if (!empty($start_year)) {
                $student_query .= " AND start_year = '$start_year'";
            }
            if (!empty($section)) {
                $student_query .= " AND section = '$section'";
            }
            
            $student_result = mysqli_query($conn, $student_query);

            if (!$student_result) {
                die("<div class='alert alert-danger mt-4'>Error fetching students: " . mysqli_error($conn) . "</div>");
            }
        ?>
            <table class="table table-bordered mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($student_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rollno']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>
                                <a href="add_cia_marks.php?rollno=<?php echo urlencode($row['rollno']); ?>" class="btn btn-success">Add CIA Marks</a>
                                <a href="add_sem_marks.php?rollno=<?php echo urlencode($row['rollno']); ?>" class="btn btn-success">Add Semester Marks</a>
                                
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
