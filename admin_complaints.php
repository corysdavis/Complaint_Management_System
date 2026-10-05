<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../controllers/ComplaintController.php';

requireRole('Administrator');

$controller = new ComplaintController();
$techs = (new Employee(db()))->getTechnicians();

// Assign (or reassign) a complaint to a technician
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $techIds = array_column($techs, 'employee_id');
    $employeeId = $_POST['employee_id'] ?? '';

    if (in_array($employeeId, $techIds) && ctype_digit($_POST['complaint_id'] ?? '')) {
        $controller->assignComplaint($_POST['complaint_id'], $employeeId);
    }

    header('Location: admin_complaints.php?assigned=1');
    exit;
}

function assignForm($complaintId, $techs, $current = null) {
    echo '<form method="post" class="inline"><input type="hidden" name="complaint_id" value="' . e($complaintId) . '">';
    echo '<select name="employee_id">';
    foreach ($techs as $t) {
        $selected = ($current == $t['employee_id']) ? ' selected' : '';
        echo '<option value="' . e($t['employee_id']) . '"' . $selected . '>' . e($t['first_name'] . ' ' . $t['last_name']) . '</option>';
    }
    echo '</select><button type="submit">Assign</button></form>';
}

$counts = $controller->technicianCounts();
$unassigned = $controller->openUnassigned();
$assigned = $controller->openAssigned();

pageHeader('Complaints Overview');
?>
<h1>Complaints Overview</h1>
<?php if (isset($_GET['assigned'])): ?><div class="success">Assignment saved.</div><?php endif; ?>

<h2>Open Complaints by Technician</h2>
<table>
    <tr><th>Technician</th><th>Open Complaints</th></tr>
    <?php foreach ($counts as $t): ?>
        <tr>
            <td><?= e($t['first_name'] . ' ' . $t['last_name']) ?></td>
            <td><?= e($t['open_count']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Open Complaints - No Technician Assigned</h2>
<?php if (!$unassigned): ?>
    <p class="muted">None.</p>
<?php else: ?>
    <table>
        <tr><th>ID</th><th>Customer</th><th>Product</th><th>Type</th><th>Assign</th></tr>
        <?php foreach ($unassigned as $c): ?>
            <tr>
                <td>#<?= e($c['complaint_id']) ?></td>
                <td><?= e($c['customer_first'] . ' ' . $c['customer_last']) ?></td>
                <td><?= e($c['product_name']) ?></td>
                <td><?= e($c['type_name']) ?></td>
                <td><?php assignForm($c['complaint_id'], $techs); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h2>Open Complaints - Assigned</h2>
<?php if (!$assigned): ?>
    <p class="muted">None.</p>
<?php else: ?>
    <table>
        <tr><th>ID</th><th>Customer</th><th>Product</th><th>Type</th><th>Technician</th></tr>
        <?php foreach ($assigned as $c): ?>
            <tr>
                <td>#<?= e($c['complaint_id']) ?></td>
                <td><?= e($c['customer_first'] . ' ' . $c['customer_last']) ?></td>
                <td><?= e($c['product_name']) ?></td>
                <td><?= e($c['type_name']) ?></td>
                <td><?php assignForm($c['complaint_id'], $techs, $c['employee_id']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php pageFooter(); ?>
