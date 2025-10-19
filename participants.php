<?php
session_start();
$conn = new mysqli("sql109.infinityfree.com", "if0_40150596", "Xnhu1XfzEkLNd8Y", "if0_40150596_grade_tracker");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get only subjects for this teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Teacher') {
    echo "Unauthorized";
    exit();
}
$teacher_id = $_SESSION['user_id'];
$subjects_sql = "SELECT * FROM subjects WHERE teacher_id = ?";
$stmt = $conn->prepare($subjects_sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$subjects = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Participants</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body { font-family: sans-serif; margin: 0; display: flex; }
    .sidebar { width: 250px; background: #f0f2f5; height: 100vh; padding: 20px; display: flex; flex-direction: column; }
    .logo { display: flex; align-items: center; font-size: 25px; font-weight: bold; }
    .g-image { height: 35px; margin-right: 0px; }
    .logo-text { font-size: 25px; color: #6785f5; padding-top: 7%; }
    .sidebar ul { list-style: none; padding: 0; margin-top: 40px; }
    .sidebar ul li { padding: 10px; margin-bottom: 10px; border-radius: 6px; display: flex; align-items: center; }
    .sidebar ul li a { text-decoration: none; color: inherit; display: flex; align-items: center; }
    .sidebar ul li i { margin-right: 10px; }
    .sidebar ul li.active, .sidebar ul li:hover { background-color: #6785f5; color: white; }
    .sidebar ul li.logout { margin-top: auto; }
    .sidebar ul li.logout:hover { background-color: #f66; color: white; }
    .main { flex: 1; padding: 30px; background: #f9f9f9; }
    .table-container { margin-bottom: 40px; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .subject-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .subject-title { color: #6785f5; font-size: 20px; font-weight: bold; }
    .subject-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    .subject-table th { background-color: #6785f5; color: white; }
    .assign-btn {
      background-color:rgb(2, 11, 44);
      color: #fff;
      padding: 6px 14px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
      transition: background 0.2s;
    }
    .assign-btn:hover {
      background-color: #4663c6;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="logo">
    <img src="logo.png" alt="G" class="g-image">
    <span class="logo-text">rade Tracker</span>
  </div>
  <ul>
    <li><a href="teacher-dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
    <li><a href="subjects.php"><i class="fas fa-book"></i> Subjects</a></li>
    <li class="active"><i class="fas fa-user"></i> Participants</li>
    <li><a href="grades.php"><i class="fas fa-file-alt"></i> Grades</a></li>
    <li><a href="view-grades.php"><i class="fas fa-list-alt"></i> View Grades</a></li>
    <li class="logout"><a href="logout.php">⏻ Log out</a></li>
  </ul>
</div>

<!-- Main Content -->
<div class="main">
  <h2>Participants by Subject</h2>

  <?php while ($row = $subjects->fetch_assoc()): ?>
    <?php
      $subject_name = $row['subject_name'];
      $course_code = $row['course_code'];

      // Only students for this subject (which already belongs to this teacher)
      $students_sql = "SELECT st.student_id, st.name
                       FROM student_subject ss
                       JOIN students st ON ss.student_id = st.student_id
                       WHERE ss.course_code = ?";
      $stmt2 = $conn->prepare($students_sql);
      $stmt2->bind_param("s", $course_code);
      $stmt2->execute();
      $students = $stmt2->get_result();
    ?>
    <div class="table-container">
      <div class="subject-header">
        <span class="subject-title"><?php echo $subject_name . " (" . $course_code . ")"; ?></span>
        <span class="participant-count"><?php echo $students->num_rows; ?> participants</span>
      </div>
      <table class="subject-table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($student = $students->fetch_assoc()): ?>
            <?php
              // Check if grade exists for this student and subject
              $grade_check_sql = "SELECT grade FROM grades WHERE student_id = ? AND course_code = ?";
              $grade_stmt = $conn->prepare($grade_check_sql);
              $grade_stmt->bind_param("ss", $student['student_id'], $course_code);
              $grade_stmt->execute();
              $grade_stmt->store_result();
              $has_grade = $grade_stmt->num_rows > 0;
              $grade_stmt->close();
            ?>
            <tr>
              <td><?php echo htmlspecialchars($student['student_id']); ?></td>
              <td><?php echo htmlspecialchars($student['name']); ?></td>
              <td>
                <a href="grades.php?student_id=<?php echo urlencode($student['student_id']); ?>&course_code=<?php echo urlencode($course_code); ?>" class="assign-btn">
                  Assign Grade
                </a>
                <?php if ($has_grade): ?>
                  <span style="color: green; font-size: 18px; margin-left: 8px;" title="Grade submitted">&#10003;</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
