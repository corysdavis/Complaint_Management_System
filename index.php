<?php
require_once __DIR__ . '/../config/app.php';

$role = $_SESSION['role'] ?? null;

pageHeader('Home');
?>
<?php if (!$role): ?>
    <div class="hero">
        <h1>Customer Complaint Tracker</h1>
        <p class="muted">Report an issue, follow its progress, and see how it was resolved.</p>
        <a class="btn" href="login.php">Log In</a>
        <a class="btn secondary" href="register.php">Create Account</a>
    </div>
<?php else: ?>
    <h1>Welcome, <?= e($_SESSION['first_name']) ?></h1>
    <p class="muted"><?= e($role) ?></p>

    <div class="menu">
        <?php if ($role === 'Customer'): ?>
            <a href="complaints.php">Submit &amp; View Complaints</a>
            <a href="account.php">My Information</a>
        <?php elseif ($role === 'Technician'): ?>
            <a href="tech.php">My Assigned Complaints</a>
            <a href="account.php">Change Password</a>
        <?php else: ?>
            <a href="admin_complaints.php">Complaints Overview &amp; Assignment</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_employees.php">Employees</a>
            <a href="account.php">Change Password</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php pageFooter(); ?>
