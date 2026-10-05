<?php
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Employee.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee = new Employee((new Database())->connect());
    $user = $employee->getByEmail(trim($_POST['email'] ?? ''));

    if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['employee_id'] = $user['employee_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h1>Employee Login</h1>
    <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <p>Email: <input type="email" name="email" maxlength="100" required></p>
        <p>Password: <input type="password" name="password" required></p>
        <p><button type="submit">Log In</button></p>
    </form>
    <p><a href="register.php">Create an account</a></p>
</body>
</html>
