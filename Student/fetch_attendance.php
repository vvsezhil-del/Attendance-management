<?php
include '../db/db_connection.php';

if (isset($_POST['month'])) {
    $month = $_POST['month'];

    $query = "SELECT a.rollno, s.name, a.date, a.status 
              FROM attendance a 
              JOIN students s ON a.rollno = s.rollno
              WHERE DATE_FORMAT(a.date, '%Y-%m') = '$month' 
              ORDER BY a.date ASC";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['student_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['date']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No attendance records found for this month.</td></tr>";
    }
}
?>
