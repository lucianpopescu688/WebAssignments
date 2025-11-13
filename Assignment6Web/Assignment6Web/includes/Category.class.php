<?php
class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM categories");
        return $stmt->fetchAll();
    }

    public function getCategory($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch();
    }
}
?>