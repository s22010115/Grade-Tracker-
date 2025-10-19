<?php
session_start();
include 'database.php'; // Ensure this file connects to the correct database

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch the latest data from the database
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$Student = $result->fetch_assoc();
// Fetch subjects already selected
$subjectsStmt = $conn->prepare("
    SELECT ss.course_code, s.subject_name
    FROM student_subject ss
    JOIN subjects s ON ss.course_code = s.course_code
    WHERE ss.student_id = ?
");
$subjectsStmt->bind_param("i", $student_id);
$subjectsStmt->execute();
$subjectsResult = $subjectsStmt->get_result();
$studentSubjects = $subjectsResult->fetch_all(MYSQLI_ASSOC);


if (!$Student) {
    echo "Error: Student not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Grade Tracker Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    .logo {
      display: flex;
      align-items: center;
      font-size: 25px;
      font-weight: bold;
    }

    .g-image {
      height: 35px;
      margin-right: 0px;
    }

    .logo-text {
      font-family: sans-serif;
      font-size: 25px;
      color: #6785f5;
      padding-top: 8%;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #f4f6f8;
      padding: 20px;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar h2 {
      font-size: 16px;
      margin-bottom: 30px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
      margin-top: 40px;
    }

    .sidebar ul li {
      padding: 10px;
      margin-bottom: 10px;
      cursor: pointer;
      border-radius: 6px;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .sidebar ul li.active,
    .sidebar ul li:hover {
      background-color: #6785f5;
      color: white;
    }

    .sidebar ul li.logout {
      margin-top: auto;
    }

    .sidebar ul li.logout:hover {
      background-color: #f66;
      color: white;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 20px;
      background-color: #fff;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .form {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
    }

    select, input {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 100%;
    }

    button {
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      background-color: #2563eb;
      color: white;
      width: 100px;
    }

    /* Table styles */
    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
      background-color: white;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: center;
    }
    .profile-picture img {
      width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid rgb(0, 4, 15);
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div>
      <!-- Logo -->
      <div class="logo">
        <img src="logo.png" alt="G" class="g-image">
        <span class="logo-text">rade Tracker</span>
      </div>

      <!-- Navigation -->
      <nav>
        <ul>
          <li class="active"><i class="fa fa-plus"></i> Add Subjects</li>
          <li><a href="subject-grades.php"><i class="fas fa-chart-bar"></i> Subject Grades</a></li>
        </ul>
      </nav>
    </div>

    <!-- Log out button pinned to bottom -->
    <ul>
      <li class="logout"><a href="logout.php">⏻ Log out</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header>
      <h1 style="font-size: 22px; margin: 0;">Welcome, <?php echo htmlspecialchars($Student['name']); ?></h1>
      
       <div style="display: flex; align-items: center; gap: 15px;">
    <div class="profile-picture">
    <img src="<?php echo !empty($Student['profile_photo']) ? 'uploads/' . htmlspecialchars($Student['profile_photo']) : 'default-profile.png'; ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solidrgb(0, 4, 15);">
    </div>
    <div class="user-details">
    <h2 style="margin: 0;"><?php echo htmlspecialchars($Student['name']); ?></h2>
      <a href="edit-student-profile.php" style="text-decoration: none; background: #6785f5; color: white; padding: 6px 12px; border-radius: 6px; font-size: 14px; display: inline-block; margin-top: 5px;">Edit Profile</a>
    </div>
  </div>
    </header>

    <h2 style="margin-top: 20px;">Add Subjects</h2>
 
    <div class="form">
      <?php
// Fetch unique course codes
$courseCodes = $conn->query("SELECT DISTINCT course_code FROM subjects");

// Fetch all subjects
$subjects = $conn->query("SELECT subject_name FROM subjects");
?>

<select id="courseCode" required>
  <option value="">Select Course Code</option>
  <?php while($row = $courseCodes->fetch_assoc()): ?>
    <option value="<?php echo htmlspecialchars($row['course_code']); ?>">
      <?php echo htmlspecialchars($row['course_code']); ?>
    </option>
  <?php endwhile; ?>
</select>

<select id="subjectName" required>
  <option value="">Select Subject Name</option>
  <?php while($row = $subjects->fetch_assoc()): ?>
    <option value="<?php echo htmlspecialchars($row['subject_name']); ?>">
      <?php echo htmlspecialchars($row['subject_name']); ?>
    </option>
  <?php endwhile; ?>
</select>


      <button onclick="addSubject()">Add</button>
    </div>

    <!-- Table to Display Subjects -->
    <table id="subjectTable">
      <thead>
        <tr>
          <th>Course Code</th>
          <th>Subject Name</th>
        </tr>
      </thead>
      <tbody>
        <tbody>
  <?php foreach ($studentSubjects as $subject): ?>
    <tr>
      <td><?php echo htmlspecialchars($subject['course_code']); ?></td>
      <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
    </tr>
  <?php endforeach; ?>
</tbody>

      </tbody>
    </table>
  </main>
</div>

<script>
function addSubject() {
  const courseCode = document.getElementById("courseCode").value;
  const subjectName = document.getElementById("subjectName").value;

  if (!courseCode || !subjectName) {
    alert("Please select both Course Code and Subject Name.");
    return;
  }

  fetch("enroll-subject.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `course_code=${courseCode}&subject_name=${subjectName}`,
  })
  .then(response => response.text())
  .then(data => {
    if (data === "success") {
      addRow(courseCode, subjectName);
    } else {
      alert(data);
    }
  });
}

function addRow(code, name) {
  const table = document.getElementById("subjectTable").querySelector("tbody");
  const row = table.insertRow();
  row.insertCell(0).innerText = code;
  row.insertCell(1).innerText = name;
}
</script>


</body>
</html>
