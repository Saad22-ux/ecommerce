<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'];
    $userId = $_SESSION['user']['id'];

    // Insert order
    $pdo->prepare("INSERT INTO orders (user_id, payment_method, created_at) VALUES (?, ?, NOW())")
        ->execute([$userId, $paymentMethod]);

    $orderId = $pdo->lastInsertId();

    foreach ($cart as $productId => $qty) {
        $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)")
            ->execute([$orderId, $productId, $qty]);
    }

    unset($_SESSION['cart']);
    $_SESSION['success'] = "Order placed successfully!";
    header("Location: orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #e3f2fd;
      font-family: 'Segoe UI', sans-serif;
    }
    .checkout-card {
      background-color: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 500px;
      margin: 50px auto;
    }
    .form-check-input:checked {
      background-color: #007bff;
      border-color: #007bff;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="checkout-card">
    <h2 class="mb-4 text-center">🧾 Checkout</h2>
    <form method="post">
      <h5 class="mb-3">Choose Payment Method</h5>

      <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" required>
        <label class="form-check-label" for="cash">💵 Cash on Delivery</label>
      </div>

      <div class="form-check mb-4">
        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
        <label class="form-check-label" for="card">💳 Credit Card</label>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Place Order</button>
      </div>
    </form>

    <div class="text-center mt-4">
      <a href="cart.php" class="btn btn-outline-secondary">⬅ Back to Cart</a>
    </div>
  </div>
</div>

</body>
</html>
