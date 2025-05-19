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

    // Créer commande
    $pdo->prepare("INSERT INTO orders (user_id, payment_method, created_at) VALUES (?, ?, NOW())")
        ->execute([$userId, $paymentMethod]);
    $orderId = $pdo->lastInsertId();

    // Ajouter les produits
    foreach ($cart as $productId => $qty) {
        $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)")
            ->execute([$orderId, $productId, $qty]);
    }

    // Si paiement par carte, enregistrer les infos carte
    if ($paymentMethod === 'card') {
        $cardNumber = $_POST['card_number'];
        $cardHolder = $_POST['card_holder'];
        $expiryDate = $_POST['expiry_date'];
        $cvv = $_POST['cvv'];

        $pdo->prepare("INSERT INTO card_payments (order_id, card_number, card_holder, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)")
            ->execute([$orderId, $cardNumber, $cardHolder, $expiryDate, $cvv]);
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
      max-width: 600px;
      margin: 50px auto;
    }
    .form-check-input:checked {
      background-color: #007bff;
      border-color: #007bff;
    }
    #card-info {
      display: none;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="checkout-card">
    <h2 class="mb-4 text-center">🧾 Checkout</h2>
    <form method="post" id="checkout-form">
      <h5 class="mb-3">Choose Payment Method</h5>

      <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" required>
        <label class="form-check-label" for="cash">💵 Cash on Delivery</label>
      </div>

      <div class="form-check mb-4">
        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
        <label class="form-check-label" for="card">💳 Credit Card</label>
      </div>

      <!-- Infos carte bancaire -->
      <div id="card-info">
        <div class="mb-3">
          <label class="form-label">Card Number</label>
          <input type="text" name="card_number" class="form-control" maxlength="20">
        </div>
        <div class="mb-3">
          <label class="form-label">Card Holder Name</label>
          <input type="text" name="card_holder" class="form-control">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Expiry Date (MM/YYYY)</label>
            <input type="text" name="expiry_date" class="form-control" placeholder="MM/YYYY">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">CVV</label>
            <input type="text" name="cvv" class="form-control" maxlength="4">
          </div>
        </div>
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

<script>
  const cashRadio = document.getElementById('cash');
  const cardRadio = document.getElementById('card');
  const cardInfo = document.getElementById('card-info');
  const cardFields = cardInfo.querySelectorAll('input');

  function toggleCardFields(required) {
    if (required) {
      cardInfo.style.display = 'block';
      cardFields.forEach(field => field.setAttribute('required', 'required'));
    } else {
      cardInfo.style.display = 'none';
      cardFields.forEach(field => field.removeAttribute('required'));
    }
  }

  // Initial setup
  toggleCardFields(false);

  cashRadio.addEventListener('change', () => toggleCardFields(false));
  cardRadio.addEventListener('change', () => toggleCardFields(true));
</script>

</body>
</html>
