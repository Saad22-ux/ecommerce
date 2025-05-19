<?php
require_once 'Model/database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($fullName) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } else {
        $check = $pdo->prepare("SELECT * FROM user WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $errors[] = "Email is already registered.";
        } else {
            $insert = $pdo->prepare("INSERT INTO user (fullName, email, password, role) VALUES (?, ?, ?, 'client')");
            $insert->execute([$fullName, $email, $password]);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      background: radial-gradient(circle at top, #1a1a1a, #000);
      color: #fff;
      font-family: 'Orbitron', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .register-box {
      background: #111;
      border: 2px solid #00f2ff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 20px #00f2ff33;
      width: 100%;
      max-width: 500px;
    }

    .form-control {
      background-color: #1e1e1e;
      border: 1px solid #00f2ff;
      color: #fff;
    }

    .form-control:focus {
      border-color: #0ff;
      box-shadow: 0 0 10px #00f2ff;
      background-color: #1e1e1e;
      color: white;
    }

    .btn-primary {
      background: linear-gradient(45deg, #00f2ff, #7a00ff);
      border: none;
      font-weight: bold;
      box-shadow: 0 0 10px #00f2ff;
    }

    .btn-primary:hover {
      transform: scale(1.03);
      box-shadow: 0 0 15px #00f2ff;
    }

    .btn-secondary-custom {
      display: block;
      margin-top: 1rem;
      text-align: center;
      background: transparent;
      border: 1px solid #00f2ff;
      color: #00f2ff;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s ease-in-out;
    }

    .btn-secondary-custom:hover {
      background-color: #00f2ff22;
      color: #fff;
      box-shadow: 0 0 10px #00f2ff;
    }

    .alert {
      font-size: 0.95rem;
    }

    h2 {
      color: #0ff;
      text-shadow: 0 0 10px #0ff;
    }
  </style>
</head>
<body>

<div class="register-box">
  <h2 class="text-center mb-4">Create Your E-GAMES Account</h2>

  <?php if ($success): ?>
    <div class="alert alert-success text-center">
      Account created! You can now <a href="login.php" class="text-decoration-underline">log in</a>.
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <div><?= htmlspecialchars($error) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="fullName" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Register</button>
  </form>

 
  <a href="index.php" class="btn-secondary-custom text-center">← Retour à l'accueil</a>
</div>

</body>
</html>
