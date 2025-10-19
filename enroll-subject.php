<?php
session_start();
include 'database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    echo "Unauthorized";
    exit();
}

$student_id = $_SESSION['user_id'];
$course_code = $_POST['course_code'];
$subject_name = $_POST['subject_name'];

// Prevent duplicate enrollment
$check = $conn->prepare("SELECT * FROM student_subject WHERE student_id = ? AND course_code = ?");
$check->bind_param("is", $student_id, $course_code);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    echo "Already enrolled.";
    exit();
}

// Enroll student in subject (including subject_name)
$stmt = $conn->prepare("INSERT INTO student_subject (student_id, course_code, subject_name) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $student_id, $course_code, $subject_name);

if ($stmt->execute()) {
    echo "success";
    $stmt->close();
    exit();
} else {
    echo "Database error.";
    $stmt->close();
    exit();
}

