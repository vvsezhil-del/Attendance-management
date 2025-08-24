<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: login_students.php"); // Redirect to the login or home page
exit();
?>
