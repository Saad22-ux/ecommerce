<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header('Location: login.php');
  exit;
}

require_once 'Model/database.php';

if (isset($_POST['add_product'])) {
  $name = $_POST['name'];
  $desc = $_POST['description'];
  $quantity = $_POST['quantity'];
  $price = $_POST['price'];
  $categorie = $_POST['categorie'];
  $age = $_POST['age'];

  $imagePath = '';
  if ($_FILES['image']['name']) {
    $targetDir = "uploads/";
    $imagePath = $targetDir . time() . '_' . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
  }

  $sql = $pdo->prepare("INSERT INTO products (name, description, price, image, categorie, age, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $sql->execute([$name, $desc, $price, $imagePath, $categorie, $age, $quantity]);
}

// Handle Delete
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
  header("Location: manage_products.php");
  exit;
}

if (isset($_POST['edit_product'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $desc = $_POST['description'];
  $quantity = $_POST['quantity'];
  $price = $_POST['price'];
  $categorie = $_POST['categorie'];
  $age = $_POST['age'];

  $imagePath = $_POST['existing_image'];
  if ($_FILES['image']['name']) {
    $targetDir = "uploads/";
    $imagePath = $targetDir . time() . '_' . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
  }

  $sql = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=?, categorie=?, age=?, quantity=? WHERE id=?");
  $sql->execute([$name, $desc, $price, $imagePath, $categorie, $age, $quantity, $id]);

  header("Location: manage_products.php");
  exit;
}

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$categories = ['Action', 'Jeux de rôle', 'Jeux de sport', 'Jeux de tir', 'Stratégie'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Manage Products - E-GAMES</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
  background: #121212;
  color: #E0E0E0;
  font-family: 'Poppins', sans-serif;
  padding: 40px 0;
  min-height: 100vh;
}

h2 {
  text-align: center;
  margin-bottom: 40px;
  font-weight: 700;
  letter-spacing: 2px;
  color: #00BFFF;
  text-shadow: 0 0 8px #00BFFFaa;
}

form h4 {
  color: #00BFFF;
  margin-bottom: 25px;
  border-bottom: 2px solid #00BFFF;
  padding-bottom: 8px;
  font-weight: 600;
  letter-spacing: 1.2px;
}

.container {
  max-width: 1100px;
  margin: auto;
}

input.form-control,
textarea.form-control,
select.form-control {
  background: #1e1e1e;
  border: 1px solid #333;
  color: #E0E0E0;
  box-shadow: none;
  border-radius: 10px;
  transition: 0.3s ease;
}

input.form-control:focus,
textarea.form-control:focus,
select.form-control:focus {
  outline: none;
  border-color: #00BFFF;
  box-shadow: 0 0 10px #00BFFFaa;
  background: #222;
  color: #fff;
}

input[type="file"].form-control {
  padding: 6px 10px;
  background: #1e1e1e;
}

button.btn-success {
  background: #FF6F00;
  border: none;
  font-weight: 700;
  letter-spacing: 1px;
  box-shadow: 0 4px 15px #FF6F00cc;
  transition: background 0.3s ease;
  border-radius: 30px;
  padding: 12px 35px;
  color: #121212;
}

button.btn-success:hover {
  background: #e65c00;
  box-shadow: 0 6px 25px #e65c00cc;
  color: #fff;
}

table {
  background: #1e1e1e;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 12px 30px rgba(0, 191, 255, 0.25);
  border-collapse: separate !important;
  border-spacing: 0 12px !important;
  color: #e0e0e0;
}

thead tr {
  background: #222;
  color: #00BFFF;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

tbody tr {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
  border-radius: 15px;
  background: #292929;
  box-shadow: 0 6px 18px rgba(0, 191, 255, 0.1);
}

tbody tr:hover {
  background: #333;
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
}

tbody tr td {
  vertical-align: middle !important;
  padding: 15px 18px !important;
  font-weight: 500;
}

tbody tr td img {
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(255, 111, 0, 0.4);
  transition: transform 0.3s ease;
}

tbody tr td img:hover {
  transform: scale(1.2);
  box-shadow: 0 8px 30px rgba(255, 111, 0, 0.7);
}

input[type="text"],
input[type="number"] {
  font-weight: 600;
  color: #00BFFF;
  background: transparent !important;
  border: 1px solid transparent;
  transition: border-color 0.3s ease;
  text-align: center;
}

input[type="text"]:focus,
input[type="number"]:focus {
  border-color: #FF6F00;
  background: #222 !important;
  color: #FF6F00;
}

select.form-control {
  font-weight: 600;
  color: #00BFFF;
  text-align-last: center;
  border: 1px solid transparent;
  transition: border-color 0.3s ease;
  background: transparent;
}

select.form-control:focus {
  border-color: #FF6F00;
  background: #222;
  color: #FF6F00;
}

.btn-warning {
  background: #FF6F00;
  color: #121212;
  font-weight: 700;
  border-radius: 12px;
  box-shadow: 0 6px 18px #FF6F00cc;
  transition: all 0.3s ease;
}

.btn-warning:hover {
  background: #e65c00;
  box-shadow: 0 10px 30px #e65c00cc;
  color: #fff;
}

.btn-danger {
  background: #D32F2F;
  font-weight: 700;
  border-radius: 12px;
  box-shadow: 0 6px 18px #d32f2fcc;
  transition: all 0.3s ease;
}

.btn-danger:hover {
  background: #b71c1c;
  box-shadow: 0 10px 30px #b71c1ccc;
}

.btn-outline-secondary {
  border-radius: 30px;
  border-color: #00BFFF;
  color: #00BFFF;
  font-weight: 600;
  padding: 10px 30px;
  transition: all 0.3s ease;
  margin-top: 30px;
  display: inline-block;
}

.btn-outline-secondary:hover {
  background: #00BFFF;
  color: #121212;
}

/* Responsive */
@media (max-width: 768px) {
  table, tbody, tr, td, th {
    display: block;
  }
  thead {
    display: none;
  }
  tbody tr {
    margin-bottom: 25px;
    box-shadow: 0 12px 25px rgba(0, 191, 255, 0.25);
  }
  tbody tr td {
    text-align: right;
    padding-left: 55%;
    position: relative;
  }
  tbody tr td::before {
    content: attr(data-label);
    position: absolute;
    left: 18px;
    width: 50%;
    padding-left: 15px;
    font-weight: 700;
    text-transform: uppercase;
    color: #00BFFF;
    text-align: left;
  }
}

  </style>
</head>

<body>
  <div class="container">
    <h2>Gestion des produits</h2>

    <form method="POST" enctype="multipart/form-data" class="mb-5">
      <h4>Ajouter un nouveau produit</h4>

      <input type="text" name="name" class="form-control my-2" placeholder="Nom du produit" required />
      <textarea name="description" class="form-control my-2" placeholder="Description"></textarea>
      <input type="number" name="quantity" class="form-control my-2" placeholder="Quantité en stock" required />
      <input type="number" step="0.01" name="price" class="form-control my-2" placeholder="Prix (€)" required />
      <input type="file" name="image" class="form-control my-2" />

      <select name="categorie" class="form-control my-2" required>
        <option value="">-- Choisir une catégorie --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat ?>"><?= $cat ?></option>
        <?php endforeach; ?>
      </select>

      <input type="number" name="age" class="form-control my-2" placeholder="Âge minimum conseillé" required />

      <button type="submit" name="add_product" class="btn btn-success">Ajouter le produit</button>
    </form>

    <!-- Products Table -->
    <table class="table table-dark table-striped text-center align-middle">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Description</th>
          <th>Quantité</th>
          <th>Prix (€)</th>
          <th>Catégorie</th>
          <th>Âge</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product): ?>
          <tr>
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $product['id'] ?>" />
              <input type="hidden" name="existing_image" value="<?= $product['image'] ?>" />

              <td data-label="Nom"><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" /></td>
              <td data-label="Description"><input type="text" name="description" class="form-control" value="<?= htmlspecialchars($product['description']) ?>" /></td>
              <td data-label="Quantité"><input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?>" /></td>
              <td data-label="Prix (€)"><input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" /></td>

              <td data-label="Catégorie">
                <select name="categorie" class="form-control">
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat ?>" <?= $product['categorie'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                  <?php endforeach; ?>
                </select>
              </td>

              <td data-label="Âge"><input type="number" name="age" class="form-control" value="<?= $product['age'] ?>" /></td>

              <td data-label="Image">
                <?php if ($product['image']): ?>
                  <img src="<?= $product['image'] ?>" width="50" alt="image produit" />
                <?php endif; ?>
                <input type="file" name="image" class="form-control mt-1" />
              </td>

              <td data-label="Actions">
                <button type="submit" name="edit_product" class="btn btn-warning btn-sm mb-1">Modifier</button>
                <a href="?delete=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-outline-secondary">⬅ Retour au tableau de bord</a>
  </div>
</body>

</html>
