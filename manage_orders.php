<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("
    SELECT o.id, o.created_at, o.payment_method, u.fullName
    FROM orders o
    JOIN user u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des commandes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      margin-top: 50px;
    }
    .table thead {
      background-color: #343a40;
      color: white;
    }
    .btn-detail {
      background-color: #0d6efd;
      color: white;
    }
    .btn-detail:hover {
      background-color: #0b5ed7;
    }
    .back-btn {
      background-color: #6c757d;
      color: white;
      margin-bottom: 20px;
    }
    .back-btn:hover {
      background-color: #5a6268;
    }
  </style>
</head>
<body>

<div class="container">
  <a href="dashboard.php" class="btn back-btn">⬅ Retour au Dashboard</a>

  <h2 class="mb-4">📦 Liste des commandes</h2>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Client</th>
        <th>Date</th>
        <th>Paiement</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= $order['id'] ?></td>
          <td><?= htmlspecialchars($order['fullName']) ?></td>
          <td><?= $order['created_at'] ?></td>
          <td><?= ucfirst($order['payment_method']) ?></td>
          <td>
            <a href="order_detail_admin.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-detail">Voir</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
