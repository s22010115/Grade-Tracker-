<?php
session_start();
$conn = new mysqli("sql109.infinityfree.com", "if0_40150596", "Xnhu1XfzEkLNd8Y", "if0_40150596_grade_tracker");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Teacher') {
    echo "Unauthorized";
    exit();
}
$teacher_id = $_SESSION['user_id'];

// Handle delete request
if (isset($_GET['delete_student_id']) && isset($_GET['delete_course_code'])) {
    $del_student_id = $_GET['delete_student_id'];
    $del_course_code = $_GET['delete_course_code'];
    $stmt = $conn->prepare("DELETE FROM grades WHERE student_id = ? AND course_code = ?");
    $stmt->bind_param("ss", $del_student_id, $del_course_code);
    $stmt->execute();
    $stmt->close();
    // Redirect to avoid resubmission
    header("Location: view-grades.php");
    exit();
}

// Fetch all grades with subject info
$grades_sql = "
    SELECT g.student_id, g.course_code, g.grade, g.status
    FROM grades g
    JOIN subjects s ON g.course_code = s.course_code
    WHERE s.teacher_id = ?
    ORDER BY s.subject_name, g.student_id
";
$stmt = $conn->prepare($grades_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$grades_by_course = [];
while ($row = $result->fetch_assoc()) {
    $grades_by_course[$row['course_code']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Grades</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .container {
      display: flex;
      width: 100%;
      height: 100%;
    }

    body {
      font-family: sans-serif;
      margin: 0;
      display: flex;
    }

    .sidebar {
      width: 250px;
      background: #f0f2f5;
      height: 100vh;
      padding: 20px;
      display: flex;
      flex-direction: column;
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
      padding-top: 7%;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin-top: 40px;
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

    .sidebar ul li.active,
    .sidebar ul li:hover {
      background-color: #6785f5;
      color: white;
    }

    .sidebar ul li.logout {
      margin-top: 110%;
    }

    .sidebar ul li.logout:hover {
      background-color: #f66;
      color: white;
    }

    .main-content {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .grades-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .grades-table th, .grades-table td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }

    .grades-table th {
      background-color: #6785f5;
      color: white;
    }

    .grades-table tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .grades-table tr:hover {
      background-color: #e0e0e0;
    }

    .btn {
      padding: 8px 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-danger {
      background-color: #f66;
      color: white;
    }
    .btn-danger:hover {
      background-color: #d32f2f;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div>
        <div class="logo">
          <img src="logo.png" alt="G" class="g-image">
          <span class="logo-text">rade Tracker</span>
        </div>
        <nav>
          <ul>
            <li><a href="teacher-dashboard.php"><i class="fas fa-th-large"></i>Dashboard</a></li>
            <li><a href="subjects.php"><i class="fas fa-book"></i>Subjects</a></li>
            <li><a href="participants.php"><i class="fas fa-user"></i>Participants</a></li>
            <li><a href="grades.php"><i class="fas fa-file-alt"></i>Grades</a></li>
            <li class="active"><i class="fas fa-list-alt"></i>View Grades</li>
          </ul>
        </nav>
      </div>
      <ul>
        <li class="logout"><a href="logout.php">⏻ Log out</a></li>
      </ul>
    </aside>

    <!-- Main content -->
    <div class="main-content">
      <h2>Submitted Grades</h2>
      <?php if (!empty($grades_by_course)): ?>
        <?php foreach ($grades_by_course as $course => $grades): ?>
          <table class="grades-table">
            <thead>
              <tr>
                <th colspan="5" style="background:#333; color:#fff; text-align:left;">Course: <?php echo htmlspecialchars($course); ?></th>
              </tr>
              <tr>
                <th>Student ID</th>
                <th>Course Code</th>
                <th>Grade</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($grades as $grade): ?>
                <tr>
                  <td><?php echo htmlspecialchars($grade['student_id']); ?></td>
                  <td><?php echo htmlspecialchars($grade['course_code']); ?></td>
                  <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                  <td><?php echo htmlspecialchars($grade['status']); ?></td>
                  <td>
                    <form method="get" action="view-grades.php" onsubmit="return confirm('Are you sure you want to delete this grade?');" style="display:inline;">
                      <input type="hidden" name="delete_student_id" value="<?php echo htmlspecialchars($grade['student_id']); ?>">
                      <input type="hidden" name="delete_course_code" value="<?php echo htmlspecialchars($grade['course_code']); ?>">
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No grades submitted yet.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
