<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Employee.php';

//log out
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    //log in with an email and staff with a user ID
    $isCustomer = strpos($login, '@') !== false;
    $user = $isCustomer
        ? (new Customer(db()))->getByEmail($login)
        : (new Employee(db()))->getByUserId($login);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $isCustomer ? $user['customer_id'] : $user['employee_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['role'] = $isCustomer ? 'Customer' : $user['role'];
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid login or password.';
}

pageHeader('Log In');
?>
<h1>Log In</h1>

<?php if (isset($_GET['registered'])): ?><div class="success">Account created. You can log in now.</div><?php endif; ?>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>

<form method="post" class="card">
    <label>Email (customers) or User ID (staff)</label>
    <input type="text" name="login" maxlength="100" value="<?= val('login') ?>" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Log In</button>
</form>
<p class="muted">New customer? <a href="register.php">Create an account</a></p>
<?php pageFooter(); ?>
