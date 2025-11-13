<?php
// API endpoint for categories
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/Category.php';

// Initialize category class
$category = new Category();

// Handle GET requests to fetch all categories
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $categories = $category->getCategories();
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($categories);
    exit;
}

// If no valid request is made
header('HTTP/1.1 400 Bad Request');
echo json_encode(['error' => 'Invalid request']);
exit;
