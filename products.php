<?php
session_start();
require_once 'Model/database.php';

$categories = ['Action', 'Jeux de rôle', 'Jeux de sport', 'Jeux de tir', 'Stratégie'];
$filters = [];
$params = [];

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
  if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
  }

  $productId = $_POST['product_id'];

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]++;
  } else {
    $_SESSION['cart'][$productId] = 1;
  }

  header("Location: products.php");
  exit;
}


$isLoggedIn = isset($_SESSION['user']);
$role = $isLoggedIn ? $_SESSION['user']['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>E-GAMES - Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
      font-family: 'Orbitron', sans-serif;
    }

    .navbar {
      background-color: #121212;
      box-shadow: 0 2px 10px rgba(0, 242, 255, 0.6);
    }

    .navbar-brand {
      color: #00f2ff !important;
      font-weight: bold;
      font-size: 1.5rem;
      letter-spacing: 1.5px;
    }

    .nav-link {
      color: #bbb !important;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
      color: #00f2ff !important;
      text-shadow: 0 0 8px #00f2ff;
    }

    .product-card {
      background-color: #1c1c1c;
      color: #fff;
      border: 2px solid transparent;
      border-image: linear-gradient(45deg, #0ff, #00f, #f0f) 1;
      border-image-slice: 1;
      border-radius: 12px;
      padding: 1.2rem;
      box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .product-card:hover {
      transform: scale(1.03);
      box-shadow: 0 0 20px rgba(0, 255, 255, 0.6);
    }

    .btn-primary {
      background: linear-gradient(45deg, #00f2ff, #7a00ff);
      border: none;
      color: white;
      font-weight: bold;
      box-shadow: 0 0 10px #00f2ff;
    }

    .btn-primary:hover {
      box-shadow: 0 0 20px #00f2ff;
      transform: scale(1.05);
    }

    .search-bar input {
      background-color: #111;
      color: #fff;
      border: 2px solid #0ff;
      box-shadow: 0 0 10px #0ff;
      padding: 0.8rem;
    }

    .search-bar input::placeholder {
      color: #aaa;
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

<body style="opacity: 0; transition: opacity 1s ease-in;">

  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="index.php">E-GAMES</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>" href="products.php">Produits</a>
          </li>

          <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item">
              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'active' : '' ?>" href="cart.php">Cart</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'profil.php' ? 'active' : '' ?>" href="profil.php">Profil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : '' ?>" href="login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : '' ?>" href="register.php">Register</a>
            </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">

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

    <div class="search-bar mx-auto text-center mb-4">
      <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search for products...">
    </div>

    <div class="row" id="productList">
      <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4 product-card-wrapper">
          <div class="product-card text-center">
            <a href="product_details.php?id=<?= $product['id'] ?>" style="text-decoration: none; color: inherit;">
              <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid mb-2" style="height: 200px; object-fit: cover;" alt="<?= $product['name'] ?>">
              <h5><?= htmlspecialchars($product['name']) ?></h5>
              <p><?= htmlspecialchars($product['description']) ?></p>
              <strong>$<?= number_format($product['price'], 2) ?></strong>
            </a>
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

    window.addEventListener('load', () => {
      document.body.style.opacity = '1';
    });
  </script>
</body>

</html>