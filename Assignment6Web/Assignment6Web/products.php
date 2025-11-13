<?php
// ProductsComponent listing page
require_once 'includes/header.php';

// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 4; // 4 products per page as required

// Initialize product class
$product = new Product();

// Check if category filter is applied
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $categoryId = (int)$_GET['category'];
    $categoryObj = $category->getCategoryById($categoryId);
    $products = $product->getProductsByCategory($categoryId, $page, $perPage);
    $totalProducts = $product->getTotalProductsByCategory($categoryId);
    $pageTitle = "ProductsComponent in " . htmlspecialchars($categoryObj->name);
} else if (isset($_GET['search']) && !empty($_GET['search'])) {
    // Handle search
    $keyword = $_GET['search'];
    $products = $product->searchProducts($keyword, $page, $perPage);
    $totalProducts = $product->getTotalSearchResults($keyword);
    $pageTitle = "Search Results for: " . htmlspecialchars($keyword);
} else {
    // Get all products with pagination
    $products = $product->getProducts($page, $perPage);
    $totalProducts = $product->getTotalProducts();
    $pageTitle = "All ProductsComponent";
}

$totalPages = ceil($totalProducts / $perPage);
?>

<div class="container">
    <h1 class="page-title"><?php echo $pageTitle; ?></h1>
    
    <div class="row">
        <!-- Category filter -->
        <div class="category-filter">
            <form method="GET" action="products.php">
                <select name="category" id="category-filter">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat->id; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        
        <!-- Search form -->
        <div class="search-form">
            <form method="GET" action="products.php">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
    
    <!-- ProductsComponent container - will be updated via AJAX -->
    <div class="products-container">
        <?php include 'includes/products_grid.php'; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
