<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Employee.php';

requireRole('Administrator');

$employee = new Employee(db());
$id = $_GET['id'] ?? null;
$row = ($id !== null) ? $employee->getById($id) : null;
$errors = [];

if ($id !== null && !$row) {
    http_response_code(404);
    exit('Employee not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateEmployeeFields($_POST);
    $other = $employee->getByEmail(trim($_POST['email'] ?? ''));

    if ($id !== null) {
        // Update an employee
        if (empty($errors) && $other && $other['employee_id'] != $id) {
            $errors[] = 'That email is already in use.';
        }

        if (empty($errors)) {
            $employee->fill($_POST);
            $employee->update($id);
            header('Location: admin_employees.php?saved=1');
            exit;
        }
    } else {
        //add an employee
        $userId = trim($_POST['user_id'] ?? '');

        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $userId)) {
            $errors[] = 'User ID must be 3-30 letters, numbers, or underscores.';
        }

        $passwordError = validatePassword($_POST['password'] ?? '');
        if ($passwordError) {
            $errors[] = $passwordError;
        }

        if (empty($errors)) {
            if ($employee->getByUserId($userId)) {
                $errors[] = 'That user ID is already taken.';
            }
            if ($other) {
                $errors[] = 'That email is already in use.';
            }
        }

        if (empty($errors)) {
            $employee->fill($_POST);
            $employee->userId = $userId;
            $employee->passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $employee->create();
            header('Location: admin_employees.php?added=1');
            exit;
        }
    }
}

$employees = ($id === null) ? $employee->getAll() : [];

pageHeader($id === null ? 'Employees' : 'Edit Employee');
?>
<?php if ($id === null): ?>
    <h1>Employees</h1>
    <?php if (isset($_GET['added'])): ?><div class="success">Employee added.</div><?php endif; ?>
    <?php if (isset($_GET['saved'])): ?><div class="success">Employee updated.</div><?php endif; ?>

    <table>
        <tr><th>User ID</th><th>Name</th><th>Email</th><th>Ext.</th><th>Level</th><th></th></tr>
        <?php foreach ($employees as $emp): ?>
            <tr>
                <td><?= e($emp['user_id']) ?></td>
                <td><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                <td><?= e($emp['email']) ?></td>
                <td><?= e($emp['phone_ext']) ?></td>
                <td><?= e($emp['role']) ?></td>
                <td><a href="admin_employees.php?id=<?= e($emp['employee_id']) ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Add Employee</h2>
    <?php showErrors($errors); ?>
    <form method="post" class="card">
        <label>User ID (cannot be changed later)</label>
        <input type="text" name="user_id" maxlength="30" value="<?= val('user_id') ?>" required>

        <?php employeeFields(); ?>

        <label>Temporary Password</label>
        <input type="password" name="password" required>

        <button type="submit">Add Employee</button>
    </form>
<?php else: ?>
    <h1>Edit Employee</h1>
    <?php showErrors($errors); ?>

    <form method="post" class="card">
        <div class="label">User ID</div>
        <div><?= e($row['user_id']) ?></div>

        <?php employeeFields($row); ?>

        <button type="submit">Save Changes</button>
        <a class="btn secondary" href="admin_employees.php">Cancel</a>
    </form>
<?php endif; ?>
<?php pageFooter(); ?>
