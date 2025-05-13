<?php
// header.php – expect $pageTitle to be set before include
if (!isset($pageTitle)) {
    $pageTitle = 'Hospital Management';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="../public/css/style.css">
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2c3e50;
      --text-light: #ecf0f1;
      --hover-color: #1abc9c;
      --shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f7fa;
    }
    
    header.navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.8rem 2rem;
      background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
      color: var(--text-light);
      box-shadow: var(--shadow);
    }
    
    header.navbar .logo {
      font-size: 1.8rem;
      text-decoration: none;
      color: var(--text-light);
      font-weight: bold;
      letter-spacing: 1px;
      transition: all 0.3s ease;
    }
    
    header.navbar .logo:hover {
      color: #fff;
      text-shadow: 0 0 10px rgba(255,255,255,0.5);
    }
    
    header.navbar nav {
      display: flex;
      gap: 1.2rem;
    }
    
    header.navbar nav a {
      text-decoration: none;
      color: var(--text-light);
      font-weight: 500;
      padding: 0.5rem 0.8rem;
      border-radius: 4px;
      transition: all 0.3s ease;
    }
    
    header.navbar nav a:hover {
      background-color: var(--hover-color);
      color: white;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <header class="navbar">
    <a href="dashboard.php" class="logo">HospitalMS</a>
    <nav>
      <a href="dashboard.php">Dashboard</a>
      <a href="manage_departments.php">Department</a>
      <a href="manage_doctors.php">Doctors</a>
      <a href="manage_patients.php">Patients</a>
      <a href="appointments.php">Appointments</a>
      <a href="../logout.php">Logout</a>
    </nav>
  </header>
</body>
</html>
