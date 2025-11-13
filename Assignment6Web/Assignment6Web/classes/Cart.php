<?php
class Cart {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Get cart items for current session
    public function getCartItems() {
        $sessionId = session_id();
        
        $this->db->query('SELECT ci.*, p.name, p.price, p.image_url, (p.price * ci.quantity) as subtotal 
                          FROM cart_items ci 
                          JOIN products p ON ci.product_id = p.id 
                          WHERE ci.session_id = :session_id');
        
        $this->db->bind(':session_id', $sessionId);
        return $this->db->resultSet();
    }
    
    // Add item to cart
    public function addToCart($productId, $quantity = 1) {
        $sessionId = session_id();
        
        // Check if the product already exists in the cart
        $this->db->query('SELECT * FROM cart_items 
                          WHERE session_id = :session_id AND product_id = :product_id');
        
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':product_id', $productId);
        
        $item = $this->db->single();
        
        if($item) {
            // Update quantity if product already in cart
            $this->db->query('UPDATE cart_items 
                              SET quantity = quantity + :quantity 
                              WHERE session_id = :session_id AND product_id = :product_id');
            
            $this->db->bind(':session_id', $sessionId);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':quantity', $quantity);
        } else {
            // Add new item to cart
            $this->db->query('INSERT INTO cart_items (session_id, product_id, quantity) 
                              VALUES (:session_id, :product_id, :quantity)');
            
            $this->db->bind(':session_id', $sessionId);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':quantity', $quantity);
        }
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Update cart item quantity
    public function updateCartItem($cartItemId, $quantity) {
        $sessionId = session_id();
        
        $this->db->query('UPDATE cart_items 
                          SET quantity = :quantity 
                          WHERE id = :id AND session_id = :session_id');
        
        $this->db->bind(':id', $cartItemId);
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':quantity', $quantity);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Remove item from cart
    public function removeFromCart($cartItemId) {
        $sessionId = session_id();
        
        $this->db->query('DELETE FROM cart_items 
                          WHERE id = :id AND session_id = :session_id');
        
        $this->db->bind(':id', $cartItemId);
        $this->db->bind(':session_id', $sessionId);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Clear cart
    public function clearCart() {
        $sessionId = session_id();
        
        $this->db->query('DELETE FROM cart_items WHERE session_id = :session_id');
        $this->db->bind(':session_id', $sessionId);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Get cart total
    public function getCartTotal() {
        $sessionId = session_id();
        
        $this->db->query('SELECT SUM(p.price * ci.quantity) as total 
                          FROM cart_items ci 
                          JOIN products p ON ci.product_id = p.id 
                          WHERE ci.session_id = :session_id');
        
        $this->db->bind(':session_id', $sessionId);
        $result = $this->db->single();
        
        return $result->total ?? 0;
    }
    
    // Get cart item count
    public function getCartItemCount() {
        $sessionId = session_id();
        
        $this->db->query('SELECT COUNT(*) as count FROM cart_items WHERE session_id = :session_id');
        $this->db->bind(':session_id', $sessionId);
        $result = $this->db->single();
        
        return $result->count ?? 0;
    }
}
