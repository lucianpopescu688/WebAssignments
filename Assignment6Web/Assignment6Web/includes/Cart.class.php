<?php
class Cart {
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addToCart($productId, $quantity = 1) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    public function removeFromCart($productId) {
        unset($_SESSION['cart'][$productId]);
    }

    public function getCartItems() {
        return $_SESSION['cart'];
    }

    public function clearCart() {
        $_SESSION['cart'] = [];
    }
}
?>