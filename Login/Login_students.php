<?php
session_start();
include('../db/db_connection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs
    $rollno = mysqli_real_escape_string($conn, $_POST['rollno']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Query to check if the student exists
    $query = "SELECT * FROM students WHERE rollno = '$rollno' AND email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);

        // Store student details in session
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['name'] = $student['name'];
        $_SESSION['rollno'] = $student['rollno'];
        $_SESSION['email'] = $student['email'];
        $_SESSION['department'] = $student['department'];

        // Redirect to student dashboard
        header('Location: ../Student/student_dashboard.php');
        exit;
    } else {
        $error_message = "Invalid roll number or email.";
    }
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Raleway, sans-serif;
        }

        body {
            background: linear-gradient(90deg, #C7C5F4, #776BCC);
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            background: linear-gradient(90deg, #5D54A4, #7C78B8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 24px #5C5696;
            max-width: 400px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            color: white;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 25px;
            padding: 10px 15px;
            font-weight: 600;
            border: 2px solid #D1D1D4;
        }

        .form-control:focus {
            border-color: #6A679E;
            box-shadow: none;
        }

        .btn-login {
            background: #fff;
            color: #4C489D;
            font-weight: bold;
            width: 100%;
            border-radius: 25px;
            padding: 12px;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #6A679E;
            color: white;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            text-align: center;
            color: white;
            margin-top: 15px;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            color: #C7C5F4;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <h2 class="login-header">Student Login</h2>
        <form method="POST" action="Login_students.php">
            <?php if (isset($error_message)): ?>
                <div class="error"> <?= htmlspecialchars($error_message); ?> </div>
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label text-white">Roll Number</label>
                <input type="text" name="rollno" class="form-control" placeholder="Enter Roll Number" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-white">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
            </div>
            <button type="submit" class="btn btn-login">Log In</button>
            <a href="../home.html" class="back-link">Back to Home</a>
        </form>
    </div>
</div>
</body>
</html>
