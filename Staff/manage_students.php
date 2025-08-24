<?php
session_start();
include("../db/db_connection.php");

// Assuming the logged-in staff's department name is stored in the session
$department = $_SESSION['department'];

// Initialize filter variables
$year = isset($_POST['year']) ? $_POST['year'] : '';
$rollno = isset($_POST['rollno']) ? $_POST['rollno'] : '';
$section = isset($_POST['section']) ? $_POST['section'] : '';

// Build the query based on filters
$query = "SELECT * FROM students WHERE department = ?";
$params = [$department];

if ($year) {
  $query .= " AND (start_year = ? OR end_year = ?)";
  array_push($params, $year, $year);
}
if ($rollno) {
  $query .= " AND rollno LIKE ?";
  array_push($params, "%" . $rollno . "%");
}
if ($section) {
  $query .= " AND section = ?";
  array_push($params, $section);
}

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);

// Fetch distinct years and sections for the filter options
$yearQuery = "SELECT DISTINCT start_year FROM students WHERE department = ?";
$yearStmt = $conn->prepare($yearQuery);
$yearStmt->bind_param("s", $department);
$yearStmt->execute();
$yearResult = $yearStmt->get_result();
$years = [];
while ($row = $yearResult->fetch_assoc()) {
  $years[] = $row['start_year'];

}
$years = array_unique($years);

$sectionQuery = "SELECT DISTINCT section FROM students WHERE department = ?";
$sectionStmt = $conn->prepare($sectionQuery);
$sectionStmt->bind_param("s", $department);
$sectionStmt->execute();
$sectionResult = $sectionStmt->get_result();
$sections = [];
while ($row = $sectionResult->fetch_assoc()) {
  $sections[] = $row['section'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Students</title>
  <style>
    /* Reset some default spacing */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      color: #333;
      padding-top: 60px;
      /* Leave space for the fixed header */
    }

    h1 {
      text-align: center;
      margin: 20px 0;
      color: #333;
    }

    /* Header Styles */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: #222;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
    }

    .header .logo {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .header .toggle-btn {
      font-size: 1.5rem;
      cursor: pointer;
      color: white;
    }

    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      top: 50px;

      left: 0;
      width: 200px;
      height: calc(100% - 20px);
      background-color: #222;
      padding-top: 20px;
      display: none;
      flex-direction: column;
      z-index: 999;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 12px 20px;
      font-size: 1.1rem;
      transition: background-color 0.3s;
    }

    .sidebar a:hover {
      background-color: #444;
    }

    /* Filter Section */
    #filter-container {
      margin: 20px auto;
      max-width: 800px;
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
      padding: 10px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    #filter-container form {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
      align-items: center;
    }

    label {
      font-size: 0.9rem;
    }

    select,
    input[type="text"],
    button.filter-btn {
      padding: 8px 12px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #222;
      color: #ddd;
      width: 150px;
    }

    button.filter-btn {
      background-color: #f4c430;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s;
      border: none;
    }

    button.filter-btn:hover {
      background-color: #e1a800;
    }

    /* Table Styles */
    table {
      width: 90%;
      margin: 30px auto;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #ddd;
      font-size: 1rem;
    }

    td {
      background-color: #fff;
    }

    tr:nth-child(even) td {
      background-color: #f9f9f9;
    }

    tr:hover td {
      background-color: #f1f1f1;
    }

    /* Action Buttons */
    button.action-btn {
      margin: 5px 0;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    /* Desktop action buttons */
    .desktop-actions button.edit {
      background-color: brown;
    }

    .desktop-actions button.delete {
      background-color: red;
    }

    .desktop-actions button.edit:hover {
      background-color: #a52a2a;
    }

    .desktop-actions button.delete:hover {
      background-color: darkred;
    }

    /* Mobile action button */
    .mobile-actions button.view {
      background-color: green;
    }

    .mobile-actions button.view:hover {
      background-color: darkgreen;
    }

    /* Responsive Design */
    /* On large screens, show all columns and only desktop actions */
    @media (min-width: 769px) {
      .desktop-actions {
        display: inline-block;
      }

      .mobile-actions {
        display: none;
      }

      /* Ensure sidebar is always visible on larger screens if desired */
      .sidebar {
        display: flex;
      }

      /* Shift main content to the right */
      body {
        padding-left: 220px;
      }
    }

    /* On small screens, show only Roll No, Name and Action columns.
       Also, hide the desktop action buttons and display mobile action. */
    @media (max-width: 768px) {

      /* Hide columns: Email, Start Year, End Year, Section, Phone, Department, Parent Name */
      /* Assuming table structure:
         1: Roll No, 2: Name, 3: Email, 4: Start Year, 5: End Year,
         6: Section, 7: Phone, 8: Department, 9: Parent Name, 10: Actions */
      th:nth-child(3),
      td:nth-child(3),
      th:nth-child(4),
      td:nth-child(4),
      th:nth-child(5),
      td:nth-child(5),
      th:nth-child(6),
      td:nth-child(6),
      th:nth-child(7),
      td:nth-child(7),
      th:nth-child(8),
      td:nth-child(8),
      th:nth-child(9),
      td:nth-child(9) {
        display: none;
      }

      .desktop-actions {
        display: none;
      }

      .mobile-actions {
        display: inline-block;
      }
    }

    /* Modal styles */
    .modal {

      top: 1;
      left: 0;
      width: 80%;
      height: 80%;

      display: none;
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }

    .modal-content {
      background-color: #fff;
      padding: 20px;
      border-radius: 5px;
      width: 90%;
      max-width: 400px;
      position: relative;
    }

    .modal-content h2 {
      margin-top: 0;
    }

    .modal-content label {
      display: block;
      margin-top: 10px;
    }

    .modal-content input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      box-sizing: border-box;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
    }

    .modal-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .modal-actions button {
      padding: 8px 12px;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s;
      border: none;
    }

    .modal-actions button.edit {
      background-color: brown;
    }

    .modal-actions button.delete {
      background-color: red;
    }

    .modal-actions button.edit:hover {
      background-color: #a52a2a;
    }

    .modal-actions button.delete:hover {
      background-color: darkred;
    }
/* Responsive Design: Stack the label and input vertically on small screens */
@media (max-width: 768px) {
  #filter-container form {
    flex-direction: column;
  }

  #filter-container form div {
    flex-direction: column; /* Stack label and input vertically */
    align-items: flex-start;
    gap: 1px;
  }

  select,
  input[type="text"],
  button.filter-btn {
    width: 80%; /* Take full width on small screens */
  }
}
th:last-child, td:last-child {
  width: 141px; /* Adjust width for actions column */
}

  </style>
</head>

<body>
  <!-- Header & Sidebar -->
  <div class="header">
    <div class="logo">University</div>
    <div class="toggle-btn" onclick="toggleSidebar()">&#9776;</div>
  </div>

  <div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_students.php">Manage Students</a>
    <a href="manage_attendance.php">Manage Attendance</a>
    <a href="manage_marks.php">Manage Marks</a>
    <a href="view_courses.php">View Courses</a>
    <a href="../Login/logout.php">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Manage Students</h1>

    <!-- Filter Form -->
    <div id="filter-container">
      <form method="POST">
        <label for="year">Year:</label>
        <select name="year" id="year">
          <option value="">Select Year</option>
          <?php foreach ($years as $yearOption): ?>
            <option value="<?= htmlspecialchars($yearOption) ?>" <?= $year == $yearOption ? 'selected' : '' ?>>
              <?= htmlspecialchars($yearOption) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label for="rollno">Roll No:</label>
        <input type="text" name="rollno" id="rollno" value="<?= htmlspecialchars($rollno) ?>" />

        <label for="section">Section:</label>
        <select name="section" id="section">
          <option value="">Select Section</option>
          <?php foreach ($sections as $sectionOption): ?>
            <option value="<?= htmlspecialchars($sectionOption) ?>" <?= $section == $sectionOption ? 'selected' : '' ?>>
              <?= htmlspecialchars($sectionOption) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="filter-btn">Apply Filter</button>
      </form>
    </div>

    <!-- Students Table -->
    <table>
      <thead>
        <tr>
          <th>Roll No</th>
          <th>Name</th>
          <th>Email</th>
          <th>Start Year</th>
          <th>End Year</th>
          <th>Section</th>
          <th>Phone</th>
          <th>Department</th>
          <th>Parent Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $student): ?>
          <tr id="student-<?= htmlspecialchars($student['rollno']) ?>">
            <td><?= htmlspecialchars($student['rollno']) ?></td>
            <td><?= htmlspecialchars($student['name']) ?></td>
            <td><?= htmlspecialchars($student['email']) ?></td>
            <td><?= htmlspecialchars($student['start_year']) ?></td>
            <td><?= htmlspecialchars($student['end_year']) ?></td>
            <td><?= htmlspecialchars($student['section']) ?></td>
            <td><?= htmlspecialchars($student['phone_number']) ?></td>
            <td><?= htmlspecialchars($student['department']) ?></td>
            <td><?= htmlspecialchars($student['parent_name']) ?></td>
            <!-- Desktop Action Buttons -->
            <!-- Desktop Action Buttons -->
            <td class="desktop-actions">
              <button class="action-btn edit"
                onclick="location.href='edit_student.php?rollno=<?= htmlspecialchars($student['rollno']) ?>'">Edit</button>
              <button class="action-btn delete"
              onclick="location.href='delete_student.php?rollno=<?= htmlspecialchars($student['rollno']) ?>'">Delete</button>
            </td>

            <!-- Mobile Action Buttons -->
            <td class="mobile-actions">
              <button class="action-btn view"
              onclick="location.href='view_student.php?rollno=<?= htmlspecialchars($student['rollno']) ?>'">view</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>


  <script>
    // Toggle sidebar for small screens
    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
      // Toggle the display between "flex" and "none"
      sidebar.style.display = sidebar.style.display === 'flex' ? 'none' : 'flex';
    }
  </script>
</body>

</html>