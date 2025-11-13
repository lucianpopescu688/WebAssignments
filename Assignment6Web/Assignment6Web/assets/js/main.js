// Main JavaScript file for E-Commerce Store

document.addEventListener('DOMContentLoaded', function() {
    // Event delegation for add to cart buttons
    document.addEventListener('click', function(e) {
        // Add to cart functionality
        if (e.target && e.target.classList.contains('add-to-cart')) {
            e.preventDefault();
            const productId = e.target.getAttribute('data-id');
            const quantity = document.querySelector('#quantity-' + productId) ? 
                             document.querySelector('#quantity-' + productId).value : 1;
            
            addToCart(productId, quantity);
        }
        
        // Remove from cart functionality
        if (e.target && e.target.classList.contains('remove-from-cart')) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                const cartItemId = e.target.getAttribute('data-id');
                removeFromCart(cartItemId);
            }
        }
        
        // Update cart item quantity
        if (e.target && e.target.classList.contains('update-quantity')) {
            e.preventDefault();
            const cartItemId = e.target.getAttribute('data-id');
            const quantity = document.querySelector('#cart-quantity-' + cartItemId).value;
            
            updateCartQuantity(cartItemId, quantity);
        }
        
        // Clear cart
        if (e.target && e.target.id === 'clear-cart') {
            e.preventDefault();
            if (confirm('Are you sure you want to clear your cart?')) {
                clearCart();
            }
        }
    });
    
    // Category filter functionality
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const categoryId = this.value;
            if (categoryId) {
                loadProductsByCategory(categoryId, 1);
            } else {
                window.location.href = 'products.php';
            }
        });
    }
    
    // Pagination functionality
    const paginationLinks = document.querySelectorAll('.pagination a');
    if (paginationLinks.length > 0) {
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                const categoryId = this.getAttribute('data-category');
                
                if (categoryId) {
                    loadProductsByCategory(categoryId, page);
                } else {
                    loadProducts(page);
                }
            });
        });
    }
});

// Function to add product to cart
function addToCart(productId, quantity) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'api/cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                // Update cart count in header
                updateCartCount();
                
                // Show success message
                showMessage('Product added to cart successfully!', 'success');
            } else {
                showMessage('Failed to add product to cart.', 'danger');
            }
        }
    };
    
    xhr.send(`action=add&product_id=${productId}&quantity=${quantity}`);
}

// Function to remove item from cart
function removeFromCart(cartItemId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'api/cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                // Remove the row from the cart table
                const row = document.querySelector(`tr[data-id="${cartItemId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Update cart total
                updateCartTotal();
                
                // Update cart count in header
                updateCartCount();
                
                // Show success message
                showMessage('Item removed from cart!', 'success');
                
                // If cart is empty, show empty cart message
                if (document.querySelectorAll('.cart-table tbody tr').length === 0) {
                    document.querySelector('.cart-table').style.display = 'none';
                    document.querySelector('.cart-actions').style.display = 'none';
                    document.querySelector('.cart-container').innerHTML = '<div class="alert alert-info">Your cart is empty.</div>';
                }
            } else {
                showMessage('Failed to remove item from cart.', 'danger');
            }
        }
    };
    
    xhr.send(`action=remove&cart_item_id=${cartItemId}`);
}

// Function to update cart item quantity
function updateCartQuantity(cartItemId, quantity) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'api/cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                // Update subtotal for this item
                const price = parseFloat(document.querySelector(`tr[data-id="${cartItemId}"] .price`).getAttribute('data-price'));
                const subtotal = price * quantity;
                document.querySelector(`tr[data-id="${cartItemId}"] .subtotal`).textContent = '$' + subtotal.toFixed(2);
                
                // Update cart total
                updateCartTotal();
                
                // Show success message
                showMessage('Cart updated!', 'success');
            } else {
                showMessage('Failed to update cart.', 'danger');
            }
        }
    };
    
    xhr.send(`action=update&cart_item_id=${cartItemId}&quantity=${quantity}`);
}

// Function to clear cart
function clearCart() {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'api/cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                // Clear the cart table
                document.querySelector('.cart-table').style.display = 'none';
                document.querySelector('.cart-actions').style.display = 'none';
                document.querySelector('.cart-container').innerHTML = '<div class="alert alert-info">Your cart is empty.</div>';
                
                // Update cart count in header
                updateCartCount();
                
                // Show success message
                showMessage('Cart cleared!', 'success');
            } else {
                showMessage('Failed to clear cart.', 'danger');
            }
        }
    };
    
    xhr.send('action=clear');
}

// Function to update cart count in header
function updateCartCount() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'api/cart.php?action=count', true);
    
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            document.querySelector('nav ul li a[href="cart.php"]').textContent = `Cart (${response.count})`;
        }
    };
    
    xhr.send();
}

// Function to update cart total
function updateCartTotal() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'api/cart.php?action=total', true);
    
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            document.querySelector('.cart-total span').textContent = '$' + parseFloat(response.total).toFixed(2);
        }
    };
    
    xhr.send();
}

// Function to load products by category using AJAX
function loadProductsByCategory(categoryId, page = 1) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `api/products.php?category=${categoryId}&page=${page}`, true);
    
    xhr.onload = function() {
        if (this.status === 200) {
            const productsContainer = document.querySelector('.products-container');
            productsContainer.innerHTML = this.responseText;
            
            // Update URL without reloading the page
            history.pushState(null, '', `products.php?category=${categoryId}&page=${page}`);
            
            // Update the active category in the filter
            const categoryFilter = document.getElementById('category-filter');
            if (categoryFilter) {
                categoryFilter.value = categoryId;
            }
        }
    };
    
    xhr.send();
}

// Function to load products using AJAX
function loadProducts(page = 1) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `api/products.php?page=${page}`, true);
    
    xhr.onload = function() {
        if (this.status === 200) {
            const productsContainer = document.querySelector('.products-container');
            productsContainer.innerHTML = this.responseText;
            
            // Update URL without reloading the page
            history.pushState(null, '', `products.php?page=${page}`);
        }
    };
    
    xhr.send();
}

// Function to show messages
function showMessage(message, type) {
    const messageContainer = document.createElement('div');
    messageContainer.className = `alert alert-${type}`;
    messageContainer.textContent = message;
    
    // Check if there's already a message container
    const existingMessage = document.querySelector('.alert');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Add the message to the top of the main content
    const mainContent = document.querySelector('main');
    mainContent.insertBefore(messageContainer, mainContent.firstChild);
    
    // Remove the message after 3 seconds
    setTimeout(() => {
        messageContainer.remove();
    }, 3000);
}
