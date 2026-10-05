<?php

class Complaint
{
    private $conn;
    private $table = "complaints";

    private $listSelect = "SELECT c.*, cu.first_name AS customer_first, cu.last_name AS customer_last,
                                  p.product_name, t.type_name,
                                  e.first_name AS tech_first, e.last_name AS tech_last
                           FROM complaints c
                           JOIN customers cu ON c.customer_id = cu.customer_id
                           JOIN products p ON c.product_id = p.product_id
                           JOIN complaint_types t ON c.complaint_type_id = t.complaint_type_id
                           LEFT JOIN employees e ON c.employee_id = e.employee_id ";

    public $complaintId;
    public $customerId;
    public $productId;
    public $complaintTypeId;
    public $employeeId;
    public $description;
    public $imagePath;
    public $status;
    public $technicianNotes;
    public $resolutionDate;
    public $resolutionNotes;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function run($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
                  (customer_id, product_id, complaint_type_id, description, image_path)
                  VALUES (:customer_id, :product_id, :complaint_type_id, :description, :image_path)";

        return $this->run($query, [
            ':customer_id' => $this->customerId,
            ':product_id' => $this->productId,
            ':complaint_type_id' => $this->complaintTypeId,
            ':description' => $this->description,
            ':image_path' => $this->imagePath
        ]);
    }

    public function getAll()
    {
        return $this->run($this->listSelect . "ORDER BY c.date_submitted DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        return $this->run($this->listSelect . "WHERE c.complaint_id = :id", [':id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCustomer($customerId)
    {
        return $this->run(
            $this->listSelect . "WHERE c.customer_id = :id ORDER BY c.date_submitted DESC",
            [':id' => $customerId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByTechnician($employeeId)
    {
        return $this->run(
            $this->listSelect . "WHERE c.employee_id = :id ORDER BY c.status, c.date_submitted DESC",
            [':id' => $employeeId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOpenAssigned()
    {
        return $this->run($this->listSelect . "WHERE c.status = 'Open' AND c.employee_id IS NOT NULL
                                               ORDER BY c.date_submitted")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOpenUnassigned()
    {
        return $this->run($this->listSelect . "WHERE c.status = 'Open' AND c.employee_id IS NULL
                                               ORDER BY c.date_submitted")->fetchAll(PDO::FETCH_ASSOC);
    }

    // each tech has a count of their open complaints
    public function technicianCounts()
    {
        $query = "SELECT e.employee_id, e.first_name, e.last_name, COUNT(c.complaint_id) AS open_count
                  FROM employees e
                  LEFT JOIN complaints c ON c.employee_id = e.employee_id AND c.status = 'Open'
                  WHERE e.role = 'Technician'
                  GROUP BY e.employee_id, e.first_name, e.last_name
                  ORDER BY e.last_name";
        return $this->run($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assign($id, $employeeId)
    {
        return $this->run(
            "UPDATE " . $this->table . " SET employee_id = :employee_id WHERE complaint_id = :id",
            [':employee_id' => $employeeId, ':id' => $id]
        );
    }

    public function update($id)
    {
        $query = "UPDATE " . $this->table . "
                  SET technician_notes = :notes, status = :status,
                      resolution_date = :res_date, resolution_notes = :res_notes
                  WHERE complaint_id = :id";

        return $this->run($query, [
            ':notes' => $this->technicianNotes,
            ':status' => $this->status,
            ':res_date' => $this->resolutionDate,
            ':res_notes' => $this->resolutionNotes,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        return $this->run("DELETE FROM " . $this->table . " WHERE complaint_id = :id", [':id' => $id]);
    }
}
