<?php
class Category {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all categories
    public function getCategories() {
        $this->db->query('SELECT * FROM categories ORDER BY name');
        return $this->db->resultSet();
    }
    
    // Get category by ID
    public function getCategoryById($id) {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // Add category
    public function addCategory($data) {
        $this->db->query('INSERT INTO categories (name, description) VALUES (:name, :description)');
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Update category
    public function updateCategory($data) {
        $this->db->query('UPDATE categories SET name = :name, description = :description WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Delete category
    public function deleteCategory($id) {
        $this->db->query('DELETE FROM categories WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
