<?php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$error    = '';
$success  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $n = $mysqli->real_escape_string($_POST['name']);
            $e = $mysqli->real_escape_string($_POST['email']);
            $p = hash('sha256', $_POST['password']);
            $d = (int)$_POST['department_id'];

            $stmt = $mysqli->prepare("
              INSERT INTO doctors(name, email, password, department_id)
              VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('sssi', $n, $e, $p, $d);
            if ($stmt->execute()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=added');
                exit;
            } else {
                $error = $stmt->error;
            }
            break;

        case 'update':
            $id = (int)$_POST['id'];
            $n  = $mysqli->real_escape_string($_POST['name']);
            $e  = $mysqli->real_escape_string($_POST['email']);
            $d  = (int)$_POST['department_id'];

            if (!empty($_POST['password'])) {
                $p = hash('sha256', $_POST['password']);
                $sql = "UPDATE doctors
                        SET name=?, email=?, password=?, department_id=?
                        WHERE id=?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('sssii', $n, $e, $p, $d, $id);
            } else {
                $sql = "UPDATE doctors
                        SET name=?, email=?, department_id=?
                        WHERE id=?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('ssii', $n, $e, $d, $id);
            }

            if ($stmt->execute()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=updated');
                exit;
            } else {
                $error = $stmt->error;
            }
            break;

        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $mysqli->prepare("DELETE FROM doctors WHERE id = ?");
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=deleted');
                exit;
            } else {
                $error = $stmt->error;
            }
            break;
    }
}

$editDoc = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $stmt = $mysqli->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editDoc = $stmt->get_result()->fetch_assoc();
}

$res   = $mysqli->query("
  SELECT d.*, dep.name AS dept
    FROM doctors d
    LEFT JOIN departments dep
      ON d.department_id = dep.id
");
$depts = $mysqli->query("SELECT * FROM departments");

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':   $success = 'Doctor added.'; break;
        case 'updated': $success = 'Doctor updated.'; break;
        case 'deleted': $success = 'Doctor deleted.'; break;
    }
}

include __DIR__ . '/inc/header.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Doctors</title>
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
    
    .alert {
      padding: 12px 15px;
      border-radius: 4px;
      margin-bottom: 20px;
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    form {
      background-color: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
    
    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 14px;
    }
    
    small {
      color: #6c757d;
      font-style: italic;
    }
    
    button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s;
    }
    
    button:hover {
      background-color: #2980b9;
    }
    
    a.btn {
      display: inline-block;
      background-color: #6c757d;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 4px;
      margin-left: 10px;
      transition: background-color 0.3s;
    }
    
    a.btn:hover {
      background-color: #5a6268;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    
    th {
      background-color: #3498db;
      color: white;
      text-align: left;
      padding: 12px 15px;
    }
    
    td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
    }
    
    tr:last-child td {
      border-bottom: none;
    }
    
    tr:hover {
      background-color: #f5f5f5;
    }
    
    .action-buttons {
      display: flex;
      gap: 10px;
    }
    
    .edit-btn {
      background-color: #2ecc71;
      color: white;
      text-decoration: none;
      padding: 6px 10px;
      border-radius: 4px;
      font-size: 13px;
    }
    
    .edit-btn:hover {
      background-color: #27ae60;
    }
    
    .delete-btn {
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
    }
    
    .delete-btn:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>
<div class="container">
<h2><?= $editDoc ? 'Edit' : 'Add' ?> Doctor</h2>
<form method="post">
  <input type="hidden" name="action" value="<?= $editDoc ? 'update' : 'add' ?>">
  <?php if ($editDoc): ?>
    <input type="hidden" name="id" value="<?= (int)$editDoc['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label for="name">Name:</label>
    <input
      id="name"
      name="name"
      required
      value="<?= $editDoc ? htmlspecialchars($editDoc['name']) : '' ?>">
  </div>

  <div class="form-group">
    <label for="email">Email:</label>
    <input
      id="email"
      type="email"
      name="email"
      required
      value="<?= $editDoc ? htmlspecialchars($editDoc['email']) : '' ?>">
  </div>

  <div class="form-group">
    <label for="password">Password:</label>
    <input
      id="password"
      type="password"
      name="password"
      <?= $editDoc ? '' : 'required' ?>>
    <?php if ($editDoc): ?>
      <small>Leave blank to keep current password</small>
    <?php endif; ?>
  </div>

  <div class="form-group">
    <label for="department">Department:</label>
    <select id="department" name="department_id">
      <?php while ($r = $depts->fetch_assoc()): ?>
        <option
          value="<?= $r['id'] ?>"
          <?= $editDoc && $editDoc['department_id']==$r['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($r['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <button type="submit"><?= $editDoc ? 'Update' : 'Add' ?> Doctor</button>
  <?php if ($editDoc): ?>
    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn">Cancel</a>
  <?php endif; ?>
</form>

<?php if ($success): ?>
  <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h2>All Doctors</h2>
<table>
<tr>
  <th>ID</th>
  <th>Name</th>
  <th>Email</th>
  <th>Department</th>
  <th>Actions</th>
</tr>
<?php while ($row = $res->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['dept']) ?></td>
    <td class="action-buttons">
      <a href="?edit=<?= $row['id'] ?>" class="edit-btn">Edit</a>

      <form method="post" style="display:inline; margin:0; padding:0; box-shadow:none; background:none;" 
            onsubmit="return confirm('Are you sure you want to delete this doctor?');">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <button type="submit" class="delete-btn">Delete</button>
      </form>
    </td>
  </tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
