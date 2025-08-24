<?php
session_start();
include('../db/db_connection.php');

if (!isset($_SESSION['rollno'])) {
    echo "<div class='alert alert-danger'>You must be logged in to view your progress.</div>";
    exit;
}

$rollno = $_SESSION['rollno'];

if (isset($_POST['semester'])) {
    $semester = $_POST['semester'];

    // Fetch CIA Marks
    $query_cia = "SELECT * FROM cia_marks WHERE rollno = '$rollno' AND semester = '$semester'";
    $result_cia = mysqli_query($conn, $query_cia);

    // Fetch Semester Exam Marks
    $query_sem = "SELECT * FROM semester_marks WHERE rollno = '$rollno' AND semester = '$semester'";
    $result_sem = mysqli_query($conn, $query_sem);

    echo "<h3 class='text-center text-primary'>Semester $semester - Marks</h3>";

    if (mysqli_num_rows($result_cia) > 0) {
        echo "<h4 class='text-success'>CIA Marks</h4>";
        echo "<div class='table-responsive'>
                <table class='table table-bordered table-striped'>
                    <thead class='table-dark'>
                        <tr>
                            <th>Subject Code</th>
                            <th>CIA 1</th>
                            <th>CIA 2</th>
                            <th>CIA 3</th>
                            <th>Total CIA Marks</th>
                        </tr>
                    </thead>
                    <tbody>";

        while ($row = mysqli_fetch_assoc($result_cia)) {
            echo "<tr>
                    <td>{$row['subject_code']}</td>
                    <td>{$row['cia1_marks']}</td>
                    <td>{$row['cia2_marks']}</td>
                    <td>{$row['cia3_marks']}</td>
                    <td>{$row['total_cia_marks']}</td>
                  </tr>";
        }

        echo "    </tbody>
                </table>
              </div>";
    } else {
        echo "<div class='alert alert-warning'>No CIA Marks found for this semester.</div>";
    }

    if (mysqli_num_rows($result_sem) > 0) {
        echo "<h4 class='text-success'>Semester Exam Marks</h4>";
        echo "<div class='table-responsive'>
                <table class='table table-bordered table-striped'>
                    <thead class='table-dark'>
                        <tr>
                            <th>Subject Code</th>
                            <th>CIA Total</th>
                            <th>Semester Exam Marks</th>
                            <th>Final Total</th>
                        </tr>
                    </thead>
                    <tbody>";

        while ($row = mysqli_fetch_assoc($result_sem)) {
            echo "<tr>
                    <td>{$row['subject_code']}</td>
                    <td>{$row['cia_total']}</td>
                    <td>{$row['sem_exam_marks']}</td>
                    <td>{$row['final_total']}</td>
                  </tr>";
        }

        echo "    </tbody>
                </table>
              </div>";
    } else {
        echo "<div class='alert alert-warning'>No Semester Marks found for this semester.</div>";
    }
}
?>
