<?php
$course_code = $_GET['course_code'];

$conn = new mysqli("localhost", "root", "", "grade_tracker");

$sql = "SELECT subject_name FROM subjects WHERE course_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $course_code);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
}

echo json_encode($subjects);
?>
