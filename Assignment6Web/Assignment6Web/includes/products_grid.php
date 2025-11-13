<?php
// This file is included by the API to return the products grid via AJAX
// It expects $products, $page, $totalPages, and optionally $categoryId to be defined
?>

<div class="products-grid">
    <?php if(!empty($products)): ?>
        <?php foreach($products as $product): ?>
            <div class="product-card">
                <img src="assets/images/<?php echo htmlspecialchars($product->image_url); ?>" alt="<?php echo htmlspecialchars($product->name); ?>">
                <h3><?php echo htmlspecialchars($product->name); ?></h3>
                <div class="price">$<?php echo number_format($product->price, 2); ?></div>
                <div class="category"><?php echo htmlspecialchars($product->category_name); ?></div>
                <div class="buttons">
                    <a href="product-details.php?id=<?php echo $product->id; ?>" class="btn">View Details</a>
                    <button class="btn add-to-cart" data-id="<?php echo $product->id; ?>">Add to Cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-products">No products found.</div>
    <?php endif; ?>
</div>

<?php if($totalPages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="#" data-page="<?php echo $page - 1; ?>" <?php echo isset($categoryId) ? 'data-category="'.$categoryId.'"' : ''; ?>>Previous</a>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="#" data-page="<?php echo $i; ?>" <?php echo isset($categoryId) ? 'data-category="'.$categoryId.'"' : ''; ?> class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        
        <?php if($page < $totalPages): ?>
            <a href="#" data-page="<?php echo $page + 1; ?>" <?php echo isset($categoryId) ? 'data-category="'.$categoryId.'"' : ''; ?>>Next</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
