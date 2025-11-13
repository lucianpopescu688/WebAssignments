<?php
// CheckoutComponent page
require_once 'includes/header.php';

// Initialize cart
$cart = new Cart();
$cartItems = $cart->getCartItems();
$cartTotal = $cart->getCartTotal();

// Check if cart is empty
if(count($cartItems) === 0) {
    header('Location: cart.php');
    exit;
}

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form fields
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    
    // Basic validation
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($address)) {
        $errors['address'] = 'Address is required';
    }
    
    if (empty($city)) {
        $errors['city'] = 'City is required';
    }
    
    if (empty($zip)) {
        $errors['zip'] = 'ZIP code is required';
    }
    
    if (empty($payment_method)) {
        $errors['payment_method'] = 'Payment method is required';
    }
    
    // If no errors, process the order
    if (empty($errors)) {
        // Here you would typically:
        // 1. Create an order record in the database
        // 2. Transfer cart items to order_items table
        // 3. Clear the cart
        // 4. Send confirmation email
        // 5. Redirect to confirmation page
        
        // For demo purposes, just clear the cart and show success message
        $cart->clearCart();
        $success = true;
    }
}
?>

<div class="container">
    <h1 class="page-title">CheckoutComponent</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <h2>Thank you for your order!</h2>
            <p>Your order has been placed successfully. You will receive a confirmation email shortly.</p>
            <a href="products.php" class="btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="checkout-container">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cartItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->name); ?></td>
                                <td>$<?php echo number_format($item->price, 2); ?></td>
                                <td><?php echo $item->quantity; ?></td>
                                <td>$<?php echo number_format($item->subtotal, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td><strong>$<?php echo number_format($cartTotal, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="checkout-form">
                <h2>Shipping & Payment Information</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <p>Please correct the following errors:</p>
                        <ul>
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="checkout.php">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="zip">ZIP Code:</label>
                        <input type="text" id="zip" name="zip" value="<?php echo isset($_POST['zip']) ? htmlspecialchars($_POST['zip']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Method:</label>
                        <div class="payment-methods">
                            <label>
                                <input type="radio" name="payment_method" value="credit_card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'credit_card') ? 'checked' : ''; ?> required>
                                Credit Card
                            </label>
                            <label>
                                <input type="radio" name="payment_method" value="paypal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'paypal') ? 'checked' : ''; ?>>
                                PayPal
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="cart.php" class="btn">Back to Cart</a>
                        <button type="submit" class="btn">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
