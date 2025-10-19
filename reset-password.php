<?php
include 'database.php';

$email = $_POST['email'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password !== $confirm) {
    echo "Passwords do not match.";
    exit();
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

// Try to update in students table
$stmt = $conn->prepare("UPDATE students SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed, $email);
$stmt->execute();
$student_updated = $stmt->affected_rows > 0;
$stmt->close();

// Try to update in teachers table
$stmt = $conn->prepare("UPDATE teachers SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed, $email);
$stmt->execute();
$teacher_updated = $stmt->affected_rows > 0;
$stmt->close();

if ($student_updated || $teacher_updated) {
    header("Location: login.php?reset=success");
    exit();
} else {
    echo "No account found with that email.";
    exit();
}
?>