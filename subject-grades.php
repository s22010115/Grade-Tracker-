<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch student info
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$Student = $result->fetch_assoc();
$stmt->close();

// Fetch grades and subjects for this student
$sql = "SELECT g.course_code, s.subject_name, g.grade, g.status
        FROM grades g
        JOIN subjects s ON g.course_code = s.course_code
        WHERE g.student_id = ?";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$grades_result = $stmt2->get_result();

$grades = [];
$total_gpa = 0;
$count_gpa = 0;

// GPA calculation map
$gpa_map = [
    'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
    'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
    'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
    'D+' => 1.3, 'D' => 1.0, 'E' => 0.0,
];

while ($row = $grades_result->fetch_assoc()) {
    $grades[] = $row;
    // Calculate GPA if grade is in map
    if (isset($gpa_map[$row['grade']])) {
        $total_gpa += $gpa_map[$row['grade']];
        $count_gpa++;
    }
}
$gpa = $count_gpa > 0 ? round($total_gpa / $count_gpa, 2) : 'N/A';
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Grade Tracker Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, sans-serif; }
    .container { display: flex; min-height: 100vh; }
    .logo { display: flex; align-items: center; font-size: 25px; font-weight: bold; }
    .g-image { height: 35px; margin-right: 0px; }
    .logo-text { font-family: sans-serif; font-size: 25px; color: #6785f5; padding-top: 8%; }
    .sidebar {
      width: 250px;
      background-color: #f4f6f8;
      padding: 20px;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .sidebar h2 { font-size: 16px; margin-bottom: 30px; }
    .sidebar ul { list-style: none; padding: 0; margin: 0; margin-top: 40px; }
    .sidebar ul li { padding: 10px; margin-bottom: 10px; cursor: pointer; border-radius: 6px; }
    .sidebar ul li a { text-decoration: none; color: inherit; display: block; }
    .sidebar ul li.active, .sidebar ul li:hover { background-color: #6785f5; color: white; }
    .sidebar ul li.logout { margin-top: auto; }
    .sidebar ul li.logout:hover { background-color: #f66; color: white; }
    .main-content { flex: 1; padding: 20px; background-color: #fff; }
    header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
    .profile-picture img {
      width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid rgb(0, 4, 15);
    }
    .user-details h2 { margin: 0; }
    .user-details a {
      text-decoration: none; background: #6785f5; color: white; padding: 6px 12px; border-radius: 6px;
      font-size: 14px; display: inline-block; margin-top: 5px;
    }
    .section { flex: 1; padding: 24px; text-align: center; display: flex; flex-direction: column; justify-content: center; }
    .section h2 { font-size: 28px; margin-bottom: 24px; display: flex; justify-content: center; align-items: center; gap: 8px; }
    table { width: 100%; margin: 0 auto 20px; border-collapse: separate; border-spacing: 0 12px; }
    th, td {
      padding: 16px; background-color: #fff; border: 1px solid #ddd; text-align: center;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    th { background-color: #e2e8f0; font-weight: bold; color: #333; border-bottom: 2px solid #cbd5e1; }
    tbody tr:hover { background-color: #f1f5f9; transition: background-color 0.3s ease; }
    .gpa {
      color: #1c8c34;
      font-size: 22px;
      font-weight: bold;
      margin: 20px auto 0 auto;
      text-align: center;
    }
    .buttons {
      display: flex;
      justify-content: center;
      gap: 40px;
      padding-bottom: 0;
      margin-top: 24px;
    }
    .buttons button { padding: 12px 24px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; transition: background-color 0.2s ease-in-out; }
    .export-btn { background-color: #2563eb; color: #fff; }
    .export-btn:hover { background-color: #1d4ed8; }
    .view-btn { background-color: #e5e7eb; }
    .view-btn:hover { background-color: #d1d5db; }
    .logout { text-align:left; padding: 16px; }
    .logout button:hover { background-color: #ec1919; }
    .modal {
      display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
      background-color: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      z-index: 10; max-width: 300px; text-align: center;
    }
    .modal.show { display: block; }
    .modal h3 { margin-bottom: 16px; }
    .modal button {
      padding: 8px 16px; background-color: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;
    }
    .modal button:hover { background-color: #1d4ed8; }
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
        <li><a href="student-dashboard.php"><i class="fa fa-plus"></i> Add Subjects</a></li>
        <li class="active"><i class="fas fa-chart-bar"></i> Subject Grades</li>
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
    <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;">
      <div class="profile-picture" style="margin-right: 10px;">
         <img src="<?php echo !empty($Student['profile_photo']) ? 'uploads/' . htmlspecialchars($Student['profile_photo']) : 'default-profile.png'; ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solidrgb(0, 4, 15);">
      </div>
      <div class="user-details">
    <h2 style="margin: 0;"><?php echo htmlspecialchars($Student['name']); ?></h2>
      <a href="edit-student-profile.php" style="text-decoration: none; background: #6785f5; color: white; padding: 6px 12px; border-radius: 6px; font-size: 14px; display: inline-block; margin-top: 5px;">Edit Profile</a>
    </div>
    </div>
    <h2>Subject Grades</h2>
    <p>View your subject grades below.</p>
    <div class="section">
      <table>
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Subjects</th>
            <th>Grade</th>
            <th>Progress Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($grades) > 0): ?>
            <?php foreach ($grades as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                <td><?php echo htmlspecialchars($row['grade']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">No grades available.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="gpa" style="margin: 20px auto 0 auto; text-align: center;">GPA: <?php echo $gpa; ?></div>
      <div class="buttons" style="justify-content: center; padding-bottom: 0;">
        <form method="post" action="export-report-card.php" style="display:inline;">
          <button type="submit" class="export-btn">⬇ Export Report Card</button>
        </form>
      </div>
    </div>
    <!-- Modal -->
    <div class="modal" id="exportModal">
      <h3>✅ Report Card Exported!</h3>
      <p>Your report card has been successfully exported.</p>
      <button onclick="closeModal()">Close</button>
    </div>
  </main>
</div>
<script>
  function openModal() {
    document.getElementById("exportModal").classList.add("show");
  }
  function closeModal() {
    document.getElementById("exportModal").classList.remove("show");
  }
</script>
</body>
</html>






