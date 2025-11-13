<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database config
require_once 'config/database.php';
// Include classes
require_once 'classes/Database.php';
require_once 'classes/Category.php';
require_once 'classes/Product.php';
require_once 'classes/Cart.php';

// Initialize cart to get item count
$cart = new Cart();
$cartItemCount = $cart->getCartItemCount();

// Initialize category
$category = new Category();
$categories = $category->getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- You can add more CSS frameworks here if needed -->
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="index.php">E-Commerce Store</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">HomeComponent</a></li>
                    <li class="dropdown">
                        <a href="products.php">ProductsComponent</a>
                        <div class="dropdown-content">
                            <?php foreach($categories as $cat): ?>
                                <a href="products.php?category=<?php echo $cat->id; ?>"><?php echo $cat->name; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li>
                        <a href="cart.php">Cart (<?php echo $cartItemCount; ?>)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
