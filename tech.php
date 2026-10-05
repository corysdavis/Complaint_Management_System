<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/ComplaintController.php';

requireRole('Technician');

$controller = new ComplaintController();
$id = $_GET['id'] ?? null;
$errors = [];
$saved = false;

if ($id !== null) {
    $c = $controller->getComplaint($id);

    if (!$c || $c['employee_id'] != $_SESSION['user_id']) {
        http_response_code(404);
        exit('Complaint not found.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $notes = trim($_POST['technician_notes'] ?? '');
        $resolved = isset($_POST['resolved']);
        $resNotes = trim($_POST['resolution_notes'] ?? '');
        $resDate = trim($_POST['resolution_date'] ?? '');

        $errors = array_values(array_filter([
            validateText($notes, 'Technician notes', 2000, false),
            validateText($resNotes, 'Resolution notes', 2000, $resolved),
        ]));

        if ($resolved) {
            $d = DateTime::createFromFormat('Y-m-d', $resDate);
            if (!$d || $d->format('Y-m-d') !== $resDate) {
                $errors[] = 'Enter a valid resolution date.';
            }
        }

        if (empty($errors)) {
            $controller->updateComplaint($id, [
                'technician_notes' => ($notes === '') ? null : $notes,
                'status' => $resolved ? 'Closed' : 'Open',
                'resolution_date' => $resolved ? $resDate : null,
                'resolution_notes' => ($resNotes === '') ? null : $resNotes
            ]);
            $saved = true;
            $c = $controller->getComplaint($id);
        }
    }

    $isChecked = isset($_POST['resolved']) || ($_SERVER['REQUEST_METHOD'] !== 'POST' && $c['status'] === 'Closed');
} else {
    $complaints = $controller->forTechnician($_SESSION['user_id']);
}

pageHeader($id !== null ? 'Complaint #' . $c['complaint_id'] : 'My Assigned Complaints');
?>
<?php if ($id === null): ?>
    <h1>My Assigned Complaints</h1>

    <?php if (!$complaints): ?>
        <p class="muted">No complaints are assigned to you right now.</p>
    <?php else: ?>
        <table>
            <tr><th>ID</th><th>Submitted</th><th>Customer</th><th>Product</th><th>Type</th><th>Status</th><th></th></tr>
            <?php foreach ($complaints as $row): ?>
                <tr>
                    <td>#<?= e($row['complaint_id']) ?></td>
                    <td><?= e(substr($row['date_submitted'], 0, 10)) ?></td>
                    <td><?= e($row['customer_first'] . ' ' . $row['customer_last']) ?></td>
                    <td><?= e($row['product_name']) ?></td>
                    <td><?= e($row['type_name']) ?></td>
                    <td><span class="badge <?= strtolower($row['status']) ?>"><?= e($row['status']) ?></span></td>
                    <td><a href="tech.php?id=<?= e($row['complaint_id']) ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php else: ?>
    <h1>Complaint #<?= e($c['complaint_id']) ?>
        <span class="badge <?= strtolower($c['status']) ?>"><?= e($c['status']) ?></span>
    </h1>

    <div class="card">
        <div class="label">Customer</div>
        <div><?= e($c['customer_first'] . ' ' . $c['customer_last']) ?></div>
        <div class="label">Product / Type</div>
        <div><?= e($c['product_name']) ?> &middot; <?= e($c['type_name']) ?></div>
        <div class="label">Submitted</div>
        <div><?= e(substr($c['date_submitted'], 0, 10)) ?></div>
        <div class="label">Description</div>
        <div><?= nl2br(e($c['description'])) ?></div>
        <?php if ($c['image_path']): ?>
            <img class="thumb" src="uploads/<?= e($c['image_path']) ?>" alt="Complaint image">
        <?php endif; ?>
    </div>

    <?php if ($saved): ?><div class="success">Complaint updated.</div><?php endif; ?>
    <?php showErrors($errors); ?>

    <form method="post" class="card">
        <label>Technician Notes / Analysis</label>
        <textarea name="technician_notes" rows="4" maxlength="2000"><?= val('technician_notes', $c) ?></textarea>

        <label><input type="checkbox" name="resolved" <?= $isChecked ? 'checked' : '' ?>> Mark resolved</label>

        <label>Resolution Date</label>
        <input type="date" name="resolution_date" value="<?= val('resolution_date', $c) ?: date('Y-m-d') ?>">

        <label>Resolution Notes (required to resolve)</label>
        <textarea name="resolution_notes" rows="4" maxlength="2000"><?= val('resolution_notes', $c) ?></textarea>

        <button type="submit">Save</button>
        <a class="btn secondary" href="tech.php">Back</a>
    </form>
<?php endif; ?>
<?php pageFooter(); ?>
