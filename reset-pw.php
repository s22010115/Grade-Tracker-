<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfdfd;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background-color: white;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      width: 400px;
      text-align: center;
    }

    h2 {
      margin: 0;
      font-size: 20px;
      font-weight: 600;
      color: #333;
    }

    label {
      display: block;
      text-align: left;
      margin-top: 20px;
      font-weight: 500;
      color: #444;
    }

    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 20px;
      margin-top: 8px;
      font-size: 14px;
      outline: none;
    }

    .reset-btn {
      width: 100%;
      background-color: #6785f5;
      color: white;
      border: none;
      padding: 14px;
      border-radius: 30px;
      margin-top: 20px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .reset-btn:hover {
      background-color: #516fde;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Create New Password</h2>
    <form method="POST" action="reset-password.php">
      <label>New Password</label>
      <input type="password" name="password" placeholder="Enter new password" required>
      
      <label>Confirm New Password</label>
      <input type="password" name="confirm_password" placeholder="Confirm new password" required>

      <!-- Optionally, include a hidden input for the reset token or email -->
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">

      <button type="submit" class="reset-btn">Reset Password</button>
    </form>
  </div>
</body>
</html>


