<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllProducts($page = 1, $perPage = 4) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT * FROM products LIMIT :offset, :perPage");
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductsByCategory($categoryId, $page = 1, $perPage = 4) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT * FROM products WHERE category_id = :categoryId LIMIT :offset, :perPage");
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductById($productId) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = :productId");
        $stmt->execute([':productId' => $productId]);
        return $stmt->fetch();
    }

    public function addProduct($data) {
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['description'], $data['price'], $data['category_id'], $data['stock']]);
    }

    public function updateProduct($productId, $data) {
        $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, stock = ? WHERE product_id = ?");
        return $stmt->execute([$data['name'], $data['description'], $data['price'], $data['category_id'], $data['stock'], $productId]);
    }

    public function deleteProduct($productId) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
        return $stmt->execute([$productId]);
    }

    public function getTotalProducts($categoryId = null) {
        if ($categoryId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM products");
        }
        return $stmt->fetchColumn();
    }
}
?>