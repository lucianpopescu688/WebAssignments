<?php
// API endpoint for products
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

// Initialize product class
$product = new Product();
$category = new Category();

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 4; // 4 products per page as required
    
    // Check if category filter is applied
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $categoryId = (int)$_GET['category'];
        $categoryObj = $category->getCategoryById($categoryId);
        $products = $product->getProductsByCategory($categoryId, $page, $perPage);
        $totalProducts = $product->getTotalProductsByCategory($categoryId);
    } else {
        // Get all products with pagination
        $products = $product->getProducts($page, $perPage);
        $totalProducts = $product->getTotalProducts();
    }
    
    $totalPages = ceil($totalProducts / $perPage);
    
    // Include the products HTML to be inserted via AJAX
    include '../includes/products_grid.php';
    exit;
}

// Handle search requests
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $keyword = $_GET['search'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 4;
    
    $products = $product->searchProducts($keyword, $page, $perPage);
    $totalProducts = $product->getTotalSearchResults($keyword);
    $totalPages = ceil($totalProducts / $perPage);
    
    // Include the products HTML to be inserted via AJAX
    include '../includes/products_grid.php';
    exit;
}

// If no valid request is made
header('HTTP/1.1 400 Bad Request');
echo json_encode(['error' => 'Invalid request']);
exit;
