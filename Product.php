<?php

class Product {
    private $conn;
    private $table = "products";

    public $productId;
    public $productName;
    public $description;
    public $price;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (product_name, description, price)
                  VALUES (:product_name, :description, :price)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":product_name", $this->productName);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE product_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id) {
        $query = "UPDATE " . $this->table . "
                  SET product_name = :product_name, description = :description, price = :price
                  WHERE product_id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":product_name", $this->productName);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE product_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
