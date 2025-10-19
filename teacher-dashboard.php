<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch the latest teacher data from the database
$stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    echo "Error: Teacher not found.";
    exit();
}

// Count total subjects for this teacher
$subject_count = 0;
$subject_sql = "SELECT COUNT(*) AS total FROM subjects WHERE teacher_id = ?";
$stmt = $conn->prepare($subject_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$subject_result = $stmt->get_result();
if ($subject_result) {
    $row = $subject_result->fetch_assoc();
    $subject_count = $row['total'];
}
$stmt->close();

// Count total students for this teacher (students enrolled in this teacher's subjects)
$student_count = 0;
$student_sql = "SELECT COUNT(DISTINCT ss.student_id) AS total
                FROM student_subject ss
                JOIN subjects s ON ss.course_code = s.course_code
                WHERE s.teacher_id = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$student_result = $stmt->get_result();
if ($student_result) {
    $row = $student_result->fetch_assoc();
    $student_count = $row['total'];
}
$stmt->close();

// Count grades submitted for this teacher
$grades_count = 0;
$grades_sql = "SELECT COUNT(*) AS total FROM grades WHERE course_code IN (SELECT course_code FROM subjects WHERE teacher_id = ?)";
$stmt = $conn->prepare($grades_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$grades_result = $stmt->get_result();
if ($grades_result) {
    $row = $grades_result->fetch_assoc();
    $grades_count = $row['total'];
}
$stmt->close();

// Count missing grades for this teacher
$missing_sql = "
    SELECT COUNT(*) AS missing
    FROM student_subject ss
    JOIN subjects s ON ss.course_code = s.course_code
    LEFT JOIN grades g ON ss.student_id = g.student_id AND ss.course_code = g.course_code
    WHERE g.grade IS NULL AND s.teacher_id = ?
";
$stmt = $conn->prepare($missing_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$missing_result = $stmt->get_result();
$missing_count = 0;
if ($missing_result) {
    $row = $missing_result->fetch_assoc();
    $missing_count = $row['missing'];
}
$stmt->close();

// Get last 3 submitted grades for this teacher
$recent_grades = [];
$recent_sql = "
    SELECT g.student_id, g.course_code, s.subject_name, g.grade, g.status
    FROM grades g
    JOIN subjects s ON g.course_code = s.course_code
    WHERE s.teacher_id = ?
    ORDER BY g.student_id DESC
    LIMIT 3
";
$stmt = $conn->prepare($recent_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$recent_result = $stmt->get_result();
if ($recent_result) {
    while ($row = $recent_result->fetch_assoc()) {
        $recent_grades[] = $row;
    }
}
$stmt->close();

// Get all subjects for this teacher for progress
$subjects_progress = [];
$subjects_sql = "SELECT course_code, subject_name FROM subjects WHERE teacher_id = ?";
$stmt = $conn->prepare($subjects_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$subjects_result = $stmt->get_result();

if ($subjects_result) {
    while ($subject = $subjects_result->fetch_assoc()) {
        $course_code = $subject['course_code'];
        $subject_name = $subject['subject_name'];

        // Count total students enrolled in this subject
        $total_sql = "SELECT COUNT(*) AS total FROM student_subject WHERE course_code = ?";
        $stmt2 = $conn->prepare($total_sql);
        $stmt2->bind_param("s", $course_code);
        $stmt2->execute();
        $total_result = $stmt2->get_result();
        $total_row = $total_result->fetch_assoc();
        $total_students = $total_row['total'];
        $stmt2->close();

        // Count students with grades submitted for this subject
        $graded_sql = "SELECT COUNT(DISTINCT student_id) AS graded FROM grades WHERE course_code = ?";
        $stmt3 = $conn->prepare($graded_sql);
        $stmt3->bind_param("s", $course_code);
        $stmt3->execute();
        $graded_result = $stmt3->get_result();
        $graded_row = $graded_result->fetch_assoc();
        $graded_students = $graded_row['graded'];
        $stmt3->close();

        // Calculate percentage
        $percent = ($total_students > 0) ? round(($graded_students / $total_students) * 100) : 0;

        $subjects_progress[] = [
            'subject_name' => $subject_name,
            'percent' => $percent,
            'total_students' => $total_students
        ];
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Grade Tracker Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      font-family: sans-serif;
      margin: 0;
      display: flex;
    }

    .logo {
      display: flex;
      align-items: center;
      font-size: 25px;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .g-image {
      height: 35px;
      margin-right: 0px;
    }

    .logo-text {
      font-family: sans-serif;
      font-size: 25px;
      color: #6785f5;
      padding-top: 9%;
    }

    .sidebar {
      width: 250px;
      background: #f0f2f5;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin-top: 10px;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .sidebar ul li {
      padding: 10px;
      margin-bottom: 10px;
      cursor: pointer;
      border-radius: 6px;
      display: flex;
      align-items: center;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: inherit;
      display: flex;
      align-items: center;
    }

    .sidebar ul li i {
      margin-right: 10px;
    }

    /* Active link style */
    .sidebar ul li.active,
    .sidebar ul li:hover {
      background-color: #6785f5;
      color: white;
      width: 168%;
      transition: all 0.3s ease;
    }

.sidebar ul li.logout {
  margin-top: 240%;
}

    .sidebar ul li.logout:hover {
      background-color: #f66;
      color: white;
    }

    .main-content {
      flex: 1;
      padding: 20px;
      background-color: #f9fafc;
      overflow-y: auto;
      margin-left: 300px;
      flex-grow: 1;
      transition: all 0.3s ease;
      margin-top: 2%;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .welcome-banner {
      background-color: #6785f5;
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .welcome-banner h1 {
      margin: 0;
      font-size: 20px;
    }

    .welcome-banner p {
      margin: 5px 0 0;
      opacity: 0.9;
      font-size: 14px;
    }

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
      margin-bottom: 20px;
    }

    .stat-card {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      text-align: center;
    }

    .stat-card h3 {
      margin: 0;
      font-size: 28px;
      color: #6785f5;
    }

    .stat-card p {
      margin: 5px 0 0;
      color: #666;
      font-size: 14px;
    }

    .stat-card.highlight {
      background: #fff4f4;
      color: #ff6b6b;
      position: relative;
    }

    .stat-card.highlight i {
      color: #ff6b6b;
      font-size: 24px;
      position: absolute;
      left: 18px;
      top: 18px;
    }

    .stat-card.highlight h3,
    .stat-card.highlight p {
      color: #ff6b6b;
      margin-left: 32px;
    }

    .quick-actions {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 20px;
    }

    .action-card {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      cursor: pointer;
      transition: all 0.2s;
    }

    .action-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .action-card i {
      font-size: 20px;
      color: #6785f5;
      margin-bottom: 8px;
    }

    .action-card h3 {
      margin: 0;
      font-size: 14px;
    }

    .content-section {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
    }

    .panel {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .panel h2 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 16px;
      color: #333;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }

    .grade-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .grade-table th {
      text-align: left;
      padding: 10px 12px;
      background: #f5f7fa;
      color: #6785f5;
      font-weight: 600;
    }

    .grade-table td {
      padding: 10px 12px;
      border-bottom: 1px solid #eee;
    }

    .grade-table tr:last-child td {
      border-bottom: none;
    }

    .subject-progress {
      margin-top: 10px;
    }

    .progress-item {
      margin-bottom: 12px;
    }

    .progress-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .progress-bar {
      height: 6px;
      background: #e0e6ed;
      border-radius: 3px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: #6785f5;
      border-radius: 3px;
    }

    .progress-item small {
      font-size: 12px;
      color: #666;
      display: block;
      margin-top: 3px;
    }

    .urgent-alert {
      background: #fff4f4;
      border-left: 4px solid #ff6b6b;
      padding: 12px;
      margin-top: 15px;
      display: flex;
      align-items: center;
      font-size: 14px;
    }

    .urgent-alert i {
      color: #ff6b6b;
      margin-right: 8px;
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Sidebar - Back on left side -->
  <aside class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="G" class="g-image">
      <span class="logo-text">rade Tracker</span>
    </div>

    <nav>
      <ul>
        <li class="active"><i class="fas fa-th-large"></i> Dashboard</li>
        <li><a href="subjects.php"><i class="fas fa-book"></i> Subjects</a></li>
        <li><a href="participants.php"><i class="fas fa-user"></i> Participants</a></li>
        <li><a href="grades.html"><i class="fas fa-file-alt"></i> Grades</a></li>
        <li><a href="view-grades.php"><i class="fas fa-list-alt"></i>View Grades</a></li>
        <li class="logout"><a href="index.html">⏻ Log out</a></li>
      </ul>

    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
  <header>
  <h1 style="font-size: 22px; margin: 0;">Welcome, <?php echo htmlspecialchars($teacher['name']); ?></h1>
  <div style="display: flex; align-items: center; gap: 15px;">
    <div class="profile-picture">
    <img src="<?php echo !empty($teacher['profile_photo']) ? 'uploads/' . htmlspecialchars($teacher['profile_photo']) : 'default-profile.png'; ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solidrgb(0, 4, 15);">
    </div>
    <div class="user-details">
    <h2 style="margin: 0;"><?php echo htmlspecialchars($teacher['name']); ?></h2>
      <a href="edit-teacher-profile.php" style="text-decoration: none; background: #6785f5; color: white; padding: 6px 12px; border-radius: 6px; font-size: 14px; display: inline-block; margin-top: 5px;">Edit Profile</a>
    </div>
  </div>
</header>


    <div class="welcome-banner">
      <h1>Grade Tracking Dashboard</h1>
      <p>Monitor and manage all your class grades in one place</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <h3><?php echo $subject_count; ?></h3>
        <p>Total Subjects</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $student_count; ?></h3>
        <p>Total Students</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $grades_count; ?></h3>
        <p>Grades Submitted</p>
      </div>
      <div class="stat-card highlight" style="background: #fff4f4; color: #ff6b6b; position: relative;">
        <i class="fas fa-exclamation-triangle" style="color: #ff6b6b; font-size: 24px; position: absolute; left: 18px; top: 18px;"></i>
        <h3 style="color: #ff6b6b; margin-left: 32px;"><?php echo $missing_count; ?></h3>
        <p style="color: #ff6b6b; margin-left: 32px;">Missing Grades</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <div class="action-card" onclick="location.href='subjects.php'">
        <i class="fas fa-plus"></i>
        <h3>Add Subject</h3>
      </div>
      <div class="action-card" onclick="location.href='view-grades.php'">
        <i class="fas fa-chart-bar"></i>
        <h3>View Grades</h3>
      </div>
      <div class="action-card" onclick="location.href='missing-grades.php'">
        <i class="fas fa-exclamation-circle"></i>
        <h3>Missing Grades</h3>
      </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
      <div class="panel">
        <h2>Subject Grades</h2>
        <table class="grade-table">
          <tr>
            <th>Student ID</th>
            <th>Course Code</th>
            <th>Subject</th>
            <th>Grade</th>
            <th>Status</th>
          </tr>
          <?php if (count($recent_grades) > 0): ?>
            <?php foreach ($recent_grades as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                <td><?php echo htmlspecialchars($row['grade']); ?></td>
                <td>
                  <?php if (strtolower($row['status']) == 'pass'): ?>
                    <span style="color: green;"><?php echo htmlspecialchars($row['status']); ?></span>
                  <?php else: ?>
                    <span style="color: red;"><?php echo htmlspecialchars($row['status']); ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align:center;">No grades submitted yet.</td>
            </tr>
          <?php endif; ?>
        </table>
      </div>
      <div class="panel">
        <h2>Subject Progress</h2>
        <div class="subject-progress">
          <?php foreach ($subjects_progress as $progress): ?>
            <div class="progress-item">
              <div class="progress-header">
                <span><?php echo htmlspecialchars($progress['subject_name']); ?></span>
                <span><?php echo $progress['percent']; ?>%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progress['percent']; ?>%"></div>
              </div>
              <small><?php echo $progress['total_students']; ?> students</small>
            </div>
          <?php endforeach; ?>
        </div>
        <!-- Urgent Alert -->
        <div class="urgent-alert">
          <i class="fas fa-exclamation-triangle"></i>
          <span>You have <?php echo $missing_count; ?> missing grade submissions. Please update them.</span>
        </div>
      </div>
    </div>
  </main>
</div>

</body>
</html>