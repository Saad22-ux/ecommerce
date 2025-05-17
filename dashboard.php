<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #121212;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .light-theme {
      background-color: #fff;
      color: #121212;
    }

    .dark-theme {
      background-color: #121212;
      color: #fff;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      padding: 2rem 1rem;
      position: fixed;
      transition: background-color 0.3s ease;
    }

    .sidebar a {
      color: #ddd;
      display: block;
      margin: 1rem 0;
      text-decoration: none;
    }

    .sidebar-light {
      background-color: #f8f9fa;
    }

    .sidebar-dark {
      background-color: #1f1f1f;
    }

    .main {
      margin-left: 250px;
      padding: 2rem;
    }

    .card {
      background-color: #1e1e1e;
      border: none;
      color: white;
    }

    .card-light {
      background-color: #f8f9fa;
      color: #121212;
    }

    .logout-btn {
      background-color: #dc3545;
      border: none;
    }

    .theme-toggle-btn {
      background-color: #c4a04e;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      cursor: pointer;
    }
  </style>
</head>
<body class="dark-theme">

<div class="sidebar sidebar-dark">
  <h2>E-GAMES Admin</h2>
  <a href="#">Dashboard</a>
  <a href="manage_products.php">Manage Products</a>
  <a href="manage_users.php">Manage Users</a>
  <a href="#">Orders</a>
  <a href="#">Reports</a>
  <form action="logout.php" method="post">
    <button class="btn logout-btn w-100 mt-4">Logout</button>
  </form>
</div>

<div class="main">
  <button class="btn theme-toggle-btn" id="theme-toggle-btn">Switch to Light Mode</button>
  <h1>Welcome, <?= htmlspecialchars($user['fullName']) ?> 👑</h1>
  
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Total Users</h5>
        <h2>154</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Total Products</h5>
        <h2>87</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Total Orders</h5>
        <h2>230</h2>
      </div>
    </div>
  </div>

  <div class="mt-5">
    <h4>Sales Overview</h4>
    <canvas id="salesChart" height="100"></canvas>
  </div>
</div>

<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Sales',
        data: [120, 190, 300, 250, 210, 320],
        backgroundColor: '#c4a04e'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: '#fff' }
        },
        x: {
          ticks: { color: '#fff' }
        }
      },
      plugins: {
        legend: {
          labels: { color: '#fff' }
        }
      }
    }
  });

  // Toggle theme logic
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const body = document.body;
  const sidebar = document.querySelector('.sidebar');
  const cards = document.querySelectorAll('.card');

  themeToggleBtn.addEventListener('click', () => {
    if (body.classList.contains('dark-theme')) {
      body.classList.remove('dark-theme');
      body.classList.add('light-theme');
      sidebar.classList.remove('sidebar-dark');
      sidebar.classList.add('sidebar-light');
      cards.forEach(card => card.classList.remove('card-dark'));
      cards.forEach(card => card.classList.add('card-light'));
      themeToggleBtn.textContent = 'Switch to Dark Mode';
    } else {
      body.classList.remove('light-theme');
      body.classList.add('dark-theme');
      sidebar.classList.remove('sidebar-light');
      sidebar.classList.add('sidebar-dark');
      cards.forEach(card => card.classList.remove('card-light'));
      cards.forEach(card => card.classList.add('card-dark'));
      themeToggleBtn.textContent = 'Switch to Light Mode';
    }
  });
</script>

</body>
</html>
