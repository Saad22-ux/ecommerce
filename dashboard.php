<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

$totalUsers = $pdo->query("SELECT COUNT(*) FROM user WHERE role = 'client'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$salesData = $pdo->query("
    SELECT MONTH(created_at) AS month, COUNT(*) AS total
    FROM orders
    GROUP BY MONTH(created_at)
")->fetchAll(PDO::FETCH_ASSOC);

$monthlySales = array_fill(1, 12, 0);

foreach ($salesData as $row) {
    $monthlySales[(int)$row['month']] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f9;
      color: #333;
      margin: 0;
      transition: all 0.3s ease;
    }

    .sidebar {
      height: 100vh;
      width: 240px;
      position: fixed;
      background: linear-gradient(145deg, #232526, #414345);
      padding: 20px;
      color: white;
    }

    .sidebar h2 {
      font-weight: 700;
      margin-bottom: 30px;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #f8f9fa;
      padding: 10px 15px;
      margin-bottom: 10px;
      text-decoration: none;
      border-radius: 8px;
      transition: all 0.2s ease;
    }

    .sidebar a:hover {
      background-color: #ffc107;
      color: #000;
    }

    .main {
      margin-left: 260px;
      padding: 40px 30px;
    }

    .card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .theme-toggle-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      padding: 10px 20px;
      border: none;
      background: #ffc107;
      font-weight: 600;
      border-radius: 30px;
      cursor: pointer;
    }

    canvas {
      background: white;
      border-radius: 15px;
      padding: 20px;
    }

    .logout-btn {
      margin-top: 30px;
      width: 100%;
      background-color: #dc3545;
      border: none;
      font-weight: bold;
      cursor: pointer;
    }

    .logout-btn:hover {
      background-color: #c82333;
    }

    .card h5 {
      color: #777;
    }

    .card h2 {
      font-weight: 700;
    }

    /* Nouveau bouton consulter page home */
    .btn-home {
      display: inline-block;
      background: #007bff;
      color: white;
      padding: 10px 20px;
      border-radius: 15px;
      font-weight: 600;
      margin-bottom: 25px;
      text-decoration: none;
      box-shadow: 0 6px 18px #007bffaa;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-home:hover {
      background: #0056b3;
      box-shadow: 0 10px 30px #0056b3cc;
      color: white;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2><i class="fas fa-gamepad"></i> E-GAMES Admin</h2>
  <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
  <a href="manage_products.php"><i class="fas fa-boxes"></i> Produits</a>
  <a href="manage_users.php"><i class="fas fa-users"></i> Utilisateurs</a>
  <a href="manage_orders.php"><i class="fas fa-receipt"></i> Commandes</a>
  <a href="#"><i class="fas fa-chart-pie"></i> Rapports</a>
  <form action="logout.php" method="post">
    <button class="btn logout-btn mt-3">Se déconnecter</button>
  </form>
</div>

<div class="main">
  <button class="theme-toggle-btn" id="theme-toggle-btn">🎨 Mode Sombre</button>

  <a href="index.php" target="_blank" class="btn-home"><i class="fas fa-home"></i> Consulter la page Home</a>

  <h1 class="mb-4">Bienvenue, <?= htmlspecialchars($user['fullName']) ?> 👑</h1>

  <div class="row">
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <h5>Total Utilisateurs</h5>
        <h2><?= $totalUsers ?></h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <h5>Total Produits</h5>
        <h2><?= $totalProducts ?></h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <h5>Total Commandes</h5>
        <h2><?= $totalOrders ?></h2>
      </div>
    </div>
  </div>

  <div class="mt-5">
    <h4>📊 Aperçu des ventes</h4>
    <canvas id="salesChart" height="100"></canvas>
  </div>
</div>

<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
      datasets: [{
        label: 'Ventes',
        data: <?= json_encode(array_values($monthlySales)) ?>,
        backgroundColor: '#ffc107'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { labels: { color: '#333' } }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: '#333' }
        },
        x: {
          ticks: { color: '#333' }
        }
      }
    }
  });

  const toggleBtn = document.getElementById('theme-toggle-btn');
  const body = document.body;

  toggleBtn.addEventListener('click', () => {
    if (body.classList.contains('dark-mode')) {
      body.classList.remove('dark-mode');
      body.style.background = '#f4f6f9';
      toggleBtn.textContent = '🎨 Mode Sombre';
    } else {
      body.classList.add('dark-mode');
      body.style.background = '#1c1c1c';
      toggleBtn.textContent = '🌞 Mode Clair';
    }
  });
</script>

</body>
</html>
