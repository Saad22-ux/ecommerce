<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'Model/database.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM user WHERE id = ?")->execute([$id]);
    header("Location: manage_users.php");
    exit;
}

if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $name = $_POST['fullName'];
    $email = $_POST['email'];

    $sql = $pdo->prepare("UPDATE user SET fullName=?, email=? WHERE id=?");
    $sql->execute([$name, $email, $id]);
    header("Location: manage_users.php");
    exit;
}

$users = $pdo->prepare("SELECT * FROM user WHERE role = 'client'");
$users->execute();
$users = $users->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-dark text-white">
<div class="container py-5">
  <h2 class="mb-4">Manage Users</h2>

  <table class="table table-dark table-striped">
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <form method="POST">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <td><input type="text" name="fullName" class="form-control" value="<?= htmlspecialchars($user['fullName']) ?>"></td>
            <td><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"></td>
            <td><input type="text" class="form-control" value="<?= $user['role'] ?>" readonly></td>
            <td>
              <button type="submit" name="update_user" class="btn btn-warning btn-sm mb-1">Update</button>
              <a href="?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </form>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
