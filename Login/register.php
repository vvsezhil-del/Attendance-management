<?php
// Include the database connection file
include('../db/db_connection.php');

// Fetch departments from the database
$query = "SELECT * FROM departments";
$result = mysqli_query($conn, $query);

// Check for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $department_name = $_POST['department_name'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password for security

    // Check if the department already exists
    $department_query = "SELECT * FROM departments WHERE department_name = '$department_name'";
    $department_result = mysqli_query($conn, $department_query);

    if (mysqli_num_rows($department_result) == 0) {
        // Insert new department into the departments table
        $insert_department = "INSERT INTO departments (department_name) VALUES ('$department_name')";
        if (mysqli_query($conn, $insert_department)) {
            echo "New department added successfully!<br>";
        } else {
            echo "Error adding department: " . mysqli_error($conn) . "<br>";
        }
    }

    // Insert data into the staff table with default status "pending"
    $sql = "INSERT INTO staff (name, email, phone_number, department, role, password, status) 
            VALUES ('$name', '$email', '$phone_number', '$department_name', '$role', '$password', 'pending')";

    if (mysqli_query($conn, $sql)) {
        // Registration success, display notification
        echo '<div id="notification" style="position: fixed; top: 20px; right: 20px; background: #7C78B8; color: #fff; padding: 15px; border-radius: 5px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2); z-index: 1000;">';
        echo 'Registration successful! Redirecting to login page...';
        echo '</div>';

        echo '<script>
                setTimeout(function() {
                    window.location.href = "login.php"; // Redirect to login page
                }, 3000); // Wait for 3 seconds before redirecting
              </script>';
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
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
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        .screen {
            background: linear-gradient(90deg, #5D54A4, #7C78B8);
            position: relative;
            height: 850px;
            width: 400px;
            box-shadow: 0px 0px 24px #5C5696;
        }

        .screen__content {
            z-index: 1;
            position: relative;
            height: 100%;
            padding-top: 20px;
        }

        .screen__background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            clip-path: inset(0 0 0 0);
        }

        .screen__background__shape {
            transform: rotate(45deg);
            position: absolute;
        }

        .screen__background__shape1 {
            height: 520px;
            width: 520px;
            background: #FFF;
            top: -50px;
            right: 120px;
            border-radius: 0 72px 0 0;
        }

        .screen__background__shape2 {
            height: 220px;
            width: 220px;
            background: #6C63AC;
            top: -172px;
            right: 0;
            border-radius: 32px;
        }

        .screen__background__shape3 {
            height: 540px;
            width: 190px;
            background: linear-gradient(270deg, #5D54A4, #6A679E);
            top: -24px;
            right: 0;
            border-radius: 32px;
        }

        .screen__background__shape4 {
            height: 400px;
            width: 200px;
            background: #7E7BB9;
            top: 420px;
            right: 50px;
            border-radius: 60px;
        }

        .login {
            width: 320px;
            padding: 30px;
            padding-top: 30px;
        }

        .login__field {
            padding: 20px 0px;
            position: relative;
        }

        .login__icon {
            position: absolute;
            top: 30px;
            color: #7875B5;
        }

        .login__input {
            border: none;
            border-bottom: 2px solid #D1D1D4;
            background: none;
            padding: 10px;
            padding-left: 24px;
            font-weight: 700;
            width: 75%;
            transition: .2s;
        }

        .login__input:active,
        .login__input:focus,
        .login__input:hover {
            outline: none;
            border-bottom-color: #6A679E;
        }

        .login__submit {
            background: #fff;
            font-size: 14px;
            margin-top: 30px;
            padding: 16px 20px;
            border-radius: 26px;
            border: 1px solid #D4D3E8;
            text-transform: uppercase;
            font-weight: 700;
            display: flex;
            align-items: center;
            width: 100%;
            color: #4C489D;
            box-shadow: 0px 2px 2px #5C5696;
            cursor: pointer;
            transition: .2s;
        }

        .login__submit:active,
        .login__submit:focus,
        .login__submit:hover {
            border-color: #6A679E;
            outline: none;
        }

        .button__icon {
            font-size: 24px;
            margin-left: auto;
            color: #7875B5;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="screen">
        <div class="screen__content">
            <form class="login" method="POST" action="register.php">
                <div class="login__field">
                    <i class="login__icon fas fa-user"></i>
                    <input type="text" name="name" class="login__input" placeholder="Full Name" required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-envelope"></i>
                    <input type="email" name="email" class="login__input" placeholder="Email" required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-phone"></i>
                    <input type="text" name="phone_number" class="login__input" placeholder="Phone Number" required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-building"></i>
                    <select name="department_name" class="login__input" required>
                        <option value="" disabled selected>Select Department</option>
                        <!-- PHP Code to Fetch Departments -->
                        <?php
                        // Include the database connection
                        include('../db/db_connection.php');
                        $query = "SELECT * FROM departments";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['department_name'] . "'>" . $row['department_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-user-tag"></i>
                    <select name="role" class="login__input" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="Head of the department">Head of the Department</option>
                        <option value="asst.professor">Asst.Professor</option>
                    </select>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-lock"></i>
                    <input type="password" name="password" class="login__input" placeholder="Password" required>
                </div>
                <button class="button login__submit">
                    <span class="button__text">Register</span>
                    <i class="button__icon fas fa-chevron-right"></i>
                </button>

                <a href="../home.html" class="button login__submit" style="text-align: center;">
                <span class="button__text">Back</span>
                <i class="button__icon fas fa-arrow-left"></i>
            </a> <br>

            <p style="color: whitesmoke;">Already registered? <a href="login.php" style="color:rgb(52, 16, 184); text-decoration: none;">Login</a></p>

            </form>

        </div>
        <div class="screen__background">
            <span class="screen__background__shape screen__background__shape4"></span>
            <span class="screen__background__shape screen__background__shape3"></span>
            <span class="screen__background__shape screen__background__shape2"></span>
            <span class="screen__background__shape screen__background__shape1"></span>
        </div>
    </div>
</div>
</body>
</html>
