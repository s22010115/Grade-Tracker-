<?php
session_start();
$conn = new mysqli("localhost", "root", "", "grade_tracker");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Teacher') {
    echo "Unauthorized";
    exit();
}
$teacher_id = $_SESSION['user_id'];

$sql = "
    SELECT ss.student_id, st.name AS student_name, ss.course_code, s.subject_name
    FROM student_subject ss
    JOIN students st ON ss.student_id = st.student_id
    JOIN subjects s ON ss.course_code = s.course_code
    LEFT JOIN grades g ON ss.student_id = g.student_id AND ss.course_code = g.course_code
    WHERE g.grade IS NULL AND s.teacher_id = ?
    ORDER BY ss.course_code, ss.student_id
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Missing Grades</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 30px;
      background-color: #f4f6f8;
    }

    h2 {
      color: #333;
      text-align: center;
      margin-bottom: 20px;
    }

    .grades-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    .grades-table th, .grades-table td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    .grades-table th {
      background-color: #f66;
      color: white;
      font-weight: bold;
    }

    .grades-table tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .grades-table tr:hover {
      background-color: #f1f1f1;
    }

    .assign-btn {
      background-color: rgb(2, 11, 44);
      color: #fff;
      padding: 6px 14px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
      transition: background 0.2s;
      border: none;
      cursor: pointer;
      display: inline-block;
    }

    .assign-btn:hover {
      background-color: #4663c6;
    }

    .empty-msg {
      text-align: center;
      padding: 30px;
      color: #888;
      font-size: 18px;
    }
  </style>
</head>
<body>

  <h2><i class="fas fa-exclamation-circle"></i> Missing Grades</h2>

  <table class="grades-table">
    <thead>
      <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Course Code</th>
        <th>Subject</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['course_code']); ?></td>
            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
            <td>
              <a href="grades.php?student_id=<?php echo urlencode($row['student_id']); ?>&course_code=<?php echo urlencode($row['course_code']); ?>" class="assign-btn">
                Assign Grade
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="empty-msg">🎉 All students have been graded!</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>

