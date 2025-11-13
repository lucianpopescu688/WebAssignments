<?php
require_once '../includes/db.php';
require_once '../includes/Product.class.php';

header('Content-Type: application/json');

$categoryId = $_GET['category_id'] ?? 0;
$page = $_GET['page'] ?? 1;
$perPage = 4;

$product = new Product();
$products = $categoryId > 0 
    ? $product->getProductsByCategory($categoryId, $page, $perPage)
    : $product->getAllProducts($page, $perPage);

$totalProducts = $product->getTotalProducts($categoryId);
$totalPages = ceil($totalProducts / $perPage);

echo json_encode([
    'products' => $products,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);
?>