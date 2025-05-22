<?php
session_start();
require_once 'Model/database.php';

function isUserLoggedIn() {
    return isset($_SESSION['user']); 
}


if (isset($_POST['product_id']) && isset($_POST['quantity']) && !isset($_POST['update_quantity']) && !isset($_POST['remove'])) {
    if (!isUserLoggedIn()) {
        header("Location: login.php");
        exit;
    }

    $pid = (int) $_POST['product_id'];
    $qty = max(1, intval($_POST['quantity']));

    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }

    header("Location: cart.php");
    exit;
}


$cart = $_SESSION['cart'] ?? [];

if (isset($_POST['remove']) && isset($_POST['product_id'])) {
    $pid = (int) $_POST['product_id'];
    if (isset($_SESSION['cart'][$pid])) {
        unset($_SESSION['cart'][$pid]);
    }
    header("Location: cart.php");
    exit;
}

if (isset($_POST['update_quantity']) && isset($_POST['product_id'], $_POST['quantity'])) {
    $pid = (int) $_POST['product_id'];
    $qty = max(1, intval($_POST['quantity']));
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] = $qty;
    }
    header("Location: cart.php");
    exit;
}

$productsInCart = [];
$total = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productsInCart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productsInCart as $key => $product) {
        $pid = $product['id'];
        $productsInCart[$key]['quantity'] = $cart[$pid];
        $productsInCart[$key]['subtotal'] = $product['price'] * $cart[$pid];
        $total += $productsInCart[$key]['subtotal'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Panier - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      background: radial-gradient(circle at center, #0f0f0f, #000);
      font-family: 'Orbitron', sans-serif;
      color: #fff;
      padding: 2rem;
    }

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      color: #00f2ff;
      text-shadow: 0 0 10px #00f2ff;
    }

    .cart-item {
      background-color: #111;
      border: 1px solid #00f2ff;
      padding: 1rem;
      border-radius: 12px;
      box-shadow: 0 0 10px #00f2ff44;
      margin-bottom: 1.5rem;
    }

    .cart-item h5 {
      color: #0ff;
    }

    .form-control {
      width: 80px;
      background-color: #222;
      color: white;
      border: 1px solid #00f2ff;
    }

    .btn-update {
      background: linear-gradient(to right, #00f2ff, #7a00ff);
      border: none;
      color: #fff;
      font-weight: bold;
    }

    .btn-remove {
      background-color: red;
      color: white;
      border: none;
    }

    .btn-remove:hover {
      background-color: darkred;
    }

    .btn-checkout {
      background-color: #00f2ff;
      color: #000;
      font-weight: bold;
      border: none;
    }

    .btn-checkout:hover {
      background-color: #0ff;
      box-shadow: 0 0 15px #0ff;
    }

    .btn-back {
      border: 1px solid #00f2ff;
      color: #00f2ff;
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: 0.3s;
      display: inline-block;
    }

    .btn-back:hover {
      background-color: #00f2ff22;
      color: white;
      box-shadow: 0 0 10px #00f2ff;
    }

    .text-end h4 {
      color: #0ff;
      text-shadow: 0 0 8px #0ff;
    }

    @media (max-width: 576px) {
      .d-flex {
        flex-direction: column;
      }

      .form-control {
        width: 100%;
        margin-bottom: 1rem;
      }

      .btn {
        width: 100%;
        margin-bottom: 0.5rem;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>🛒 Votre Panier</h2>

  <?php if (empty($productsInCart)): ?>
    <div class="alert alert-info text-dark">Votre panier est vide.</div>
  <?php else: ?>
    <?php foreach ($productsInCart as $product): ?>
      <div class="cart-item d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h5><?= htmlspecialchars($product['name']) ?></h5>
          <p>Prix unitaire : $<?= number_format($product['price'], 2) ?></p>
          <p>Sous-total : $<?= number_format($product['subtotal'], 2) ?></p>
        </div>
        <form method="post" class="d-flex align-items-center flex-wrap gap-2 mt-3 mt-md-0">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <input type="number" name="quantity" value="<?= $product['quantity'] ?>" min="1" class="form-control me-2">
          <button type="submit" name="update_quantity" class="btn btn-update">Mettre à jour</button>
          <button type="submit" name="remove" class="btn btn-remove">Supprimer</button>
        </form>
      </div>
    <?php endforeach; ?>

    <div class="text-end mt-4">
      <h4>Total : $<?= number_format($total, 2) ?></h4>
      <a href="checkout.php" class="btn btn-checkout mt-2">Passer à la caisse</a>
    </div>
  <?php endif; ?>

  <div class="mt-4 text-center">
    <a href="index.php" class="btn-back">⬅ Retour à la boutique</a>
  </div>
</div>

</body>
</html>
