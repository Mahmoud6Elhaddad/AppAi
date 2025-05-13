<?php
require_once '../config.php';
if (! $_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$msg       = '';
$edit_mode = false;
$id        = null;
$name      = '';
$email     = '';
$age       = '';
$gender    = '';
$address   = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $delId = (int) $_GET['id'];
    $delSt = $mysqli->prepare("DELETE FROM patients WHERE id = ?");
    $delSt->bind_param('i', $delId);
    $delSt->execute();
    $msg = "Patient #{$delId} deleted";
    header('Location: manage_patients.php?msg=' . urlencode($msg));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $edit_mode = true;
    $id        = (int) $_GET['id'];
    $selSt     = $mysqli->prepare("SELECT * FROM patients WHERE id = ?");
    $selSt->bind_param('i', $id);
    $selSt->execute();
    $res       = $selSt->get_result();
    if ($row = $res->fetch_assoc()) {
        $name    = $row['name'];
        $email   = $row['email'];
        $age     = $row['age'];
        $gender  = $row['gender'];
        $address = $row['address'];
    } else {
        $msg = "Patient not found";
        $edit_mode = false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action'] ?? 'add';
    $name    = $_POST['name']    ?? '';
    $email   = $_POST['email']   ?? '';
    $age     = (int) ($_POST['age']    ?? 0);
    $gender  = $_POST['gender']  ?? '';
    $address = $_POST['address'] ?? '';

    $n  = $mysqli->real_escape_string($name);
    $e  = $mysqli->real_escape_string($email);
    $g  = $mysqli->real_escape_string($gender);
    $ad = $mysqli->real_escape_string($address);

    try {
        if ($action === 'update') {
            $id = (int) $_POST['id'];
            $upd = $mysqli->prepare(
                "UPDATE patients
                   SET name = ?, email = ?, age = ?, gender = ?, address = ?
                 WHERE id = ?"
            );
            $upd->bind_param('ssissi', $n, $e, $age, $g, $ad, $id);
            $upd->execute();
            $msg = "Patient #{$id} updated";

        } else {
            $ins = $mysqli->prepare(
                "INSERT INTO patients(name,email,age,gender,address)
                 VALUES(?,?,?,?,?)"
            );
            $ins->bind_param('ssiss', $n, $e, $age, $g, $ad);
            $ins->execute();
            $msg = 'Patient added';
        }
        $edit_mode = false;
        $id        = null;
        $name = $email = $age = $gender = $address = '';

    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() === 1062) {
            $msg = 'Error: That email is already registered.';
        } else {
            throw $ex;
        }
    }
}

$res = $mysqli->query("SELECT * FROM patients");
include __DIR__ . '/inc/header.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Patients</title>
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2980b9;
      --success-color: #2ecc71;
      --danger-color: #e74c3c;
      --light-color: #f8f9fa;
      --dark-color: #343a40;
      --border-color: #dee2e6;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    
    h2 {
      color: var(--dark-color);
      border-bottom: 2px solid var(--primary-color);
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
    
    .form-group {
      margin-bottom: 15px;
    }
    
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }
    
    input, select, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid var(--border-color);
      border-radius: 4px;
      font-size: 16px;
      margin-bottom: 15px;
    }
    
    textarea {
      min-height: 100px;
      resize: vertical;
    }
    
    button {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }
    
    button:hover {
      background-color: var(--secondary-color);
    }
    
    a.btn {
      display: inline-block;
      text-decoration: none;
      background-color: #6c757d;
      color: white;
      padding: 12px 20px;
      border-radius: 4px;
      margin-left: 10px;
    }
    
    .message {
      padding: 15px;
      border-radius: 4px;
      margin: 20px 0;
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .message.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
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
      background-color: var(--primary-color);
      color: white;
      text-align: left;
      padding: 12px 15px;
    }
    
    td {
      padding: 12px 15px;
      border-bottom: 1px solid var(--border-color);
    }
    
    tr:last-child td {
      border-bottom: none;
    }
    
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    
    .action-links a {
      text-decoration: none;
      margin-right: 10px;
      color: var(--primary-color);
      font-weight: 600;
    }
    
    .action-links a:hover {
      text-decoration: underline;
    }
    
    .action-links a.delete {
      color: var(--danger-color);
    }
    
    @media (max-width: 768px) {
      table {
        display: block;
        overflow-x: auto;
      }
      
      input, select, textarea {
        width: calc(100% - 22px);
      }
    }
  </style>
</head>
<body>
  <div class="container">
<h2><?= $edit_mode ? "Edit Patient #{$id}" : 'Add Patient' ?></h2>

<form method="post">
  <input type="hidden" name="action" value="<?= $edit_mode ? 'update' : 'add' ?>">
  <?php if ($edit_mode): ?>
    <input type="hidden" name="id" value="<?= $id ?>">
  <?php endif; ?>

  <div class="form-group">
    <label for="name">Name:</label>
    <input id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
  </div>
  
  <div class="form-group">
    <label for="email">Email:</label>
    <input id="email" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
  </div>
  
  <div class="form-group">
    <label for="age">Age:</label>
    <input id="age" type="number" name="age" value="<?= htmlspecialchars($age) ?>" required>
  </div>
  
  <div class="form-group">
    <label for="gender">Gender:</label>
    <select id="gender" name="gender" required>
      <option value="">Select Gender</option>
      <option <?= $gender==='M' ? 'selected' : '' ?> value="M">Male</option>
      <option <?= $gender==='F' ? 'selected' : '' ?> value="F">Female</option>
      <option <?= $gender==='O' ? 'selected' : '' ?> value="O">Other</option>
    </select>
  </div>
  
  <div class="form-group">
    <label for="address">Address:</label>
    <textarea id="address" name="address"><?= htmlspecialchars($address) ?></textarea>
  </div>

  <button type="submit"><?= $edit_mode ? 'Update Patient' : 'Add Patient' ?></button>
  <?php if ($edit_mode): ?>
    <a href="manage_patients.php" class="btn">Cancel</a>
  <?php endif; ?>
</form>

<?php if ($msg): ?>
  <div class="message <?= strpos($msg, 'Error') !== false ? 'error' : '' ?>">
    <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>

<h2>All Patients</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Age</th>
      <th>Gender</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= htmlspecialchars($r['name']) ?></td>
      <td><?= htmlspecialchars($r['email']) ?></td>
      <td><?= $r['age'] ?></td>
      <td><?= htmlspecialchars($r['gender']) ?></td>
      <td class="action-links">
        <a href="?action=edit&id=<?= $r['id'] ?>">Edit</a>
        <a href="?action=delete&id=<?= $r['id'] ?>" 
           onclick="return confirm('Delete patient #<?= $r['id'] ?>?')"
           class="delete">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
</body>
</html>
