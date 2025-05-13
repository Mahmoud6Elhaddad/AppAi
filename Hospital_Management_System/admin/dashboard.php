<?php
require_once '../config.php';

// Redirect if not logged in
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
$res=$mysqli->query("SELECT * FROM patients");
// Fetch statistics
$totalDoctors      = $mysqli->query("SELECT COUNT(*) AS cnt FROM doctors")->fetch_assoc()['cnt'];
$totalPatients     = $mysqli->query("SELECT COUNT(*) AS cnt FROM patients")->fetch_assoc()['cnt'];
$totalAppointments = $mysqli->query("SELECT COUNT(*) AS cnt FROM appointments")->fetch_assoc()['cnt'];

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../public/css/style.css">
  <style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    
    h1 {
      color: #2c3e50;
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 10px;
      border-bottom: 2px solid #3498db;
    }
    
    .stats { 
      display: flex; 
      gap: 1.5rem; 
      margin-bottom: 2.5rem; 
      flex-wrap: wrap;
    }
    
    .card {
      flex: 1;
      min-width: 200px;
      padding: 1.5rem;
      background: white;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    
    .card h2 { 
      margin: 0.5rem 0; 
      font-size: 2.5rem; 
      color: #3498db;
    }
    
    .card p {
      color: #7f8c8d;
      font-size: 1.1rem;
      margin-top: 5px;
    }
    
    nav ul { 
      list-style: none; 
      padding: 0; 
      text-align: center;
    }
    
    nav li { 
      display: inline-block; 
      margin-right: 1rem; 
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    
    th {
      background-color: #3498db;
      color: white;
      padding: 12px 15px;
      text-align: left;
    }
    
    td {
      padding: 10px 15px;
      border-bottom: 1px solid #ddd;
    }
    
    tr:last-child td {
      border-bottom: none;
    }
    
    tr:hover {
      background-color: #f5f5f5;
    }
    
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>
  <div class="container">
  <h1>Welcome, Admin!</h1>

  <div class="stats">
    <div class="card">
      <h2><?= $totalDoctors ?></h2>
      <p>Doctors</p>
    </div>
    <div class="card">
      <h2><?= $totalPatients ?></h2>
      <p>Patients</p>
    </div>
    <div class="card">
      <h2><?= $totalAppointments ?></h2>
      <p>Appointments</p>
    </div>
  </div>
  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Age</th></tr>
    <?php while($r=$res->fetch_assoc()): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= $r['name'] ?></td>
      <td><?= $r['email'] ?></td>
      <td><?= $r['age'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
  </div>
</body>
</html>
