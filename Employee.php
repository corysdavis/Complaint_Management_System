<?php

class Employee
{
    private $conn;
    private $table = "employees";

    public $employeeId;
    public $userId;
    public $firstName;
    public $lastName;
    public $email;
    public $phoneExt;
    public $role;
    public $passwordHash;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fill($d)
    {
        $ext = trim($d['phone_ext'] ?? '');

        $this->firstName = trim($d['first_name']);
        $this->lastName = trim($d['last_name']);
        $this->email = trim($d['email']);
        $this->phoneExt = ($ext === '') ? null : $ext;
        $this->role = $d['role'];
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, first_name, last_name, email, phone_ext, password_hash, role)
                  VALUES (:user_id, :first_name, :last_name, :email, :phone_ext, :password_hash, :role)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':user_id' => $this->userId,
            ':first_name' => $this->firstName,
            ':last_name' => $this->lastName,
            ':email' => $this->email,
            ':phone_ext' => $this->phoneExt,
            ':password_hash' => $this->passwordHash,
            ':role' => $this->role
        ]);
    }

    public function getAll()
    {
        $query = "SELECT employee_id, user_id, first_name, last_name, email, phone_ext, role
                  FROM " . $this->table . " ORDER BY role, last_name, first_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTechnicians()
    {
        $query = "SELECT employee_id, first_name, last_name
                  FROM " . $this->table . " WHERE role = 'Technician' ORDER BY last_name, first_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE employee_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . "
                  SET first_name = :first_name, last_name = :last_name, email = :email,
                      phone_ext = :phone_ext, role = :role
                  WHERE employee_id = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':first_name' => $this->firstName,
            ':last_name' => $this->lastName,
            ':email' => $this->email,
            ':phone_ext' => $this->phoneExt,
            ':role' => $this->role,
            ':id' => $id
        ]);
    }

    public function updatePassword($id, $hash)
    {
        $query = "UPDATE " . $this->table . " SET password_hash = :hash WHERE employee_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE employee_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
