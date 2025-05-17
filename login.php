<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-GAMES Login</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <style>
    body {
      background-color: #29A; 
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
      width: 100%;
      max-width: 400px;
      background-color: transparent;
      padding: 2rem;
      border-radius: 10px;
      text-align: center;
    }

    .login-logo img {
      width: 150px;
      margin-bottom: 1rem;
    }

    .form-control {
      background-color: transparent;
      border: 1px solid #fff;
      color: #29A;
    }

    .form-control::placeholder {
      color: #ddd;
    }

    .btn-login {
      background-color: #000;
      color: #29A;
      font-weight: bold;
      margin-top: 1rem;
    }

    .forgot-link {
      display: block;
      margin-top: 0.75rem;
      font-size: 0.9rem;
      color: #e0e0e0;
      text-decoration: none;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-logo">
      <img src="/ecommerce/Images/logo-transparent.png" />
      <h4 class="mt-2" style="color: white;">E-GAMES</h4>
    </div>
    <?php 
      if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        require_once 'Model/database.php'; 

        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) {
            session_start();
            $_SESSION['user'] = $user;
            if ($user['role'] === 'admin') {
              echo "<div class='alert alert-success mt-3'>Login successful!</div>";
              header('Location: dashboard.php');
            } else {
              header('Location: index.php');
              echo "<div class='alert alert-success mt-3'>Login successful!</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-3'>Invalid credentials.</div>";
        }
    } else {
        echo "<div class='alert alert-warning mt-3'>Please fill all fields.</div>";
    }
}
    ?>
    <form method="post">
      <div class="mb-3">
        <input name="email"  type="email"  class="form-control"  placeholder="EMAIL" required/>
      </div>
      <div class="mb-3">
        <input name="password" type="password"  class="form-control"  placeholder="PASSWORD"  required/>
      </div>
      <input name="login" type="submit" value="Login" class="btn btn-login w-100">
    </form>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>