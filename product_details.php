<?php
require_once 'Model/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<h2>Produit introuvable.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?> - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0f0f0f;
      color: #eee;
      font-family: 'Segoe UI', sans-serif;
    }
    .product-container {
      max-width: 800px;
      margin: 50px auto;
      background-color: #1c1c1c;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 12px #00f2ff50;
    }
    .product-img {
      width: 100%;
      height: auto;
      max-height: 400px;
      object-fit: contain;
      border-radius: 8px;
    }
    .price {
      color: #00f2ff;
      font-size: 1.8rem;
      font-weight: bold;
    }
    .btn-custom {
      background-color: #00f2ff;
      color: #000;
      border: none;
    }
    .btn-custom:hover {
      background-color: #00e0e0;
    }
    a {
      color: #00f2ff;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container product-container">
  <a href="products.php" class="mb-4 d-inline-block">⬅ Retour aux produits</a>
  
  <div class="row">
    <div class="col-md-6">
      <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img" />
    </div>
    <div class="col-md-6">
      <h2><?= htmlspecialchars($product['name']) ?></h2>
      <p><?= htmlspecialchars($product['description']) ?></p>
      <p class="price">$<?= number_format($product['price'], 2) ?></p>

      <form method="post" action="cart.php">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <div class="mb-3">
          <label for="quantity" class="form-label">Quantité :</label>
          <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control" style="width: 100px;" />
        </div>
        <button type="submit" class="btn btn-custom">🛒 Ajouter au panier</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
