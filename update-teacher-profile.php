<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Teacher') {
    header("Location: login.php");
    exit();
}
$teacher_id = $_SESSION['user_id'];



$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];

$photo_query = "";
if ($_FILES['profile_photo']['name']) {
    $photo_name = basename($_FILES['profile_photo']['name']);
    $target_path = "uploads/" . $photo_name;
    move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_path);
    $photo_query = ", profile_photo='$photo_name'";
}

$password_query = "";
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $password_query = ", password='$hashed_password'";
}

$sql = "UPDATE teachers SET name='$name', email='$email', phone='$phone' $password_query $photo_query WHERE teacher_id='$teacher_id'";
mysqli_query($conn, $sql);

header("Location: teacher-dashboard.php?msg=updated");
exit();

