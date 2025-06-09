<?php
session_start();
include "db.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(["status" => "success"]);
        exit;
    }

    header("Location: index.php");
    exit;
}

$result = $mysqli->query("SELECT * FROM products LIMIT 8");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        
       body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

.header {
    background-color:rgb(106, 21, 218);
    color: white;
    padding: 15px;
    text-align: center;
    position: relative;
}

.products {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
    padding: 25px;
    max-width: 1200px;
    margin: 0 auto;
}

.product {
    background: white;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    cursor: pointer;
}

.product:hover {
    transform: translateY(-5px);
}

.product img {
    max-width: 100%;
    height: 160px;
    object-fit: contain;
    margin-bottom: 15px;
}

.product h3 {
    margin: 10px 0;
    color: #333;
    font-size: 18px;
}

.product .price {
    font-weight: bold;
    color: #e53935;
    font-size: 18px;
    margin: 10px 0;
}

.add-to-cart {
    background-color:rgb(5, 14, 63);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    font-size: 14px;
    width: 100%;
    transition: background 0.3s;
}

.add-to-cart:hover {
    background-color:rgb(11, 16, 44);
}

.cart-btn {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background-color: #ff5722;
    color: white;
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
}

.cart-btn:hover {
    background-color: #e64a19;
}

.logout {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    text-decoration: none;
    font-size: 14px;
}

.welcome-message {
    position: absolute;
    left: 100px;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 14px;
}

.modal {
    display: none;
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
}

.modal-content {
    background-color: #fefefe;
    margin: 3% auto;
    padding: 40px;
    border-radius: 10px;
    width: 90%;
    max-width: 1200px;
    position: relative;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
}

.close {
    position: absolute;
    right: 20px;
    top: 20px;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.product-details {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
    align-items: flex-start;
}

.product-image-large {
    flex: 1;
    max-height: 500px;
    object-fit: contain;
    max-width: 100%;
}

.product-info {
    flex: 1;
}

.product-title {
    font-size: 28px;
    margin-bottom: 20px;
    color: #333;
}

.product-price-large {
    font-size: 24px;
    font-weight: bold;
    color: #e53935;
    margin-bottom: 20px;
}

.product-description {
    color: #555;
    line-height: 1.7;
    margin-bottom: 25px;
    font-size: 16px;
}
.toast {
    display: none;
    position: fixed;
    bottom: 10px;
    left: 10px;
    background-color: black;
    color: white;
    padding: 8px;
    font-size: 14px;
    border-radius: 3px;
}


    </style>
</head>
<body>
    <div class="header">
        <a href="logout.php" class="logout">Logout</a>
        <div class="welcome-message">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</div>
        <h1>Online Store</h1>
        <a href="cart.php" class="cart-btn">View Cart</a>
    </div>

    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product" onclick="showProductDetails(<?= $product['id'] ?>)">
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <div class="price">₹<?= number_format($product['price'], 2) ?></div>
                <form method="POST" onclick="event.stopPropagation()">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

 
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="product-details" id="productDetailsContent">
            </div>
        </div>
    </div>
<div id="toast" class="toast"></div>

    <script>
        const products = <?= json_encode($products) ?>;

        function showProductDetails(id) {
            const product = products.find(p => p.id == id);
            if (!product) return;

            const modal = document.getElementById('productModal');
            const content = document.getElementById('productDetailsContent');

            content.innerHTML = `
                <img src="${escapeHtml(product.image)}" class="product-image-large" alt="${escapeHtml(product.name)}">
                <div class="product-info">
                    <h2 class="product-title">${escapeHtml(product.name)}</h2>
                    <div class="product-price-large">₹${parseFloat(product.price).toFixed(2)}</div>
                    <p class="product-description">${escapeHtml(product.description)}</p>
                    <button onclick="addToCart(${product.id})" class="add-to-cart">Add to Cart</button>
                </div>
            `;

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        document.querySelector('.close').addEventListener('click', () => {
            document.getElementById('productModal').style.display = 'none';
            document.body.style.overflow = '';
        });

        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('productModal')) {
                document.getElementById('productModal').style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
function addToCart(productId) {
    fetch("", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: "product_id=" + productId
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        showToast("Product added to cart");
    });
}

function showToast(message) {
    var toast = document.getElementById("toast");
    toast.innerText = message;
    toast.style.display = "block";

    setTimeout(function() {
        toast.style.display = "none";
    }, 2000);
}



    </script>
</body>
</html>
