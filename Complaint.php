<?php

class Complaint {
    private $conn;
    private $table = "complaints";

    public $complaintId;
    public $customerId;
    public $productId;
    public $complaintTypeId;
    public $employeeId;
    public $description;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (customer_id, product_id, complaint_type_id, employee_id, description)
                  VALUES (:customer_id, :product_id, :complaint_type_id, :employee_id, :description)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":customer_id", $this->customerId);
        $stmt->bindParam(":product_id", $this->productId);
        $stmt->bindParam(":complaint_type_id", $this->complaintTypeId);
        $stmt->bindParam(":employee_id", $this->employeeId);
        $stmt->bindParam(":description", $this->description);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE complaint_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id) {
        $query = "UPDATE " . $this->table . "
                  SET status = :status, employee_id = :employee_id
                  WHERE complaint_id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":employee_id", $this->employeeId);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE complaint_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
