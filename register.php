<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Customer.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = new Customer(db());
    $errors = validateCustomerFields($_POST);

    $passwordError = validatePassword($_POST['password'] ?? '');
    if ($passwordError) {
        $errors[] = $passwordError;
    }

    if (empty($errors) && $customer->getByEmail(trim($_POST['email']))) {
        $errors[] = 'That email is already registered.';
    }

    if (empty($errors)) {
        $customer->fill($_POST);
        $customer->passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $customer->create();
        header('Location: login.php?registered=1');
        exit;
    }
}

pageHeader('Register');
?>
<h1>Create Account</h1>
<?php showErrors($errors); ?>

<form method="post" class="card">
    <?php customerFields(); ?>

    <label>Password</label>
    <input type="password" name="password" required>
    <p class="muted">8+ characters with an uppercase letter, a lowercase letter, and a number.</p>

    <button type="submit">Register</button>
</form>
<?php pageFooter(); ?>
