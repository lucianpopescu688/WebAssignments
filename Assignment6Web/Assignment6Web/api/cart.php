<?php
// API endpoint for cart operations
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/Cart.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart class
$cart = new Cart();

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get cart count
    if (isset($_GET['action']) && $_GET['action'] === 'count') {
        $count = $cart->getCartItemCount();
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }
    
    // Get cart total
    if (isset($_GET['action']) && $_GET['action'] === 'total') {
        $total = $cart->getCartTotal();
        header('Content-Type: application/json');
        echo json_encode(['total' => $total]);
        exit;
    }
    
    // Get cart items
    $cartItems = $cart->getCartItems();
    header('Content-Type: application/json');
    echo json_encode($cartItems);
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Add item to cart
    if ($action === 'add' && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        $success = $cart->addToCart($productId, $quantity);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
    
    // Remove item from cart
    if ($action === 'remove' && isset($_POST['cart_item_id'])) {
        $cartItemId = (int)$_POST['cart_item_id'];
        $success = $cart->removeFromCart($cartItemId);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
    
    // Update cart item quantity
    if ($action === 'update' && isset($_POST['cart_item_id']) && isset($_POST['quantity'])) {
        $cartItemId = (int)$_POST['cart_item_id'];
        $quantity = (int)$_POST['quantity'];
        $success = $cart->updateCartItem($cartItemId, $quantity);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
    
    // Clear cart
    if ($action === 'clear') {
        $success = $cart->clearCart();
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
}

// If no valid request is made
header('HTTP/1.1 400 Bad Request');
echo json_encode(['error' => 'Invalid request']);
exit;
