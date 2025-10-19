<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grade Tracker - Login</title>
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
      width: 90%;
      max-width: 400px;
      text-align: center;
    }

    .logo {
      font-size: 28px;
      font-weight: bold;
      color: #6785f5;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 30px;
    }

    .highlight-g {
      position: relative;
      padding-left: 35px; /* Space for the image */
      font-size: 28px;
      font-weight: bold;
      color: #6785f5;
    }

    .cap-icon-inside-g {
      position: absolute;
      left: 0;
      width: 35px;
      height: 35px;
      margin-top: -2%;
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
      margin-top: 22px;
      font-weight: 500;
      color: #444;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 20px;
      margin-top: 8px;
      font-size: 14px;
      outline: none;
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      margin-top: 10px;
    }

    .remember-forgot input {
      margin-right: 5px;
      accent-color: #6785f5;
    }

    .remember-forgot a {
      color: #6785f5;
      text-decoration: none;
    }

    .sign-in-btn {
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

    .sign-in-btn:hover {
      background-color: #516fde;
    }

    .divider {
      margin: 20px 0;
      text-align: center;
      color: #aaa;
      font-size: 14px;
      position: relative;
    }

    .divider::before,
    .divider::after {
      content: '';
      position: absolute;
      width: 40%;
      height: 1px;
      background-color: #ddd;
      top: 50%;
    }

    .divider::before {
      left: 0;
    }

    .divider::after {
      right: 0;
    }

    .social-icons {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 10px;
    }

    .social-icons img {
      width: 35px;
      height: 35px;
      cursor: pointer;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 5px;
      transition: 0.3s;
    }

    .social-icons img:hover {
      background-color: #eee;
    }

    .signup {
      font-size: 14px;
      margin-top: 15px;
    }

    .signup a {
      color: #6785f5;
      text-decoration: none;
      font-weight: bold;
    }
    .error {
            color: red;
            margin-bottom: 10px;
        }

    /* Media Queries for Responsiveness */
    @media (max-width: 600px) {
      body {
        height: 100vh;
        padding: 20px;
      }

      .container {
        padding: 20px;
        margin: 10px;
        border-radius: 15px;
      }

      .logo {
        font-size: 24px;
        margin-bottom: 20px;
      }

      .highlight-g {
        font-size: 24px;
        padding-left: 30px;
      }

      .cap-icon-inside-g {
        width: 30px;
        height: 30px;
      }

      h2 {
        font-size: 18px;
      }

      label {
        font-size: 14px;
        margin-top: 15px;
      }

      input[type="email"],
      input[type="password"] {
        padding: 10px;
        font-size: 16px; /* Prevents zoom on iOS */
        margin-top: 5px;
      }

      .remember-forgot {
        font-size: 12px;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
      }

      .sign-in-btn {
        padding: 12px;
        font-size: 16px;
        margin-top: 15px;
      }

      .divider {
        margin: 15px 0;
      }

      .social-icons {
        gap: 20px;
      }

      .social-icons img {
        width: 30px;
        height: 30px;
        padding: 3px;
      }

      .signup {
        font-size: 12px;
        margin-top: 10px;
      }
    }

    @media (min-width: 601px) and (max-width: 1024px) {
      .container {
        padding: 30px;
        width: 80%;
        max-width: 450px;
      }

      .logo {
        font-size: 26px;
      }

      .highlight-g {
        font-size: 26px;
      }
    }

    @media (min-width: 1025px) {
      .container {
        width: 400px;
        padding: 40px;
      }
    }
  </style>
  

</head>
<body>
  <div class="container">
    <div class="logo">
      <span class="highlight-g">
        <img src="logo.png" class="cap-icon-inside-g" alt="logo">
        rade Tracker
      </span>
    </div>
    <h2>WELCOME BACK!</h2>
    <form id="loginForm" action="login_user.php" method="POST" autocomplete="off" >
      

    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="error"><?php echo $_SESSION['login_error']; ?></div>
        <?php unset($_SESSION['login_error']); ?> 
    <?php endif; ?>
      
      <label>Email</label>
      <input type="email" name="email" placeholder="123@gmail.com" value="<?php echo isset($_SESSION['old_email']) ? $_SESSION['old_email'] : ''; ?>" required>
<?php unset($_SESSION['old_email']); ?>

      
      <label>Password</label>
      <input type="password" name="password" autocomplete="off" placeholder="********" required>

      <div class="remember-forgot">
        <label><input type="checkbox"> Remember me</label>
        <a href="forget-pw.html">Forget password?</a>
      </div>

      <button type="submit" class="sign-in-btn">Sign In</button>

      <div class="signup">
        Don’t have an account? <a href="register.html">Sign up</a>
      </div>
    </form>
  </div>
</body>
</html>
