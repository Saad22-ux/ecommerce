<?php
session_start();
require_once 'Model/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$pdo = $pdo ?? null; 

$stmt = $pdo->prepare("SELECT id, fullName, email FROM user WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($name) || empty($email)) {
        $errors[] = "Nom et email sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    if ($password !== '' && $password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
            $params = [$name, $email, $hashed, $userId];
        } else {
            $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $params = [$name, $email, $userId];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $success = "Profil mis à jour avec succès.";

       
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
    }
}

$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Profil - E-GAMES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-dark text-white">
    <div class="container py-5">
        <h2>Mon Profil</h2>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-5">
            <div class="mb-3">
                <label for="name" class="form-label">Nom complet</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['fullName']) ?>" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required />
            </div>
            <hr class="border-secondary" />
            <p>Pour changer le mot de passe, remplissez ces champs. Sinon laissez-les vides.</p>
            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" minlength="6" />
            </div>
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Confirmer mot de passe</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" minlength="6" />
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
        </form>

        <h3>Mes commandes</h3>
        <?php if (!$orders): ?>
            <p>Aucune commande trouvée.</p>
        <?php else: ?>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Date</th>
                        <th>Méthode paiement</th>
                        <th>Statut</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td><?= htmlspecialchars($order['status'] ?? 'En cours') ?></td>
                            <td><a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-info">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="index.php" class="btn btn-outline-secondary mt-4">⬅ Retour à l'accueil</a>
    </div>
</body>

</html>