<?php
include 'includes/header.php';
$productId = $_GET['id'] ?? 0;
$product = (new Product())->getProductById($productId);
if (!$product) { header("Location: index.php"); exit; }
?>
<div class="container">
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <p><?= htmlspecialchars($product['description']) ?></p>
    <p>Price: $<?= number_format($product['price'], 2) ?></p>
    <button onclick="addToCart(<?= $productId ?>)">Add to Cart</button>
</div>
<?php include 'includes/footer.php'; ?>