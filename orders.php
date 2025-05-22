<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$sort = $_GET['sort'] ?? '';

$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

function getOrderedProducts($pdo, $orderId, $sort)
{
    $query = "SELECT p.name, p.description, p.price, p.image, oi.quantity 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
    
    if ($sort === 'asc') {
        $query .= " ORDER BY p.price ASC";
    } elseif ($sort === 'desc') {
        $query .= " ORDER BY p.price DESC";
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Commandes - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #0f0f0f;
      font-family: 'Orbitron', sans-serif;
      color: #fff;
    }

    h2 {
      text-shadow: 0 0 10px #00f2ff;
    }

    .order-item {
      background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
      border: 1px solid #2c2c2c;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 0 10px rgba(0, 242, 255, 0.1);
    }

    .order-header {
      font-weight: bold;
      color: #00f2ff;
      margin-bottom: 15px;
    }

    .card {
      background-color: #1e1e1e;
      border: 1px solid #333;
      border-radius: 10px;
      transition: 0.3s;
    }

    .card:hover {
      box-shadow: 0 0 10px #00f2ff;
      transform: scale(1.02);
    }

    .product-img {
      height: 160px;
      object-fit: cover;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }

    .form-select,
    .btn {
      font-family: 'Orbitron', sans-serif;
    }

    .form-select {
      background-color: #1e1e1e;
      color: white;
      border: 1px solid #333;
    }

    .form-select:focus {
      border-color: #00f2ff;
      box-shadow: 0 0 5px #00f2ff;
    }

    .btn-primary {
      background: linear-gradient(to right, #00f2ff, #7f00ff);
      border: none;
      color: white;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background: linear-gradient(to right, #7f00ff, #00f2ff);
    }

    .btn-outline-primary {
      border-color: #00f2ff;
      color: #00f2ff;
    }

    .btn-outline-primary:hover {
      background-color: #00f2ff;
      color: #000;
    }

    .alert-info {
      background-color: #1e1e1e;
      border: 1px solid #00f2ff;
      color: #00f2ff;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="mb-4 text-center">📦 Mes Commandes</h2>

  <form method="get" class="mb-4 text-center">
    <label for="sort" class="form-label me-2">Trier par prix :</label>
    <select name="sort" id="sort" class="form-select d-inline-block w-auto">
      <option value="">-- Aucun tri --</option>
      <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>⬆ Moins cher</option>
      <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>⬇ Plus cher</option>
    </select>
    <button type="submit" class="btn btn-primary ms-2">Trier</button>
  </form>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info text-center">Vous n'avez encore passé aucune commande.</div>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <?php $items = getOrderedProducts($pdo, $order['id'], $sort); ?>
      <?php if (!empty($items)): ?>
        <div class="order-item">
          <p class="order-header">
            Commande #<?= $order['id'] ?> | <?= ucfirst($order['payment_method']) ?> | <?= $order['created_at'] ?>
          </p>
          <div class="row">
            <?php foreach ($items as $item): ?>
              <div class="col-md-4 mb-3">
                <div class="card h-100">
                  <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top product-img" alt="<?= $item['name'] ?>">
                  <div class="card-body text-white">
                    <h6 class="card-title"><?= htmlspecialchars($item['name']) ?></h6>
                    <p class="card-text"><?= htmlspecialchars($item['description']) ?></p>
                    <p><strong>$<?= number_format($item['price'], 2) ?></strong> × <?= (int)$item['quantity'] ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="index.php" class="btn btn-outline-primary">⬅ Retour à la boutique</a>
  </div>
</div>

</body>
</html>

