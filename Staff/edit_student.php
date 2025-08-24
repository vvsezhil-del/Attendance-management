<?php
session_start();
include("../db/db_connection.php");

if (!isset($_GET['rollno'])) {
    die("Roll number is required!");
}

$rollno = $_GET['rollno'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $section = $_POST['section'];
    $parent_name = $_POST['parent_name'];

    $query = "UPDATE students SET name = ?, email = ?, phone_number = ?, section = ?, parent_name = ? WHERE rollno = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $name, $email, $phone, $section, $parent_name, $rollno);
    $stmt->execute();

    header("Location: manage_students.php");
    exit;
}

// Fetch student data
$query = "SELECT * FROM students WHERE rollno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rollno);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], select, button {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background: #f4c430;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background: #e1a800;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Student</h1>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($student['phone_number']) ?>" required>

            <label for="section">Section:</label>
            <input type="text" id="section" name="section" value="<?= htmlspecialchars($student['section']) ?>" required>

            <label for="parent_name">Parent Name:</label>
            <input type="text" id="parent_name" name="parent_name" value="<?= htmlspecialchars($student['parent_name']) ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
