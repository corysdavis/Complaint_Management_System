<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Employee.php';

requireLogin();

// Customers update their information; staff change their password
$isCustomer = $_SESSION['role'] === 'Customer';
$model = $isCustomer ? new Customer(db()) : new Employee(db());
$row = $model->getById($_SESSION['user_id']);
$errors = [];
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isCustomer) {
        $errors = validateCustomerFields($_POST);
        $other = $model->getByEmail(trim($_POST['email'] ?? ''));

        if (empty($errors) && $other && $other['customer_id'] != $_SESSION['user_id']) {
            $errors[] = 'That email is already in use.';
        }

        if (empty($errors)) {
            $model->fill($_POST);
            $model->update($_SESSION['user_id']);
            $_SESSION['first_name'] = trim($_POST['first_name']);
            $saved = true;
        }
    } else {
        $new = $_POST['new_password'] ?? '';

        if (!password_verify($_POST['current_password'] ?? '', $row['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        }

        $passwordError = validatePassword($new);
        if ($passwordError) {
            $errors[] = $passwordError;
        }

        if ($new !== ($_POST['confirm_password'] ?? '')) {
            $errors[] = 'New passwords do not match.';
        }

        if (empty($errors)) {
            $model->updatePassword($_SESSION['user_id'], password_hash($new, PASSWORD_DEFAULT));
            $saved = true;
        }
    }
}

pageHeader($isCustomer ? 'My Information' : 'Change Password');
?>
<h1><?= $isCustomer ? 'My Information' : 'Change Password' ?></h1>
<?php if ($saved): ?><div class="success">Saved.</div><?php endif; ?>
<?php showErrors($errors); ?>

<form method="post" class="card">
    <?php if ($isCustomer): ?>
        <?php customerFields($row); ?>
        <button type="submit">Save Changes</button>
    <?php else: ?>
        <label>Current Password</label>
        <input type="password" name="current_password" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Update Password</button>
    <?php endif; ?>
</form>
<?php pageFooter(); ?>
