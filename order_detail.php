<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$orderId = $_GET['id'] ?? null;

if (!$orderId || !is_numeric($orderId)) {
    header('Location: profile.php');
    exit;
}

// Vérifier que la commande appartient bien à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    // commande non trouvée ou pas accessible
    header('Location: profile.php');
    exit;
}

// Récupérer les produits de la commande avec détails (nom, prix, quantité)
$stmt = $pdo->prepare("
    SELECT p.name, p.price, oi.quantity
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Détails commande #<?= htmlspecialchars($orderId) ?> - E-GAMES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-dark text-white">
<div class="container py-5">
    <h2>Détails de la commande #<?= htmlspecialchars($orderId) ?></h2>

    <p><strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
    <p><strong>Méthode de paiement :</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
    <p><strong>Statut :</strong> <?= htmlspecialchars($order['status'] ?? 'En cours') ?></p>

    <?php if (empty($orderItems)): ?>
        <p>Aucun produit trouvé pour cette commande.</p>
    <?php else: ?>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($orderItems as $item):
                    $lineTotal = $item['price'] * $item['quantity'];
                    $total += $lineTotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 2, ',', ' ') ?> €</td>
                    <td><?= number_format($lineTotal, 2, ',', ' ') ?> €</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total général</strong></td>
                    <td><strong><?= number_format($total, 2, ',', ' ') ?> €</strong></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="profil.php" class="btn btn-outline-secondary mt-4">⬅ Retour au profil</a>
</div>
</body>
</html>
