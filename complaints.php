<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/ComplaintType.php';
require_once __DIR__ . '/../controllers/ComplaintController.php';

requireRole('Customer');

$productModel = new Product(db());
$typeModel = new ComplaintType(db());
$controller = new ComplaintController();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? '';
    $typeId = $_POST['complaint_type_id'] ?? '';

    if (!$productModel->getById($productId)) {
        $errors[] = 'Choose a product or service.';
    }
    if (!$typeModel->getById($typeId)) {
        $errors[] = 'Choose a complaint type.';
    }

    $descriptionError = validateText($_POST['description'] ?? '', 'Description', 1000);
    if ($descriptionError) {
        $errors[] = $descriptionError;
    }

    $imageName = null;
    if (empty($errors)) {
        $imageName = saveComplaintImage($_FILES['image'] ?? null, $imageError);
        if ($imageError) {
            $errors[] = $imageError;
        }
    }

    if (empty($errors)) {
        $controller->addComplaint([
            'customer_id' => $_SESSION['user_id'],
            'product_id' => $productId,
            'complaint_type_id' => $typeId,
            'description' => $_POST['description']
        ], $imageName);

        header('Location: complaints.php?submitted=1');
        exit;
    }
}

$products = $productModel->getAll();
$types = $typeModel->getAll();
$complaints = $controller->forCustomer($_SESSION['user_id']);

pageHeader('Complaints');
?>
<h1>Submit a Complaint</h1>
<?php if (isset($_GET['submitted'])): ?><div class="success">Your complaint was submitted.</div><?php endif; ?>
<?php showErrors($errors); ?>

<form method="post" enctype="multipart/form-data" class="card">
    <label>Product / Service</label>
    <select name="product_id" required>
        <option value="">Select...</option>
        <?php foreach ($products as $p): ?>
            <option value="<?= e($p['product_id']) ?>" <?= (($_POST['product_id'] ?? '') == $p['product_id']) ? 'selected' : '' ?>>
                <?= e($p['product_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Complaint Type</label>
    <select name="complaint_type_id" required>
        <option value="">Select...</option>
        <?php foreach ($types as $t): ?>
            <option value="<?= e($t['complaint_type_id']) ?>" <?= (($_POST['complaint_type_id'] ?? '') == $t['complaint_type_id']) ? 'selected' : '' ?>>
                <?= e($t['type_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Description</label>
    <textarea name="description" rows="5" maxlength="1000" required><?= val('description') ?></textarea>

    <label>Image (optional - JPG, PNG, or GIF, up to 2 MB)</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/gif">

    <button type="submit">Submit Complaint</button>
</form>

<h2>My Complaints</h2>
<?php if (!$complaints): ?><p class="muted">You haven't submitted any complaints yet.</p><?php endif; ?>

<?php foreach ($complaints as $c): ?>
    <div class="card">
        <strong>#<?= e($c['complaint_id']) ?> &middot; <?= e($c['product_name']) ?></strong>
        <span class="badge <?= strtolower($c['status']) ?>"><?= e($c['status']) ?></span>
        <div class="muted"><?= e($c['type_name']) ?> &middot; submitted <?= e(substr($c['date_submitted'], 0, 10)) ?></div>

        <p><?= nl2br(e($c['description'])) ?></p>
        <?php if ($c['image_path']): ?>
            <img class="thumb" src="uploads/<?= e($c['image_path']) ?>" alt="Complaint image">
        <?php endif; ?>

        <?php if ($c['technician_notes']): ?>
            <div class="label">Technician notes</div>
            <div><?= nl2br(e($c['technician_notes'])) ?></div>
        <?php endif; ?>

        <?php if ($c['status'] === 'Closed'): ?>
            <div class="label">Resolved <?= e($c['resolution_date']) ?></div>
            <div><?= nl2br(e($c['resolution_notes'])) ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php pageFooter(); ?>
