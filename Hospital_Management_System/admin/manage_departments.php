<?php
require_once '../config.php';
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$msg = '';

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delId = (int)$_GET['delete_id'];
    $stmt = $mysqli->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param('i', $delId);
    $stmt->execute();
    header('Location: manage_departments.php');
    exit;
}

// Handle Add / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $msg = 'Please enter a department name.';
    } else {
        if (!empty($_POST['edit_id'])) {
            // Update existing
            $editId = (int)$_POST['edit_id'];
            $stmt = $mysqli->prepare("UPDATE departments SET name = ? WHERE id = ?");
            $stmt->bind_param('si', $name, $editId);
            $stmt->execute();
            $msg = 'Department updated.';
        } else {
            // Insert new
            $stmt = $mysqli->prepare("INSERT INTO departments(name) VALUES (?)");
            $stmt->bind_param('s', $name);
            if ($stmt->execute()) {
                $msg = 'Department added.';
            } else {
                $msg = 'Error: ' . $stmt->error;
            }
        }
    }
    // after POST redirect to avoid resubmission
    header('Location: manage_departments.php');
    exit;
}

// If editing, fetch that record
$editDept = null;
if (isset($_GET['edit_id'])) {
    $eid = (int)$_GET['edit_id'];
    $res = $mysqli->query("SELECT * FROM departments WHERE id = $eid");
    $editDept = $res->fetch_assoc();
}

// Fetch all departments
$depts = $mysqli->query("SELECT * FROM departments ORDER BY id ASC");

$pageTitle = 'Manage Departments';
include __DIR__ . '/inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    
    h2 {
        color: #2c3e50;
        margin-top: 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3498db;
    }
    
    form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }
    
    input[type="text"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 16px;
    }
    
    button {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }
    
    button:hover {
        background-color: #2980b9;
    }
    
    a.cancel-btn {
        display: inline-block;
        margin-left: 10px;
        padding: 10px 20px;
        background-color: #e74c3c;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    
    a.cancel-btn:hover {
        background-color: #c0392b;
    }
    
    .message {
        padding: 10px 15px;
        background-color: #d4edda;
        color: #155724;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
    
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    
    tr:hover {
        background-color: #e9f7fe;
    }
    
    .action-links a {
        display: inline-block;
        padding: 5px 10px;
        margin: 0 5px;
        border-radius: 3px;
        text-decoration: none;
        color: white;
    }
    
    .edit-link {
        background-color: #2ecc71;
    }
    
    .edit-link:hover {
        background-color: #27ae60;
    }
    
    .delete-link {
        background-color: #e74c3c;
    }
    
    .delete-link:hover {
        background-color: #c0392b;
    }
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    

<div class="container">
    <h2><?= $editDept ? 'Edit Department' : 'Add New Department' ?></h2>
    <form method="post">
      <?php if ($editDept): ?>
        <input type="hidden" name="edit_id" value="<?= $editDept['id'] ?>">
      <?php endif ?>
      <label>
        Department Name:
        <input type="text" name="name" required
               value="<?= $editDept ? htmlspecialchars($editDept['name']) : '' ?>">
      </label>
      <button type="submit"><?= $editDept ? 'Update Department' : 'Add Department' ?></button>
      <?php if ($editDept): ?>
        <a href="manage_departments.php" class="cancel-btn">Cancel</a>
      <?php endif ?>
    </form>
    
    <?php if ($msg): ?>
      <div class="message"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    
    <h2>All Departments</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Department Name</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $depts->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td class="action-links">
            <a href="?edit_id=<?= $row['id'] ?>" class="edit-link">Edit</a>
            <a href="?delete_id=<?= $row['id'] ?>" class="delete-link"
               onclick="return confirm('Are you sure you want to delete department: <?= htmlspecialchars($row['name']) ?>?')">
              Delete
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
</div>
</body>
</html>
