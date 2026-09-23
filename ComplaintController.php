<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Complaint.php';

class ComplaintController {
    private $db;
    private $complaint;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->complaint = new Complaint($this->db);
    }

    public function listComplaints() {
        return $this->complaint->getAll();
    }

    public function getComplaint($id) {
        return $this->complaint->getById($id);
    }

    public function addComplaint($data) {
        $this->complaint->customerId = $data['customer_id'];
        $this->complaint->productId = $data['product_id'];
        $this->complaint->complaintTypeId = $data['complaint_type_id'];
        $this->complaint->employeeId = $data['employee_id'] ?? null;
        $this->complaint->description = $data['description'];

        return $this->complaint->create();
    }

    public function updateComplaint($id, $data) {
        $this->complaint->status = $data['status'];
        $this->complaint->employeeId = $data['employee_id'] ?? null;

        return $this->complaint->update($id);
    }

    public function deleteComplaint($id) {
        return $this->complaint->delete($id);
    }
}
