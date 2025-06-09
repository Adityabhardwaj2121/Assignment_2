<?php
session_start();
include "db.php";

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            $error = "Wrong password";
        }
    } else {
        $error = "Username not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Product System</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #e0eafc, #cfdef3);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background-color: #ffffff;
      padding: 35px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 350px;
    }

    .login-box h1 {
      margin: 0 0 20px;
      font-size: 24px;
      text-align: center;
      color: #333;
    }

    .error {
      background-color: #ffe0e0;
      color: #cc0000;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 20px;
      text-align: center;
      font-size: 14px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
      color: #555;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px 12px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      transition: border 0.3s ease;
    }

    input:focus {
      border-color: #4d90fe;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      background-color: black;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 10px;
    }

   

    .register-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #666;
    }

    .register-link a {
      color: #4d90fe;
      text-decoration: none;
    }

    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="login-box">
  <h1>Login</h1>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>
    </div>

    <button type="submit">Log In</button>
  </form>

  <div class="register-link">
    Don't have an account? <a href="register.php">Sign up</a>
  </div>
</div>

</body>
</html>
