<?php

class Product {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($categoryId = null, $search = '') {
        $query = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if ($categoryId) {
            $query .= " AND category_id = ?";
            $params[] = $categoryId;
        }

        if ($search) {
            $query .= " AND name LIKE ?";
            $params[] = "%$search%";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getCategories() {
        $stmt = $this->conn->query("SELECT * FROM categories");
        return $stmt->fetchAll();
    }

    public function getFeatured($limit = 3) {
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($categoryId, $name, $description, $price, $imageName, $stock) {
        $stmt = $this->conn->prepare("INSERT INTO products (category_id, name, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$categoryId, $name, $description, $price, $imageName, $stock]);
    }

    public function update($id, $categoryId, $name, $description, $price, $imageName, $stock) {
        $stmt = $this->conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, image = ?, stock = ? WHERE id = ?");
        return $stmt->execute([$categoryId, $name, $description, $price, $imageName, $stock, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function searchProducts($query, $limit = 5) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE name LIKE ? LIMIT ?");
        $stmt->bindValue(1, "%$query%");
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
