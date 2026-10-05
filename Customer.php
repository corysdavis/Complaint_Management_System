<?php

class Customer
{
    private $conn;
    private $table = "customers";

    public $customerId;
    public $firstName;
    public $lastName;
    public $email;
    public $street;
    public $city;
    public $state;
    public $zip;
    public $phone;
    public $passwordHash;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fill($d)
    {
        $this->firstName = trim($d['first_name']);
        $this->lastName = trim($d['last_name']);
        $this->email = trim($d['email']);
        $this->street = trim($d['street_address']);
        $this->city = trim($d['city']);
        $this->state = strtoupper(trim($d['state']));
        $this->zip = trim($d['zip_code']);
        $this->phone = trim($d['phone']);
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                  (first_name, last_name, email, street_address, city, state, zip_code, phone, password_hash)
                  VALUES (:first_name, :last_name, :email, :street, :city, :state, :zip, :phone, :password_hash)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':first_name' => $this->firstName,
            ':last_name' => $this->lastName,
            ':email' => $this->email,
            ':street' => $this->street,
            ':city' => $this->city,
            ':state' => $this->state,
            ':zip' => $this->zip,
            ':phone' => $this->phone,
            ':password_hash' => $this->passwordHash
        ]);
    }

    public function getAll()
    {
        $query = "SELECT customer_id, first_name, last_name, email, city, state, phone
                  FROM " . $this->table . " ORDER BY last_name, first_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE customer_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
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
                      street_address = :street, city = :city, state = :state,
                      zip_code = :zip, phone = :phone
                  WHERE customer_id = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':first_name' => $this->firstName,
            ':last_name' => $this->lastName,
            ':email' => $this->email,
            ':street' => $this->street,
            ':city' => $this->city,
            ':state' => $this->state,
            ':zip' => $this->zip,
            ':phone' => $this->phone,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE customer_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
