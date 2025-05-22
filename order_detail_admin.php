<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "ID de commande manquant.";
    exit;
}

$orderId = intval($_GET['order_id']);

$stmt = $pdo->prepare("
    SELECT o.id, o.created_at, o.payment_method, u.fullName
    FROM orders o
    JOIN user u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    echo "Commande introuvable.";
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.name, p.price, oi.quantity
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détails commande #<?= htmlspecialchars($orderId) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      margin-top: 40px;
    }
    .table th {
      background-color: #343a40;
      color: white;
    }
    .back-btn {
      background-color: #6c757d;
      color: white;
    }
    .back-btn:hover {
      background-color: #5a6268;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">🧾 Détails de la commande #<?= htmlspecialchars($orderId) ?></h2>

  <div class="mb-4">
    <strong>Client :</strong> <?= htmlspecialchars($order['fullName']) ?><br>
    <strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?><br>
    <strong>Méthode de paiement :</strong> <?= htmlspecialchars($order['payment_method']) ?>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Nom du produit</th>
        <th>Prix unitaire</th>
        <th>Quantité</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php 
        $total = 0;
        foreach ($items as $item): 
            $lineTotal = $item['price'] * $item['quantity'];
            $total += $lineTotal;
      ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= number_format($item['price'], 2) ?> €</td>
          <td><?= $item['quantity'] ?></td>
          <td><?= number_format($lineTotal, 2) ?> €</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3" class="text-end">Total commande</th>
        <th><?= number_format($total, 2) ?> €</th>
      </tr>
    </tfoot>
  </table>

  <a href="manage_orders.php" class="btn back-btn mt-3">⬅ Retour à la liste</a>
</div>

</body>
</html>
