<?php
session_start();
require_once 'Model/database.php';

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// if (isset($_POST['add_to_cart'])) {
//     $productId = $_POST['product_id'];
//     if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
//     if (!in_array($productId, $_SESSION['cart'])) {
//         $_SESSION['cart'][] = $productId;
//     }
// }

if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Si le produit est déjà dans le panier, incrémente la quantité
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
}


$isLoggedIn = isset($_SESSION['user']);
$role = $isLoggedIn ? $_SESSION['user']['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-GAMES - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #29A;
      color: white;
      font-family: 'Segoe UI', sans-serif;
    }

    .navbar {
      background-color: #222;
    }

    .product-card {
      background-color: #fff;
      color: #000;
      border-radius: 10px;
      padding: 1rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      transition: transform 0.2s;
    }

    .product-card:hover {
      transform: scale(1.02);
    }

    .search-bar {
      margin: 2rem 0;
      max-width: 500px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="#">E-GAMES</a>
    <div>
      <?php if ($isLoggedIn): ?>
        <?php if ($role === 'admin'): ?>
          <a href="dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
        <?php else: ?>
          <a href="cart.php" class="btn btn-outline-light me-2">Cart</a>
        <?php endif; ?>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline-light me-2">Login</a>
        <a href="register.php" class="btn btn-light">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-5">

  <div class="search-bar mx-auto text-center">
    <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search for products...">
  </div>

  <div class="row" id="productList">
    <?php foreach ($products as $product): ?>
      <div class="col-md-4 mb-4 product-card-wrapper">
        <div class="product-card text-center">
          <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid mb-2" style="height: 200px; object-fit: cover;" alt="<?= $product['name'] ?>">
          <h5><?= htmlspecialchars($product['name']) ?></h5>
          <p><?= htmlspecialchars($product['description']) ?></p>
          <strong>$<?= number_format($product['price'], 2) ?></strong>
          <form method="post" class="mt-2">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm mt-2">Add to Cart</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const searchInput = document.getElementById('searchInput');
  const productList = document.getElementById('productList');

  searchInput.addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    const cards = document.querySelectorAll('.product-card-wrapper');
    cards.forEach(card => {
      const name = card.querySelector('h5').innerText.toLowerCase();
      card.style.display = name.includes(term) ? 'block' : 'none';
    });
  });
</script>
</body>
</html>
