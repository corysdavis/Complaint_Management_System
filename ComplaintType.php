<?php

class ComplaintType {
    private $conn;
    private $table = "complaint_types";

    public $complaintTypeId;
    public $typeName;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (type_name, description)
                  VALUES (:type_name, :description)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":type_name", $this->typeName);
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
        $query = "SELECT * FROM " . $this->table . " WHERE complaint_type_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id) {
        $query = "UPDATE " . $this->table . "
                  SET type_name = :type_name, description = :description
                  WHERE complaint_type_id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":type_name", $this->typeName);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE complaint_type_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
