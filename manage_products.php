<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'Model/database.php'; // adjust path if needed

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $imagePath = '';
    if ($_FILES['image']['name']) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $sql = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $sql->execute([$name, $desc, $price, $imagePath]);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    header("Location: manage_products.php");
    exit;
}

// Handle Edit
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $imagePath = $_POST['existing_image'];
    if ($_FILES['image']['name']) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $sql = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
    $sql->execute([$name, $desc, $price, $imagePath, $id]);
    header("Location: manage_products.php");
    exit;
}

// Fetch all products
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
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
    <input type="number" step="0.01" name="price" class="form-control my-2" placeholder="Price" required />
    <input type="file" name="image" class="form-control my-2" />
    <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
  </form>

  <!-- Product List -->
  <table class="table table-dark table-striped">
    <thead>
      <tr>
        <th>Name</th><th>Description</th><th>Price</th><th>Image</th><th>Actions</th>
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
          <td><input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" /></td>
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
</div>
</body>
</html>
