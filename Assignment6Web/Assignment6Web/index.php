<?php include 'includes/header.php'; ?>
<div class="container">
    <h1>Our ProductsComponent</h1>
    <div class="category-filter">
        <select id="categorySelect" onchange="loadProducts()">
            <option value="0">All Categories</option>
            <?php
            $category = new Category();
            foreach ($category->getAllCategories() as $cat): ?>
            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="productsContainer"></div>
    <div class="pagination">
        <button onclick="changePage(-1)">Previous</button>
        <span id="currentPage">1</span>
        <button onclick="changePage(1)">Next</button>
    </div>
</div>
<script src="js/script.js"></script>
<?php include 'includes/footer.php'; ?>