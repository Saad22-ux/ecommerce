<?php
session_start();
require_once 'Model/database.php';

$query = "
    SELECT p.id, p.name, p.price, p.image, SUM(oi.quantity) AS total_sold
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 5
";
$stmt = $pdo->query($query);
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Accueil - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet" />
  <style>
    body {
      background-color: #0f0f0f;
      color: #eee;
      font-family: 'Orbitron', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
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

    .container {
      max-width: 960px;
      margin-top: 50px;
    }

    header {
      text-align: center;
      margin-bottom: 40px;
    }

    header h1 {
      font-size: 3rem;
      color: #00f2ff;
      text-shadow: 0 0 10px #00f2ff;
    }

    header p {
      font-size: 1.25rem;
      color: #bbb;
    }

    .top-products h2 {
      color: #00f2ff;
      margin-bottom: 25px;
      text-align: center;
      text-shadow: 0 0 5px #00f2ff;
    }

    .card {
      background-color: #1e1e1e;
      border-radius: 12px;
      border: 1px solid #333;
      transition: 0.3s;
      box-shadow: 0 0 10px rgba(0, 242, 255, 0.1);
    }

    .card:hover {
      box-shadow: 0 0 20px #00f2ff;
      transform: scale(1.05);
    }

    .card-img-top {
      border-radius: 12px 12px 0 0;
      height: 180px;
      object-fit: cover;
    }

    .card-body {
      color: #eee;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 700;
    }

    .price {
      font-size: 1.25rem;
      color: #00f2ff;
      font-weight: bold;
      margin-top: 10px;
    }

    footer {
      background-color: #111;
      color: #444;
      text-align: center;
      padding: 15px 0;
      font-size: 0.9rem;
      margin-top: 40px;
      border-top: 1px solid #222;
    }

    a {
      color: #00f2ff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>

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
          <?php if ($_SESSION['user']['role'] === 'admin' && basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
            <li class="nav-item">
              <a class="nav-link" href="dashboard.php">Dashboard Admin</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'active' : '' ?>" href="cart.php">Cart</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'profil.php' ? 'active' : '' ?>" href="profil.php">Profil</a>
            </li>
          <?php endif; ?>
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



  <div class="container">
    <header>
      <h1>Bienvenue chez E-GAMES 🎮</h1>
      <p>La meilleure boutique en ligne pour acheter vos jeux vidéo préférés, du classique aux dernières nouveautés.</p>
    </header>

    <section class="top-products">
      <h2>Nos Meilleures Ventes</h2>
      <div class="row g-4">
        <?php foreach ($topProducts as $product): ?>
          <div class="col-md-4">
            <div class="card h-100">
              <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="card-img-top" />
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="price">$<?= number_format($product['price'], 2) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <footer>
    &copy; <?= date('Y') ?> E-GAMES. Tous droits réservés. | <a href="contact.php">Contactez-nous</a>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>