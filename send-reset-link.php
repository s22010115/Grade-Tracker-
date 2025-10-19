<?php
include 'database.php';

$userInput = $_POST['userInput'];

// Check in students table
$stmt = $conn->prepare("SELECT email FROM students WHERE email = ? OR name = ?");
$stmt->bind_param("ss", $userInput, $userInput);
$stmt->execute();
$stmt->bind_result($student_email);
$student_found = $stmt->fetch();
$stmt->close();

// Check in teachers table if not found in students
if (!$student_found) {
    $stmt = $conn->prepare("SELECT email FROM teachers WHERE email = ? OR name = ?");
    $stmt->bind_param("ss", $userInput, $userInput);
    $stmt->execute();
    $stmt->bind_result($teacher_email);
    $teacher_found = $stmt->fetch();
    $stmt->close();
}

$email = $student_found ? $student_email : ($teacher_found ? $teacher_email : null);

if ($email) {
    header("Location: reset-pw.php?email=" . urlencode($email));
    exit();
} else {
    echo "No account found with that email or username.";
}
?>