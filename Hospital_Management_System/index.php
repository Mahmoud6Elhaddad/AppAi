<?php
require_once "config.php";?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hospital MS</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('img/indexBackground.jpg') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-container {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      padding: 40px 60px;
      text-align: center;
      max-width: 360px;
      width: 100%;
    }
    .login-container h1 {
      margin-bottom: 24px;
      color: #333;
      font-size: 1.8em;
    }
    .btn {
      display: inline-block;
      text-decoration: none;
      padding: 12px 24px;
      margin: 8px 4px;
      border-radius: 6px;
      font-size: 1em;
      font-weight: 600;
      transition: transform 0.2s, box-shadow 0.2s;
      color: #fff;
    }
    .btn-admin {
      background: #ff6b6b;
      box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
    }
    .btn-doctor {
      background: #1dd1a1;
      box-shadow: 0 4px 12px rgba(29, 209, 161, 0.4);
    }
    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Hospital MS</h1>
    <a href="admin/login.php"  class="btn btn-admin">Admin Login</a>
    <a href="doctor/login.php" class="btn btn-doctor">Doctor Login</a>
  </div>
</body>
</html>