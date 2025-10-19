<?php
$conn = new mysqli("localhost", "root", "", "gradetracker");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';
$course_code = isset($_GET['course_code']) ? $_GET['course_code'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_code = $_POST['course_code'];
    $grade = $_POST['grade'];
    $status = $_POST['status'];

    // Optional: Add more fields if needed (e.g., subject, date, gpa)

    // Insert or update the grade
    $stmt = $conn->prepare("INSERT INTO grades (student_id, course_code, grade, status) VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE grade=VALUES(grade), status=VALUES(status)");
    $stmt->bind_param("ssss", $student_id, $course_code, $grade, $status);
    if ($stmt->execute()) {
        echo "<script>alert('Grade saved successfully!'); window.location='grades.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error saving grade: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grade Entry Form</title>
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
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .form-container {
      width: 50%;
      max-width: 700px;
      padding: 30px;
      background-color: #e4e4e4;
      border-radius: 8px;
      box-shadow: none;
      display: flex;
      flex-direction: column;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .student-entry {
      margin-bottom: 15px;
    }

    .student-entry label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    .student-entry input,
    .student-entry select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #ffffff;
      margin-bottom: 15px;
      box-sizing: border-box;
    }

    .btn-submit {
      width: 100%;
      background-color: #2563eb;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 15px;
    }

    .btn-submit:hover {
      background-color: #1d4ed8;
    }

    .gpa-container {
      margin-top: 15px;
      text-align: center;
      padding: 15px;
      background-color: #d1fae5;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="container">
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
            <li class="active"><i class="fas fa-file-alt"></i>Grades</li>
            <li><a href="view-grades.php"><i class="fas fa-list-alt"></i>View Grades</a></li>
          </ul>
        </nav>
      </div>
      <ul>
        <li class="logout"><a href="logout.php">⏻ Log out</a></li>
      </ul>
    </aside>

    <div class="main-content">
      <div class="form-container">
        <h2>Grade Entry Form</h2>
        <form id="gradeForm" method="POST" action="">
          <div class="student-entry">
            <label for="stNum">Student ID</label>
            <input type="text" id="stNum" name="student_id" placeholder="Enter Student ID" required value="<?php echo htmlspecialchars($student_id); ?>">
          </div>
          <div class="student-entry">
            <label for="code">Course Code</label>
            <input type="text" id="code" name="course_code" placeholder="Course Code" required value="<?php echo htmlspecialchars($course_code); ?>">
          </div>

          <div class="student-entry">
            <label for="grade">Grade</label>
            <select id="grade" name="grade" required>
              <option value="">Select Grade</option>
              <option value="A+">A+</option>
              <option value="A">A</option>
              <option value="A-">A-</option>
              <option value="B+">B+</option>
              <option value="B">B</option>
              <option value="B-">B-</option>
              <option value="C+">C+</option>
              <option value="C">C</option>
              <option value="C-">C-</option>
              <option value="D+">D+</option>
              <option value="D">D</option>
              <option value="E">E</option>
              <option value="FA">FA</option>
              <option value="RX">RX</option>
              <option value="—">—</option>
            </select>
          </div>

          <div class="student-entry">
            <label for="status">Progress Status</label>
            <select id="status" name="status" required>
              <option value="">Select Status</option>
              <option value="Pass">Pass</option>
              <option value="Repeat">Repeat</option>
              <option value="Resit">Resit</option>
              <option value="Pending">Pending</option>
            </select>
          </div>

          <button type="submit" class="btn-submit">Submit Grade</button>
        </form>

        <div id="gpaContainer" class="gpa-container" style="display:none;">
          <h3>Calculated GPA: <span id="gpaValue"></span></h3>
        </div>
      </div>
    </div>
  </div>

  <script>
  const gradeSelect = document.getElementById('grade');
  const statusSelect = document.getElementById('status');
  const gpaContainer = document.getElementById('gpaContainer');
  const gpaValue = document.getElementById('gpaValue');

  gradeSelect.addEventListener('change', function () {
    const selectedGrade = gradeSelect.value;
    const gpa = calculateGPA(selectedGrade);
    const status = getStatus(selectedGrade);
    statusSelect.value = status;
    displayGPA(gpa);
  });

  function calculateGPA(grade) {
    switch (grade) {
      case 'A+': case 'A': return 4.0;
      case 'A-': return 3.7;
      case 'B+': return 3.3;
      case 'B': return 3.0;
      case 'B-': return 2.7;
      case 'C+': return 2.3;
      case 'C': return 2.0;
      case 'C-': return 1.7;
      case 'D+': return 1.3;
      case 'D': return 1.0;
      case 'E': return 0.0;
      default: return 0.0;
    }
  }

  function getStatus(grade) {
    switch (grade) {
      case 'A+': 
      case 'A': 
      case 'A-':
      case 'B+':
      case 'B':
      case 'B-':
      case 'C+': 
      case 'C': return 'Pass';
      case 'FA': return 'Repeat';
      case '—': return 'Pending';
      default: return 'Resit';
    }
  }

  function displayGPA(gpa) {
    gpaValue.textContent = gpa.toFixed(2);
    gpaContainer.style.display = 'block';
  }
  </script>
  
</body>
</html>





