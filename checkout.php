<?php 
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'];
    $userId = $_SESSION['user']['id'];

    // Vérifier les quantités en stock avant de créer la commande
    foreach ($cart as $productId => $qty) {
        $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $_SESSION['error'] = "Produit introuvable (ID: $productId)";
            header("Location: cart.php");
            exit;
        }

        if ($product['quantity'] < $qty) {
            $_SESSION['error'] = "Stock insuffisant pour le produit ID $productId. Quantité en stock : {$product['quantity']}, quantité demandée : $qty.";
            header("Location: cart.php");
            exit;
        }
    }

    // Créer commande
    $pdo->prepare("INSERT INTO orders (user_id, payment_method, created_at) VALUES (?, ?, NOW())")
        ->execute([$userId, $paymentMethod]);
    $orderId = $pdo->lastInsertId();

    // Ajouter les produits à la commande ET mettre à jour la quantité dans products
    foreach ($cart as $productId => $qty) {
        // Insérer order_items
        $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)")
            ->execute([$orderId, $productId, $qty]);

        // Mettre à jour la quantité en stock
        $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?")
            ->execute([$qty, $productId]);
    }

    // Si paiement par carte, enregistrer les infos carte
    if ($paymentMethod === 'card') {
        $cardNumber = $_POST['card_number'];
        $cardHolder = $_POST['card_holder'];
        $expiryDate = $_POST['expiry_date'];
        $cvv = $_POST['cvv'];

        $pdo->prepare("INSERT INTO card_payments (order_id, card_number, card_holder, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)")
            ->execute([$orderId, $cardNumber, $cardHolder, $expiryDate, $cvv]);
    }

    unset($_SESSION['cart']);
    $_SESSION['success'] = "Commande passée avec succès !";
    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Checkout - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #0f0f0f;
      color: #fff;
      font-family: 'Orbitron', sans-serif;
    }

    .checkout-card {
      background: linear-gradient(135deg, rgba(33,33,33,0.95), rgba(20,20,20,0.95));
      border: 1px solid #2c2c2c;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,255,255,0.1);
      max-width: 650px;
      margin: 60px auto;
      backdrop-filter: blur(10px);
    }

    .form-check-label,
    .form-label {
      color: #ddd;
    }

    .form-control {
      background-color: #1e1e1e;
      border: 1px solid #333;
      color: #fff;
    }

    .form-control:focus {
      border-color: #00f2ff;
      box-shadow: 0 0 5px #00f2ff;
    }

    .btn-primary {
      background: linear-gradient(to right, #00f2ff, #7f00ff);
      border: none;
      color: white;
      font-weight: bold;
      transition: 0.3s ease;
    }

    .btn-primary:hover {
      background: linear-gradient(to right, #7f00ff, #00f2ff);
      transform: scale(1.02);
    }

    .btn-outline-secondary {
      border-color: #00f2ff;
      color: #00f2ff;
    }

    .btn-outline-secondary:hover {
      background-color: #00f2ff;
      color: #000;
    }

    h2 {
      text-shadow: 0 0 10px #00f2ff;
    }

    #card-info {
      display: none;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="checkout-card">
    <h2 class="mb-4 text-center">🧾 Finaliser la commande</h2>

    <!-- Afficher message erreur si présent -->
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post" id="checkout-form">
      <h5 class="mb-3">Méthode de paiement</h5>

      <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" required>
        <label class="form-check-label" for="cash">💵 Paiement à la livraison</label>
      </div>

      <div class="form-check mb-4">
        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
        <label class="form-check-label" for="card">💳 Carte bancaire</label>
      </div>

      <div id="card-info">
        <div class="mb-3">
          <label class="form-label">Numéro de carte</label>
          <input type="text" name="card_number" class="form-control" maxlength="20">
        </div>
        <div class="mb-3">
          <label class="form-label">Titulaire de la carte</label>
          <input type="text" name="card_holder" class="form-control">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Date d'expiration (MM/YYYY)</label>
            <input type="text" name="expiry_date" class="form-control" placeholder="MM/YYYY">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">CVV</label>
            <input type="text" name="cvv" class="form-control" maxlength="4">
          </div>
        </div>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">✅ Confirmer la commande</button>
      </div>
    </form>

    <div class="text-center mt-4">
      <a href="cart.php" class="btn btn-outline-secondary">⬅ Retour au panier</a>
    </div>
  </div>
</div>

<script>
  const cashRadio = document.getElementById('cash');
  const cardRadio = document.getElementById('card');
  const cardInfo = document.getElementById('card-info');
  const cardFields = cardInfo.querySelectorAll('input');

  function toggleCardFields(required) {
    if (required) {
      cardInfo.style.display = 'block';
      cardFields.forEach(field => field.setAttribute('required', 'required'));
    } else {
      cardInfo.style.display = 'none';
      cardFields.forEach(field => field.removeAttribute('required'));
    }
  }

  toggleCardFields(false);
  cashRadio.addEventListener('change', () => toggleCardFields(false));
  cardRadio.addEventListener('change', () => toggleCardFields(true));
</script>

</body>
</html>
