<?php

class Stats {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserCount() {
        return $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function getProductCount() {
        return $this->conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function getCategoryCount() {
        return $this->conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    }
}
?>
