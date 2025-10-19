<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Teacher') {
    echo "Unauthorized";
    exit();
}

$teacher_id = $_SESSION['user_id'];
$course_code = $_POST['course_code'];
$subject_name = $_POST['subject_name'];

// Optional: Prevent duplicates
$check = $conn->prepare("SELECT * FROM subjects WHERE course_code = ? AND subject_name = ? AND teacher_id = ?");
$check->bind_param("ssi", $course_code, $subject_name, $teacher_id);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    echo "Subject already exists.";
    exit();
}

// Insert subject
$stmt = $conn->prepare("INSERT INTO subjects (course_code, subject_name, teacher_id) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $course_code, $subject_name, $teacher_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Database error.";
}
$stmt->close();
$conn->close();
?>
