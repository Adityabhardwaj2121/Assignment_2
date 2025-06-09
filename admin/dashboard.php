<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include "../db.php";

$res = $mysqli->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 0;
    }

    header {
      background-color:rgb(50, 26, 94);
      color: white;
      padding: 20px;
      text-align: center;
    }

    .container {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .top-links {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .top-links a {
      background-color:rgb(64, 10, 165);
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }

    .top-links a:hover {
      background-color:rgb(51, 13, 139);
    }

    .product-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .product-card img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 6px;
    }

    .product-info {
      flex-grow: 1;
    }

    .product-actions a {
      margin-right: 10px;
      color:rgb(56, 6, 150);
      text-decoration: none;
      font-weight: bold;
    }

    .product-actions a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<header>
  <h1>Admin Dashboard</h1>
</header>

<div class="container">
  <div class="top-links">
    <a href="add_product.php">Add Product</a>
    <a href="logout.php">Logout</a>
  </div>

  <?php while ($row = $res->fetch_assoc()): ?>
    <div class="product-card">
      <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" />
      <div class="product-info">
        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p>Price: ₹<?= htmlspecialchars($row['price']) ?></p>
      </div>
      <div class="product-actions">
        <a href="edit_product.php?id=<?= $row['id'] ?>">Edit</a>
        <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
