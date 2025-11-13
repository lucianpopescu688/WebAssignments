<?php
// Product details page
require_once 'includes/header.php';

// Check if ID parameter exists
if(!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id = (int)$_GET['id'];

// Initialize product class
$product = new Product();
$productDetails = $product->getProductById($id);

// Check if product exists
if(!$productDetails) {
    header('Location: products.php');
    exit;
}
?>

<div class="container">
    <div class="product-details">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($productDetails->image_url); ?>" alt="<?php echo htmlspecialchars($productDetails->name); ?>">
        </div>
        <div class="product-info">
            <h1><?php echo htmlspecialchars($productDetails->name); ?></h1>
            <p class="price">$<?php echo number_format($productDetails->price, 2); ?></p>
            <p class="category">Category: <?php echo htmlspecialchars($productDetails->category_name); ?></p>
            <p class="description"><?php echo htmlspecialchars($productDetails->description); ?></p>
            <p class="stock">In Stock: <?php echo $productDetails->stock; ?></p>
            
            <div class="quantity-input">
                <label for="quantity-<?php echo $productDetails->id; ?>">Quantity:</label>
                <button onclick="decrementQuantity('#quantity-<?php echo $productDetails->id; ?>')" type="button">-</button>
                <input type="number" id="quantity-<?php echo $productDetails->id; ?>" min="1" max="<?php echo $productDetails->stock; ?>" value="1">
                <button onclick="incrementQuantity('#quantity-<?php echo $productDetails->id; ?>')" type="button">+</button>
            </div>
            
            <button class="btn add-to-cart" data-id="<?php echo $productDetails->id; ?>">Add to Cart</button>
            <a href="products.php" class="btn">Back to ProductsComponent</a>
        </div>
    </div>
</div>

<script>
function decrementQuantity(inputId) {
    const input = document.querySelector(inputId);
    const value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

function incrementQuantity(inputId) {
    const input = document.querySelector(inputId);
    const value = parseInt(input.value);
    const max = parseInt(input.getAttribute('max'));
    if (value < max) {
        input.value = value + 1;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
