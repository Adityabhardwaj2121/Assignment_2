<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    if (isset($_POST['update_quantity'])) {
        $qty = max(1, (int)$_POST['quantity']);
        $_SESSION['cart'][$product_id] = $qty;
    } elseif (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit;
}


$cart_products = [];
$total = 0.0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $result = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $total += $row['subtotal'];
        $cart_products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    body {
      background-color:rgb(137, 112, 245);
      padding: 30px;
    }

    .cart-wrapper {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    .cart-header h1 {
      font-size: 28px;
      color: #333;
    }

    .back-link {
      text-decoration: none;
      color:rgb(125, 10, 233);
      font-weight: 500;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    .product-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
    }

    .product-info {
      display: flex;
      align-items: center;
      gap: 20px;
      flex: 1;
    }

    .product-img {
      width: 80px;
      height: 80px;
      border-radius: 10px;
      object-fit: cover;
    }

    .product-details {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .product-name {
      font-size: 18px;
      font-weight: bold;
      color: #222;
    }

    .product-price {
      color: #555;
      font-size: 14px;
    }

    .product-actions {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 10px;
    }

    .quantity-form {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    input[type="number"] {
      width: 60px;
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .btn {
      background:rgb(9, 50, 187);
      color: white;
      border: none;
      padding: 7px 12px;
      border-radius: 6px;
      cursor: pointer;
    }

    .btn:hover {
      background:rgb(24, 4, 94);
    }

    .total-box {
      text-align: right;
      font-size: 20px;
      font-weight: bold;
      color: #333;
      margin-top: 20px;
    }

    .empty-msg {
      text-align: center;
      font-size: 1.2rem;
      padding: 50px;
      color: #666;
    }

    @media (max-width: 600px) {
      .product-card {
        flex-direction: column;
        align-items: flex-start;
      }

      .product-actions {
        align-items: flex-start;
        width: 100%;
      }

      .quantity-form {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>

<body>
  <div class="cart-wrapper">
    <div class="cart-header">
      <a class="back-link" href="index.php">&larr; Continue Shopping</a>
      <h1>Your Cart</h1>
    </div>

    <?php if (empty($cart_products)): ?>
      <p class="empty-msg">Your cart is currently empty.</p>
    <?php else: ?>
      <?php foreach ($cart_products as $product): ?>
        <div class="product-card">
          <div class="product-info">
            <img class="product-img" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="product-details">
              <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
              <div class="product-price">₹<?= number_format($product['price'], 2) ?></div>
              <div class="product-price">Subtotal: ₹<?= number_format($product['subtotal'], 2) ?></div>
            </div>
          </div>
          <div class="product-actions">
            <form class="quantity-form" method="post" action="cart.php">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <input type="number" name="quantity" value="<?= $product['quantity'] ?>" min="1" required>
              <button class="btn" type="submit" name="update_quantity">Update</button>
            </form>
            <form method="post" action="cart.php">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button class="btn" type="submit" name="remove">Remove</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="total-box">
        Total: ₹<?= number_format($total, 2) ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
