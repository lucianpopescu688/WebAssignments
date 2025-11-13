<?php
class Product {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all products with pagination
    public function getProducts($page = 1, $perPage = 4) {
        $offset = ($page - 1) * $perPage;
        
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          ORDER BY p.created_at DESC 
                          LIMIT :offset, :perPage');
        
        $this->db->bind(':offset', $offset);
        $this->db->bind(':perPage', $perPage);
        
        return $this->db->resultSet();
    }
    
    // Get products by category with pagination
    public function getProductsByCategory($categoryId, $page = 1, $perPage = 4) {
        $offset = ($page - 1) * $perPage;
        
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.category_id = :category_id 
                          ORDER BY p.created_at DESC 
                          LIMIT :offset, :perPage');
        
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':offset', $offset);
        $this->db->bind(':perPage', $perPage);
        
        return $this->db->resultSet();
    }
    
    // Get total product count
    public function getTotalProducts() {
        $this->db->query('SELECT COUNT(*) as total FROM products');
        $result = $this->db->single();
        return $result->total;
    }
    
    // Get total product count by category
    public function getTotalProductsByCategory($categoryId) {
        $this->db->query('SELECT COUNT(*) as total FROM products WHERE category_id = :category_id');
        $this->db->bind(':category_id', $categoryId);
        $result = $this->db->single();
        return $result->total;
    }
    
    // Get product by ID
    public function getProductById($id) {
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // Add product
    public function addProduct($data) {
        $this->db->query('INSERT INTO products (category_id, name, description, price, image_url, stock) 
                          VALUES (:category_id, :name, :description, :price, :image_url, :stock)');
        
        // Bind values
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':image_url', $data['image_url']);
        $this->db->bind(':stock', $data['stock']);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Update product
    public function updateProduct($data) {
        $this->db->query('UPDATE products 
                          SET category_id = :category_id, 
                              name = :name, 
                              description = :description, 
                              price = :price, 
                              image_url = :image_url, 
                              stock = :stock 
                          WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':image_url', $data['image_url']);
        $this->db->bind(':stock', $data['stock']);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Delete product
    public function deleteProduct($id) {
        $this->db->query('DELETE FROM products WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Search products
    public function searchProducts($keyword, $page = 1, $perPage = 4) {
        $offset = ($page - 1) * $perPage;
        $searchTerm = '%' . $keyword . '%';
        
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.name LIKE :keyword OR p.description LIKE :keyword 
                          ORDER BY p.created_at DESC 
                          LIMIT :offset, :perPage');
        
        $this->db->bind(':keyword', $searchTerm);
        $this->db->bind(':offset', $offset);
        $this->db->bind(':perPage', $perPage);
        
        return $this->db->resultSet();
    }
    
    // Get total search results count
    public function getTotalSearchResults($keyword) {
        $searchTerm = '%' . $keyword . '%';
        
        $this->db->query('SELECT COUNT(*) as total 
                          FROM products 
                          WHERE name LIKE :keyword OR description LIKE :keyword');
        
        $this->db->bind(':keyword', $searchTerm);
        $result = $this->db->single();
        return $result->total;
    }
}
