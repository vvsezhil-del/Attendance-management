<?php
session_start();

// Optionally restrict access to only users with the 'Asst.professor' role
if ($_SESSION['role'] !== 'Asst.professor') {
    echo "Access denied. You do not have permission to view this page.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professor Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Reduce header height */
    header {
      padding: 10px 0; 
    }

    /* Custom hamburger icon */
    .custom-toggler {
      border: none;
      background: transparent;
      display: flex;
      flex-direction: column;
      gap: 4px;
      padding: 5px;
    }

    .custom-toggler div {
      width: 25px;
      height: 3px;
      background-color: white;
      transition: 0.3s;
    }

    /* Navbar styling */
    .navbar-nav .nav-link {
      color: white !important;
      padding: 8px 15px;
    }

    /* Align items properly */
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
  </style>
</head>
<body>

<header class="bg-primary text-white">
  <div class="container header-container">
    <!-- Logo and title -->
    <div class="d-flex align-items-center">
      <img src="logo.png" alt="Logo" style="height: 35px;" class="me-2">
      <h1 class="h6 m-0">University Portal</h1>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
      <button class="custom-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <div></div>
        <div></div>
        <div></div>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a href="professor_dashboard.php" class="nav-link">Home</a></li>
          <li class="nav-item"><a href="manage_students.php" class="nav-link">Manage Students</a></li>
          <li class="nav-item"><a href="manage_attendance.php" class="nav-link">Manage Attendance</a></li>
          <li class="nav-item"><a href="manage_marks.php" class="nav-link">Manage Marks</a></li>
          <li class="nav-item"><a href="../Login/logout.php" class="nav-link">Logout</a></li>
        </ul>
      </div>
    </nav>
  </div>
</header>

<div class="container my-4">
  <!-- Welcome Message -->
  <div class="mb-4">
    <h2>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
    <p><strong>Department:</strong> <?php echo htmlspecialchars($_SESSION['department']); ?></p>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
