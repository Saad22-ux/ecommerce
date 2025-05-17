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
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
  <h2 class="mb-4 text-center">Create Your E-GAMES Account</h2>

  <?php if ($success): ?>
    <div class="alert alert-success">Account created! You can now <a href="login.php">log in</a>.</div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <div><?= htmlspecialchars($error) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="bg-secondary p-4 rounded">
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
</div>
</body>
</html>
