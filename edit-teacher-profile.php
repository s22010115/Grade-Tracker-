<?php
session_start();
include 'database.php';


if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Teacher') {
    $teacher_id = $_SESSION['user_id'];
} else {
    die("Access denied. Only teachers can access this page.");
}

$stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
?>
<head>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 500px;
        margin: 50px auto;
        background-color: #ffffff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }

    img {
        display: block;
        margin: 10px auto;
        border-radius: 8px;
    }

    button {
        background-color: #007bff;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        width: 100%;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>
<title>Edit Profile</title>
</head>
<h2>Edit Profile</h2>
<div class="container">
    <h2>Edit Profile</h2>
    <form action="update-teacher-profile.php" method="POST" enctype="multipart/form-data">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($teacher['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($teacher['phone']) ?>">

        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="password">

        <label>Current Photo</label>
        <?php if (!empty($teacher['profile_photo'])) : ?>
            <img src="uploads/<?= htmlspecialchars($teacher['profile_photo']) ?>" width="100">
        <?php endif; ?>

        <input type="file" name="profile_photo">

        <button type="submit" name="update">Update Profile</button>
    </form>
</div>

