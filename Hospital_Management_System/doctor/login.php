<?php
require_once '../config.php';

if (!empty($_SESSION['doctor_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $mysqli->real_escape_string($_POST['email'] ?? '');
    $passHash = hash('sha256', $_POST['password'] ?? '');

    $stmt = $mysqli->prepare(
        "SELECT id, name
         FROM doctors
         WHERE email = ? AND password = ?"
    );
    $stmt->bind_param('ss', $email, $passHash);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $doc = $res->fetch_assoc();

        $_SESSION['doctor_logged_in'] = true;
        $_SESSION['doctor_id']       = $doc['id'];
        $_SESSION['doctor_name']     = $doc['name'];

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Login</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-image: url('../img/loginDoctor.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .login-container {
      width: 380px;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
      padding: 30px;
      margin: 20px;
    }
    
    .login-container h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
      font-size: 28px;
    }
    
    .login-container form {
      display: flex;
      flex-direction: column;
    }
    
    .login-container label {
      margin-bottom: .5rem;
      color: #34495e;
      font-weight: bold;
    }
    
    .login-container input {
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      transition: border-color 0.3s;
    }
    
    .login-container input:focus {
      outline: none;
      border-color: #3498db;
      box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
    }
    
    .login-container button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    
    .login-container button:hover {
      background-color: #2980b9;
    }
    
    .error {
      color: #e74c3c;
      text-align: center;
      background-color: rgba(231, 76, 60, 0.1);
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    
    .admin-link {
      text-align: center;
      margin-top: 20px;
    }
    
    .admin-link a {
      color: #3498db;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s;
    }
    
    .admin-link a:hover {
      color: #2980b9;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Doctor Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" novalidate>
      <label>
        Email
        <input type="email" name="email" required placeholder="Enter your email">
      </label>
      <label>
        Password
        <input type="password" name="password" required placeholder="Enter your password">
      </label>
      <button type="submit">Login</button>
    </form>
    <p class="admin-link">
      <a href="../admin/login.php">Admin Login</a>
    </p>
  </div>
</body>
</html>
