<?php
session_start();
require_once 'Model/database.php';

$categories = ['Action', 'Jeux de rôle', 'Jeux de sport', 'Jeux de tir', 'Stratégie'];
$filters = [];
$params = [];

// Construction dynamique de la requête
$sql = "SELECT * FROM products WHERE 1";

if (!empty($_GET['categorie'])) {
    $sql .= " AND categorie = ?";
    $params[] = $_GET['categorie'];
}

if (!empty($_GET['age'])) {
    $sql .= " AND age >= ?";
    $params[] = $_GET['age'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
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

    .filter-form {
      background-color: #fff;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 2rem;
      color: black;
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

  <!-- Filtres -->
  <form method="GET" class="filter-form">
    <div class="row align-items-end">
      <div class="col-md-5">
        <label for="categorie" class="form-label">Catégorie</label>
        <select name="categorie" id="categorie" class="form-control">
          <option value="">Toutes les catégories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat ?>" <?= isset($_GET['categorie']) && $_GET['categorie'] === $cat ? 'selected' : '' ?>>
              <?= $cat ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label for="age" class="form-label">Âge minimum</label>
        <input type="number" name="age" id="age" class="form-control" value="<?= isset($_GET['age']) ? htmlspecialchars($_GET['age']) : '' ?>" />
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
      </div>
    </div>
  </form>

  <!-- Barre de recherche -->
  <div class="search-bar mx-auto text-center mb-4">
    <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search for products...">
  </div>

  <!-- Liste des produits -->
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
    <?php if (empty($products)): ?>
      <div class="col-12 text-center mt-5">
        <p>Aucun produit trouvé avec ces filtres.</p>
      </div>
    <?php endif; ?>
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
