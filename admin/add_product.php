<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include "../db.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $image = trim($_POST['image']); 

    // Basic validation
    if ($name === '' || $desc === '' || $price === '' || $image === '') {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || $price < 0) {
        $error = "Price must be a valid positive number.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $desc, $price, $image);
        $stmt->execute();
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Product</title>
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

<h2>Add Product</h2>

<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" action="">
  <label for="name">Product Name</label>
  <input type="text" id="name" name="name" placeholder="Product Name" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required>

  <label for="description">Description</label>
  <textarea id="description" name="description" placeholder="Description" rows="4" required><?= isset($desc) ? htmlspecialchars($desc) : '' ?></textarea>

  <label for="price">Price (₹)</label>
  <input type="number" step="0.01" id="price" name="price" placeholder="Price" value="<?= isset($price) ? htmlspecialchars($price) : '' ?>" required>

  <label for="image">Image URL</label>
  <input type="text" id="image" name="image" placeholder="Image URL" value="<?= isset($image) ? htmlspecialchars($image) : '' ?>" required>

  <button type="submit">Add</button>
</form>

<a href="dashboard.php" class="btn-back">← Back to Dashboard</a>

</body>
</html>
