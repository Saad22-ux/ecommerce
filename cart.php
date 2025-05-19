<?php
session_start();
require_once 'Model/database.php'; // Assurez-vous que $pdo est bien instancié

$cart = $_SESSION['cart'] ?? [];

// Traitement suppression d'un produit
if (isset($_POST['remove']) && isset($_POST['product_id'])) {
    $pid = (int) $_POST['product_id'];
    if (isset($_SESSION['cart'][$pid])) {
        unset($_SESSION['cart'][$pid]);
    }
    header("Location: cart.php");
    exit;
}

// Traitement mise à jour quantité
if (isset($_POST['update_quantity']) && isset($_POST['product_id'], $_POST['quantity'])) {
    $pid = (int) $_POST['product_id'];
    $qty = max(1, intval($_POST['quantity']));
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] = $qty;
    }
    header("Location: cart.php");
    exit;
}

// Récupérer les produits du panier
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
  <title>Votre panier - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #29A;
      color: white;
      font-family: 'Segoe UI', sans-serif;
    }
    .cart-item {
      background-color: #fff;
      color: #000;
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1rem;
    }
    .cart-item img {
      height: 100px;
      object-fit: cover;
    }
    .btn-remove {
      background-color: red;
      color: white;
      border: none;
    }
    .btn-remove:hover {
      background-color: darkred;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2>Votre panier</h2>

  <?php if (empty($productsInCart)): ?>
    <div class="alert alert-info text-dark">Votre panier est vide.</div>
  <?php else: ?>
    <?php foreach ($productsInCart as $product): ?>
      <div class="cart-item d-flex justify-content-between align-items-center">
        <div>
          <h5><?= htmlspecialchars($product['name']) ?></h5>
          <p>Prix unitaire : $<?= number_format($product['price'], 2) ?></p>
          <p>Sous-total : $<?= number_format($product['subtotal'], 2) ?></p>
        </div>
        <form method="post" class="d-flex align-items-center">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <input type="number" name="quantity" value="<?= $product['quantity'] ?>" min="1" class="form-control me-2" style="width:80px;">
          <button type="submit" name="update_quantity" class="btn btn-success me-2">Mettre à jour</button>
          <button type="submit" name="remove" class="btn btn-remove">Supprimer</button>
        </form>
      </div>
    <?php endforeach; ?>

    <div class="text-end mt-4">
      <h4>Total : $<?= number_format($total, 2) ?></h4>
      <a href="checkout.php" class="btn btn-light mt-2">Passer à la caisse</a>
    </div>
  <?php endif; ?>

  <a href="index.php" class="btn btn-outline-light mt-4">⬅ Retour à la boutique</a>
</div>

</body>
</html>
