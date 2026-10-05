<?php
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Employee.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $employee = new Employee((new Database())->connect());

    if ($first === '' || $last === '' || strlen($first) > 50 || strlen($last) > 50) {
        $error = 'Enter a first and last name (max 50 characters).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $error = 'Enter a valid email (max 100 characters).';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = 'Password needs 8+ characters with an uppercase letter, a lowercase letter, and a number.';
    } elseif ($employee->getByEmail($email)) {
        $error = 'That email is already registered.';
    } else {
        $employee->firstName = $first;
        $employee->lastName = $last;
        $employee->email = $email;
        $employee->role = 'Agent';
        $employee->passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $employee->create();
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h1>Create Account</h1>
    <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <p>First Name: <input type="text" name="first_name" maxlength="50" required></p>
        <p>Last Name: <input type="text" name="last_name" maxlength="50" required></p>
        <p>Email: <input type="email" name="email" maxlength="100" required></p>
        <p>Password: <input type="password" name="password" required></p>
        <p><button type="submit">Register</button></p>
    </form>
    <p><a href="login.php">Back to login</a></p>
</body>
</html>
