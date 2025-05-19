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
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: #121212;
      color: #E0E0E0;
      font-family: 'Poppins', sans-serif;
      padding: 40px 0;
      min-height: 100vh;
    }
    h2 {
      text-align: center;
      margin-bottom: 40px;
      font-weight: 700;
      letter-spacing: 2px;
      color: #00BFFF;
      text-shadow: 0 0 8px #00BFFFaa;
    }
    .btn-back {
      background: #00BFFF;
      color: #121212;
      font-weight: 700;
      border-radius: 12px;
      padding: 10px 20px;
      box-shadow: 0 6px 18px #00BFFFcc;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      margin-bottom: 25px;
    }
    .btn-back:hover {
      background: #0095d6;
      box-shadow: 0 10px 30px #0095d6cc;
      color: #fff;
    }
    table {
      background: #1e1e1e;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 12px 30px rgba(0, 191, 255, 0.25);
      border-collapse: separate !important;
      border-spacing: 0 12px !important;
      color: #e0e0e0;
      width: 100%;
    }
    thead tr {
      background: #222;
      color: #00BFFF;
      font-weight: 600;
      letter-spacing: 0.1em;
      text-transform: uppercase;
    }
    tbody tr {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      border-radius: 15px;
      background: #292929;
      box-shadow: 0 6px 18px rgba(0, 191, 255, 0.1);
    }
    tbody tr:hover {
      background: #333;
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
    }
    tbody tr td {
      vertical-align: middle !important;
      padding: 15px 18px !important;
      font-weight: 500;
    }
    input.form-control {
      background: #1e1e1e;
      border: 1px solid #333;
      color: #E0E0E0;
      box-shadow: none;
      border-radius: 10px;
      transition: 0.3s ease;
      font-weight: 600;
      text-align: center;
    }
    input.form-control:focus {
      outline: none;
      border-color: #00BFFF;
      box-shadow: 0 0 10px #00BFFFaa;
      background: #222;
      color: #fff;
    }
    .btn-warning {
      background: #FF6F00;
      color: #121212;
      font-weight: 700;
      border-radius: 12px;
      box-shadow: 0 6px 18px #FF6F00cc;
      transition: all 0.3s ease;
    }
    .btn-warning:hover {
      background: #e65c00;
      box-shadow: 0 10px 30px #e65c00cc;
      color: #fff;
    }
    .btn-danger {
      background: #D32F2F;
      font-weight: 700;
      border-radius: 12px;
      box-shadow: 0 6px 18px #d32f2fcc;
      transition: all 0.3s ease;
    }
    .btn-danger:hover {
      background: #b71c1c;
      box-shadow: 0 10px 30px #b71c1ccc;
    }
    /* Responsive */
    @media (max-width: 768px) {
      table, tbody, tr, td, th {
        display: block;
      }
      thead {
        display: none;
      }
      tbody tr {
        margin-bottom: 25px;
        box-shadow: 0 12px 25px rgba(0, 191, 255, 0.25);
      }
      tbody tr td {
        text-align: right;
        padding-left: 55%;
        position: relative;
      }
      tbody tr td::before {
        content: attr(data-label);
        position: absolute;
        left: 18px;
        width: 50%;
        padding-left: 15px;
        font-weight: 700;
        text-transform: uppercase;
        color: #00BFFF;
        text-align: left;
      }
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <a href="dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    <h2>Manage Users</h2>

    <table>
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
              <td data-label="Full Name"><input type="text" name="fullName" class="form-control" value="<?= htmlspecialchars($user['fullName']) ?>"></td>
              <td data-label="Email"><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"></td>
              <td data-label="Role"><input type="text" class="form-control" value="<?= $user['role'] ?>" readonly></td>
              <td data-label="Actions">
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
