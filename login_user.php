<?php
include 'database.php';
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT teacher_id AS id, name, password, 'Teacher' AS role FROM teachers WHERE email = ?
        UNION
        SELECT student_id AS id, name, password, 'Student' AS role FROM students WHERE email = ?
    ");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_role'] = $row['role'];

            if ($row['role'] == 'Teacher') {
                header("Location: teacher-dashboard.php");
            } else {
                header("Location: student-dashboard.php");
            }
            $stmt->close();
            $conn->close();
            exit();
        } else {
           
            $_SESSION['login_error'] = "Invalid password!";
            $_SESSION['old_email'] = $email;


            $stmt->close();
            $conn->close();
            header("Location: login.php"); 
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No user found with this email!";
        $stmt->close();
        $conn->close();
        header("Location: login.php"); 
        exit();
    }
}
?>
