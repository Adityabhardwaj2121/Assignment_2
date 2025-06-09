<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
include "../db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $desc = trim($_POST['description']);
  $price = floatval($_POST['price']);
  $image = trim($_POST['image']);

  $stmt = $mysqli->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
  $stmt->bind_param("ssdsi", $name, $desc, $price, $image, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: dashboard.php");
  exit;
}

$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

if (!$product) {
  echo "Product not found.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Edit Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 600px;
      margin: 30px auto;
    }

    input,
    textarea {
      width: 100%;
      padding: 8px;
      margin: 8px 0;
      box-sizing: border-box;
    }

    button {
      padding: 10px 20px;
      background-color: #3f51b5;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background-color: #303f9f;
    }

    a {
      display: inline-block;
      margin-top: 15px;
    }

    .btn-back {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #555;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .btn-back:hover {
      background-color: #333;
    }
  </style>
</head>

<body>

  <h2>Edit Product</h2>
  <form method="post">
    <label>Name:</label>
    <input name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Description:</label>
    <textarea name="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>

    <label>Price:</label>
    <input name="price" type="number" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

    <label>Image URL:</label>
    <input name="image" value="<?= htmlspecialchars($product['image']) ?>" required>

    <button type="submit">Update</button>
  </form>

  <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>



</body>

</html>