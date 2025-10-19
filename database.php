<?php
$servername = "sql109.infinityfree.com";
$username = "if0_40150596";
$password = "Xnhu1XfzEkLNd8Y";
$dbname = "if0_40150596_grade_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>