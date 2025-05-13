<?php
require_once '../config.php';
if (!$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$msg = '';
// Handle form submission (INSERT new or UPDATE existing)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // collect + sanitize
    $doc    = (int) $_POST['doctor_id'];
    $pat    = (int) $_POST['patient_id'];
    $dt     = $_POST['appt_date'];
    $tm     = $_POST['appt_time'];
    $status = $_POST['status'];
    // If an ID is passed, we're updating
    if (!empty($_POST['id'])) {
        $id = (int) $_POST['id'];
        $stmt = $mysqli->prepare("
            UPDATE appointments
               SET doctor_id = ?, patient_id = ?, appt_date = ?, appt_time = ?, status = ?
             WHERE id = ?
        ");
        $stmt->bind_param('iisssi', $doc, $pat, $dt, $tm, $status, $id);
        $stmt->execute() or $err = $stmt->error;
        $msg = isset($err) ? $err : 'Appointment updated';
    } else {
        // inserting new
        $stmt = $mysqli->prepare("
            INSERT INTO appointments
                   (doctor_id, patient_id, appt_date, appt_time, status)
            VALUES (?,         ?,          ?,         ?,          ?)
        ");
        $stmt->bind_param('iisss', $doc, $pat, $dt, $tm, $status);
        $stmt->execute() or $err = $stmt->error;
        $msg = isset($err) ? $err : 'Appointment scheduled';
    }
}

// If we're editing, load that appointment's data
$editAppt = null;
if (isset($_GET['edit'])) {
    $eid = (int) $_GET['edit'];
    $res = $mysqli->query("SELECT * FROM appointments WHERE id = $eid LIMIT 1");
    if ($res && $res->num_rows) {
        $editAppt = $res->fetch_assoc();
    }
}

// Fetch data for selects and listing
$docs = $mysqli->query("SELECT id,name FROM doctors");
$pats = $mysqli->query("SELECT id,name FROM patients");
$apps = $mysqli->query("
  SELECT a.id, d.name AS doc, p.name AS pat, a.appt_date, a.appt_time, a.status
    FROM appointments a
    JOIN doctors   d ON a.doctor_id = d.id
    JOIN patients  p ON a.patient_id = p.id
 ORDER BY a.appt_date, a.appt_time
");

include __DIR__ . '/inc/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Appointments</title>
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
            margin-top: 30px;
        }
        
        form {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        select, input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 20px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        a {
            color: #3498db;
            text-decoration: none;
            margin-left: 15px;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th {
            background-color: #3498db;
            color: white;
            text-align: left;
            padding: 12px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        .status-scheduled {
            color: #2980b9;
            font-weight: bold;
        }
        
        .status-completed {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-cancelled {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .action-link {
            display: inline-block;
            padding: 5px 10px;
            background-color: #3498db;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .action-link:hover {
            background-color: #2980b9;
            text-decoration: none;
        }
    </style>
</head>
<body>
  <div class="container">
<h2><?= $editAppt ? 'Edit Appointment #'. $editAppt['id'] : 'Schedule Appointment' ?></h2>
<form method="post">
  <?php if ($editAppt): ?>
    <input type="hidden" name="id" value="<?= $editAppt['id'] ?>">
  <?php endif ?>

  <div class="form-group">
    <label class="form-label">Doctor:</label>
    <select name="doctor_id">
      <?php while($r = $docs->fetch_assoc()): ?>
        <option value="<?= $r['id'] ?>"
          <?= $editAppt && $r['id']==$editAppt['doctor_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($r['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="form-group">
    <label class="form-label">Patient:</label>
    <select name="patient_id">
      <?php while($p = $pats->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>"
          <?= $editAppt && $p['id']==$editAppt['patient_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($p['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="form-group">
    <label class="form-label">Date:</label>
    <input type="date" name="appt_date"
      value="<?= $editAppt ? $editAppt['appt_date'] : '' ?>">
  </div>

  <div class="form-group">
    <label class="form-label">Time:</label>
    <input type="time" name="appt_time"
      value="<?= $editAppt ? $editAppt['appt_time'] : '' ?>">
  </div>

  <div class="form-group">
    <label class="form-label">Status:</label>
    <select name="status">
      <?php
        $statuses = ['Scheduled','Completed','Cancelled'];
        foreach($statuses as $st): ?>
        <option value="<?= $st ?>"
          <?= $editAppt && $st === $editAppt['status'] ? 'selected' : '' ?>>
          <?= $st ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit"><?= $editAppt ? 'Update' : 'Set' ?></button>
  <?php if ($editAppt): ?>
    <a href="appointments.php">Cancel</a>
  <?php endif ?>
</form>

<?php if ($msg): ?>
  <div class="message"><?= htmlspecialchars($msg) ?></div>
<?php endif ?>

<h2>All Appointments</h2>
<table>
<tr>
  <th>ID</th>
  <th>Doctor</th>
  <th>Patient</th>
  <th>Date</th>
  <th>Time</th>
  <th>Status</th>
  <th>Actions</th>
</tr>
<?php while($a = $apps->fetch_assoc()): ?>
<tr>
  <td><?= $a['id'] ?></td>
  <td><?= htmlspecialchars($a['doc']) ?></td>
  <td><?= htmlspecialchars($a['pat']) ?></td>
  <td><?= $a['appt_date'] ?></td>
  <td><?= $a['appt_time'] ?></td>
  <td class="status-<?= strtolower($a['status']) ?>"><?= $a['status'] ?></td>
  <td>
    <a href="?edit=<?= $a['id'] ?>" class="action-link">Edit</a>
  </td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
