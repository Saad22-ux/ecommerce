<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$sort = $_GET['sort'] ?? '';

// Récupérer toutes les commandes de l'utilisateur
$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les produits commandés avec tri par prix
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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .order-item { background-color: white; border-radius: 10px; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1); }
    .order-header { font-weight: bold; color: #0d6efd; }
    .product-img { height: 80px; object-fit: cover; border-radius: 5px; }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="mb-4 text-center">📦 My Orders</h2>

  <!-- Tri par prix -->
  <form method="get" class="mb-4 text-center">
    <label for="sort" class="form-label me-2">Sort products by price:</label>
    <select name="sort" id="sort" class="form-select d-inline-block w-auto">
      <option value="">-- No Sort --</option>
      <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Low to High</option>
      <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>High to Low</option>
    </select>
    <button type="submit" class="btn btn-primary ms-2">Sort</button>
  </form>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info text-center">You haven't placed any orders yet.</div>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <?php $items = getOrderedProducts($pdo, $order['id'], $sort); ?>
      <?php if (!empty($items)): ?>
        <div class="order-item p-4 mb-4">
          <p class="order-header mb-2">
            Order #<?= $order['id'] ?> | <?= ucfirst($order['payment_method']) ?> | <?= $order['created_at'] ?>
          </p>
          <div class="row">
            <?php foreach ($items as $item): ?>
              <div class="col-md-4 mb-3">
                <div class="card h-100">
                  <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top product-img" alt="<?= $item['name'] ?>">
                  <div class="card-body">
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
    <a href="index.php" class="btn btn-outline-primary">⬅ Back to Shop</a>
  </div>
</div>

</body>
</html>
