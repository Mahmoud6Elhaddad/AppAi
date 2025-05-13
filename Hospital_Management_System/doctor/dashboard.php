<?php
require_once '../config.php';
if (empty($_SESSION['doctor_logged_in'])) {
  header('Location: login.php');
  exit;
}
$docId = $_SESSION['doctor_id'];

$res = $mysqli->query("
  SELECT a.*, p.name patient
  FROM appointments a
  JOIN patients p ON a.patient_id=p.id
  WHERE a.doctor_id=$docId
  ORDER BY a.appt_date,a.appt_time
");

?>
<!DOCTYPE html>
<html>
<head>
  <title>Doctor Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    
    h2 {
      color: #2c3e50;
      border-bottom: 2px solid #3498db;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      background-color: white;
    }
    
    th {
      background-color: #3498db;
      color: white;
      padding: 12px;
      text-align: left;
    }
    
    td {
      padding: 10px 12px;
      border-bottom: 1px solid #ddd;
    }
    
    tr:hover {
      background-color: #f1f9ff;
    }
    
    tr:nth-child(even) {
      background-color: #f8f9fa;
    }
    
    a {
      display: inline-block;
      color: #3498db;
      text-decoration: none;
      font-weight: bold;
      margin-top: 10px;
    }
    
    a:hover {
      color: #2980b9;
      text-decoration: underline;
    }
    
    .logout-btn {
      background-color: #3498db;
      color: white;
      padding: 8px 15px;
      border-radius: 4px;
      transition: background-color 0.3s;
    }
    
    .logout-btn:hover {
      background-color: #2980b9;
      text-decoration: none;
    }
    
    .status-confirmed {
      color: #27ae60;
      font-weight: bold;
    }
    
    .status-pending {
      color: #f39c12;
      font-weight: bold;
    }
    
    .status-cancelled {
      color: #e74c3c;
      font-weight: bold;
    }
    
    .dashboard-container {
      max-width: 1000px;
      margin: 0 auto;
      background-color: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
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
    <a href="dashboard.php" class="logo">Doctor Dashboard</a>
    <nav>
     
    </nav>
  </header>
<div class="container">
  <h2>Your Appointments</h2>
  <table>
    <tr>
      <th>Date</th>
      <th>Time</th>
      <th>Patient</th>
      <th>Status</th>
    </tr>
    <?php while($r=$res->fetch_assoc()): 
      $statusClass = '';
      if(strtolower($r['status']) == 'confirmed') {
        $statusClass = 'status-confirmed';
      } else if(strtolower($r['status']) == 'pending') {
        $statusClass = 'status-pending';
      } else if(strtolower($r['status']) == 'cancelled') {
        $statusClass = 'status-cancelled';
      }
    ?>
    <tr>
      <td><?= $r['appt_date'] ?></td>
      <td><?= $r['appt_time'] ?></td>
      <td><?= $r['patient'] ?></td>
      <td class="<?= $statusClass ?>"><?= $r['status'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
  <p><a href="logout.php" class="logout-btn">Logout</a></p>
</div>
</body>
</html>
