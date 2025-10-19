<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Subjects</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
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
  align-items: center; /* Ensure items align horizontally */
}

.sidebar ul li a {
  text-decoration: none;
  color: inherit;
  display: flex;
  align-items: center; /* Keep the icon and text aligned */
}

.sidebar ul li i {
  margin-right: 10px; /* Adds space between icon and text */
}

.sidebar ul li.active,
.sidebar ul li:hover {
  background-color: #6785f5;
  color: white;
}

.sidebar ul li.logout {
  margin-top: 130%;
}

.sidebar ul li.logout:hover {
  background-color: #f66;
  color: white;
}
    .main {
      flex: 1;
      padding: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th{
      background-color: #6785f5;
      color: white;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    .add-btn {
      padding: 8px 12px;
      background: #4285f4;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 300px;
    }

    .modal-content input {
      width: 100%;
      padding: 3px;
      margin-bottom: 10px;
    }

    .modal-content button {
      padding: 8px 12px;
      margin-right: 10px;
    }

    .modal-content button:first-of-type {
      background-color: #6785f5;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .modal-content button:first-of-type:hover {
      background-color: #4b71fb;
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
    <li class="active"><i class="fas fa-book"></i> Subjects</li>
    <li><a href="participants.php"><i class="fas fa-user"></i> Participants</a></li>
    <li><a href="grades.php"><i class="fas fa-file-alt"></i> Grades</a></li>
    <li><a href="view-grades.php"><i class="fas fa-list-alt"></i>View Grades</a></li>
    <li class="logout"><a href="logout.php">⏻ Log out</a></li>
  </ul>
</div>

<!-- Main content -->
<div class="main">
  <h2>Subjects <button class="add-btn" onclick="openModal()">Add Subject</button></h2>

  <!-- Subject Table -->
  <table id="subjectsTable">
    <thead>
      <tr>
        <th>Course Code</th>
        <th>Subject Name</th>
      </tr>
    </thead>
    <tbody>
  <?php
  session_start();
  include 'database.php';

  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Teacher') {
      echo "Unauthorized";
      exit();
  }

  $teacher_id = $_SESSION['user_id'];
  $sql = "SELECT course_code, subject_name FROM subjects WHERE teacher_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $teacher_id);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
      echo "<tr><td>" . htmlspecialchars($row['course_code']) . "</td><td>" . htmlspecialchars($row['subject_name']) . "</td></tr>";
  }

  $stmt->close();
  $conn->close();
  ?>
</tbody>
  </table>
</div>


<div id="subjectModal" class="modal">
  <div class="modal-content">
    <h3>Add Teaching Subject</h3>
    <form id="subjectForm" method="POST" action="add-subject.php">
      <input type="text" name="course_code" placeholder="Course Code" required>
      <input type="text" name="subject_name" placeholder="Subject Name" required>

      <button type="submit">Add</button>
      <button type="button" onclick="closeModal()">Cancel</button>
    </form>
  </div>
</div>

<script>
  function openModal() {
    document.getElementById('subjectModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('subjectModal').style.display = 'none';
  }

  // Optional: Close modal if user clicks outside of modal content
  window.onclick = function(event) {
    const modal = document.getElementById('subjectModal');
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  }
</script>

</body>
</html>

