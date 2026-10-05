<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Complaint.php';

class ComplaintController {
    private $complaint;

    public function __construct() {
        $this->complaint = new Complaint(db());
    }

    public function addComplaint($data, $imageName) {
        $this->complaint->customerId = $data['customer_id'];
        $this->complaint->productId = $data['product_id'];
        $this->complaint->complaintTypeId = $data['complaint_type_id'];
        $this->complaint->description = trim($data['description']);
        $this->complaint->imagePath = $imageName;

        return $this->complaint->create();
    }

    public function listComplaints() {
        return $this->complaint->getAll();
    }

    public function getComplaint($id) {
        return $this->complaint->getById($id);
    }

    public function forCustomer($customerId) {
        return $this->complaint->getByCustomer($customerId);
    }

    public function forTechnician($employeeId) {
        return $this->complaint->getByTechnician($employeeId);
    }

    public function openAssigned() {
        return $this->complaint->getOpenAssigned();
    }

    public function openUnassigned() {
        return $this->complaint->getOpenUnassigned();
    }

    public function technicianCounts() {
        return $this->complaint->technicianCounts();
    }

    public function assignComplaint($id, $employeeId) {
        return $this->complaint->assign($id, $employeeId);
    }

    public function updateComplaint($id, $data) {
        $this->complaint->technicianNotes = $data['technician_notes'];
        $this->complaint->status = $data['status'];
        $this->complaint->resolutionDate = $data['resolution_date'];
        $this->complaint->resolutionNotes = $data['resolution_notes'];

        return $this->complaint->update($id);
    }

    public function deleteComplaint($id) {
        return $this->complaint->delete($id);
    }
}
