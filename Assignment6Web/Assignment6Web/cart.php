<?php
// Cart page
require_once 'includes/header.php';

// Initialize cart
$cart = new Cart();
$cartItems = $cart->getCartItems();
$cartTotal = $cart->getCartTotal();
?>

<div class="container">
    <h1 class="page-title">Your Shopping Cart</h1>
    
    <div class="cart-container">
        <?php if(count($cartItems) > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cartItems as $item): ?>
                        <tr data-id="<?php echo $item->id; ?>">
                            <td>
                                <img src="<?php echo htmlspecialchars($item->image_url); ?>" alt="<?php echo htmlspecialchars($item->name); ?>">
                            </td>
                            <td>
                                <a href="product_details.php?id=<?php echo $item->product_id; ?>">
                                    <?php echo htmlspecialchars($item->name); ?>
                                </a>
                            </td>
                            <td class="price" data-price="<?php echo $item->price; ?>">
                                $<?php echo number_format($item->price, 2); ?>
                            </td>
                            <td>
                                <div class="quantity-input">
                                    <button onclick="decrementQuantity('#cart-quantity-<?php echo $item->id; ?>')" type="button">-</button>
                                    <input type="number" id="cart-quantity-<?php echo $item->id; ?>" min="1" value="<?php echo $item->quantity; ?>">
                                    <button onclick="incrementQuantity('#cart-quantity-<?php echo $item->id; ?>')" type="button">+</button>
                                    <button class="update-quantity" data-id="<?php echo $item->id; ?>">Update</button>
                                </div>
                            </td>
                            <td class="subtotal">
                                $<?php echo number_format($item->subtotal, 2); ?>
                            </td>
                            <td>
                                <button class="btn remove-from-cart" data-id="<?php echo $item->id; ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-total">
                <p>Total: <span>$<?php echo number_format($cartTotal, 2); ?></span></p>
            </div>
            
            <div class="cart-actions">
                <button id="clear-cart" class="btn">Clear Cart</button>
                <a href="checkout.php" class="btn">Proceed to CheckoutComponent</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Your cart is empty.</div>
            <a href="products.php" class="btn">Continue Shopping</a>
        <?php endif; ?>
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
    input.value = value + 1;
}
</script>

<?php require_once 'includes/footer.php'; ?>
