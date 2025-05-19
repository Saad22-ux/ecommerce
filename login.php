<?php 
session_start();
require_once 'Model/database.php'; 

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) {
            $_SESSION['user'] = $user;
            if ($user['role'] === 'admin') {
                header('Location: dashboard.php');
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        } else {
            $loginError = "Invalid credentials.";
        }
    } else {
        $loginError = "Please fill all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>E-GAMES Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      background: radial-gradient(circle at center, #0f0f0f, #000);
      color: #fff;
      font-family: 'Orbitron', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
      background-color: #111;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 20px #00f2ff55;
      border: 2px solid #00f2ff;
      text-align: center;
    }

    .login-logo img {
      width: 120px;
      margin-bottom: 1rem;
      border-radius: 10px;
      box-shadow: 0 0 15px #00f2ff44;
    }

    .form-control {
      background-color: #1c1c1c;
      border: 1px solid #00f2ff;
      color: #fff;
    }

    .form-control::placeholder {
      color: #aaa;
    }

    .form-control:focus {
      border-color: #0ff;
      box-shadow: 0 0 10px #00f2ff;
      background-color: #1c1c1c;
      color: white;
    }

    .btn-login {
      background: linear-gradient(45deg, #00f2ff, #7a00ff);
      border: none;
      font-weight: bold;
      color: #fff;
      margin-top: 1rem;
      box-shadow: 0 0 10px #00f2ff88;
    }

    .btn-login:hover {
      transform: scale(1.03);
      box-shadow: 0 0 15px #00f2ff;
    }

    .btn-home {
      display: block;
      margin-top: 1rem;
      background: transparent;
      border: 1px solid #00f2ff;
      color: #00f2ff;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s ease-in-out;
    }

    .btn-home:hover {
      background-color: #00f2ff22;
      color: #fff;
      box-shadow: 0 0 10px #00f2ff;
    }

    h4 {
      color: #0ff;
      text-shadow: 0 0 10px #0ff;
    }

    .alert {
      font-size: 0.9rem;
      margin-top: 1rem;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-logo">
      <h4 class="mt-2">E-GAMES</h4>
    </div>

    <?php if (!empty($loginError)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <input name="email" type="email" class="form-control" placeholder="EMAIL" required />
      </div>
      <div class="mb-3">
        <input name="password" type="password" class="form-control" placeholder="PASSWORD" required />
      </div>
      <input name="login" type="submit" value="Login" class="btn btn-login w-100" />
    </form>

    <a href="index.php" class="btn-home">← Retour à l'accueil</a>
  </div>
</body>
</html>
