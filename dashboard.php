<?php
require_once __DIR__ . '/../config/security.php';

requireLogin();
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?></h1>
    <p>Role: <?= htmlspecialchars($_SESSION['role']) ?></p>
    <?php if ($_SESSION['role'] === 'Admin'): ?>
        <p><a href="admin.php">View All Complaints (Admin)</a></p>
    <?php endif; ?>
    <p><a href="logout.php">Log Out</a></p>
</body>
</html>
