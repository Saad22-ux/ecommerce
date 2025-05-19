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
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-dark text-white">
  <div class="container py-5">
    <h2 class="mb-4">Manage Products</h2>

    <!-- Add Product -->
    <form method="POST" enctype="multipart/form-data" class="mb-5">
      <h4>Add New Product</h4>
      <input type="text" name="name" class="form-control my-2" placeholder="Name" required />
      <textarea name="description" class="form-control my-2" placeholder="Description"></textarea>
      <input type="number" name="quantity" class="form-control my-2" placeholder="Quantité en stock" required />
      <input type="number" step="0.01" name="price" class="form-control my-2" placeholder="Price" required />
      <input type="file" name="image" class="form-control my-2" />

      <select name="categorie" class="form-control my-2" required>
        <option value="">-- Choisir une catégorie --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat ?>"><?= $cat ?></option>
        <?php endforeach; ?>
      </select>

      <input type="number" name="age" class="form-control my-2" placeholder="Âge minimum conseillé" required />

      <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
    </form>

    <table class="table table-dark table-striped">
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Quantité</th>
          <th>Price</th>
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
              <input type="hidden" name="id" value="<?= $product['id'] ?>">
              <input type="hidden" name="existing_image" value="<?= $product['image'] ?>">

              <td><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" /></td>
              <td><input type="text" name="description" class="form-control" value="<?= htmlspecialchars($product['description']) ?>" /></td>
              <td><input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?>" /></td>
              <td><input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" /></td>

              <td>
                <select name="categorie" class="form-control">
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat ?>" <?= $product['categorie'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                  <?php endforeach; ?>
                </select>
              </td>

              <td><input type="number" name="age" class="form-control" value="<?= $product['age'] ?>" /></td>

              <td>
                <?php if ($product['image']): ?>
                  <img src="<?= $product['image'] ?>" width="50" />
                <?php endif; ?>
                <input type="file" name="image" class="form-control mt-1" />
              </td>

              <td>
                <button type="submit" name="edit_product" class="btn btn-warning btn-sm mb-1">Update</button>
                <a href="?delete=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')">Delete</a>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-outline-secondary">⬅ Back to Dashboard</a>

  </div>
</body>

</html>