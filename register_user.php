<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; 

    if ($role == "Teacher") {
        $stmt = $conn->prepare("INSERT INTO teachers (name, email, phone, password) VALUES (?, ?, ?, ?)");
    } elseif ($role == "Student") {
        $stmt = $conn->prepare("INSERT INTO students (name, email, phone, password) VALUES (?, ?, ?, ?)");
    } else {
        echo "Invalid role selected.";
        exit();
    }

    $stmt->bind_param("ssss", $name, $email, $phone, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>



