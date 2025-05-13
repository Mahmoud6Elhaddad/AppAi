<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $mysqli->real_escape_string($_POST['username']);
    $p = hash('sha256', $_POST['password']);
    $res = $mysqli->query("SELECT * FROM admin WHERE username='$u' AND password='$p'");
    if ($res->num_rows === 1) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('../img/loginAdmin.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            width: 320px;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 24px;
            color: #333;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .login-container button:hover {
            background-color: #218838;
        }
        .login-container .error {
            margin-top: 12px;
            color: #c0392b;
            font-size: 14px;
        }
        .login-container .switch-link {
            margin-top: 20px;
            font-size: 14px;
        }
        .login-container .switch-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-container .switch-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="switch-link">
            <a href="../doctor/login.php">Doctor Login</a>
        </div>
    </div>
</body>
</html>
