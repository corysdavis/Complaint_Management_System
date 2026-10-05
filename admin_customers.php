<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Customer.php';

requireRole('Administrator');

$customer = new Customer(db());
$id = $_GET['id'] ?? null;
$row = ($id !== null) ? $customer->getById($id) : null;
$errors = [];

if ($id !== null && !$row) {
    http_response_code(404);
    exit('Customer not found.');
}

// Update a customer
if ($id !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateCustomerFields($_POST);
    $other = $customer->getByEmail(trim($_POST['email'] ?? ''));

    if (empty($errors) && $other && $other['customer_id'] != $id) {
        $errors[] = 'That email is already in use.';
    }

    if (empty($errors)) {
        $customer->fill($_POST);
        $customer->update($id);
        header('Location: admin_customers.php?saved=1');
        exit;
    }
}

$customers = ($id === null) ? $customer->getAll() : [];

pageHeader($id === null ? 'Customers' : 'Edit Customer');
?>
<?php if ($id === null): ?>
    <h1>Customers</h1>
    <?php if (isset($_GET['saved'])): ?><div class="success">Customer updated.</div><?php endif; ?>

    <table>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Location</th><th></th></tr>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= e($c['first_name'] . ' ' . $c['last_name']) ?></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['phone']) ?></td>
                <td><?= e($c['city'] . ', ' . $c['state']) ?></td>
                <td><a href="admin_customers.php?id=<?= e($c['customer_id']) ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <h1>Edit Customer</h1>
    <?php showErrors($errors); ?>

    <form method="post" class="card">
        <?php customerFields($row); ?>
        <button type="submit">Save Changes</button>
        <a class="btn secondary" href="admin_customers.php">Cancel</a>
    </form>
<?php endif; ?>
<?php pageFooter(); ?>
