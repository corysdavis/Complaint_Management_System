<?php

function pageHeader($title)
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> - Complaint Tracker</title>
        <style>
            :root {
                --accent: #2563eb;
                --text: #1f2937;
                --muted: #6b7280;
                --line: #e5e7eb;
                --bg: #f9fafb;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
                color: var(--text);
                background: var(--bg);
                line-height: 1.5;
            }

            nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 14px 24px;
                background: #fff;
                border-bottom: 1px solid var(--line);
            }

            nav a {
                color: var(--text);
                text-decoration: none;
                margin-left: 18px;
                font-size: .95rem;
            }

            nav a:hover {
                color: var(--accent);
            }

            nav .brand {
                font-weight: 600;
                margin-left: 0;
            }

            main {
                max-width: 900px;
                margin: 32px auto;
                padding: 0 24px;
            }

            h1 {
                font-size: 1.6rem;
                font-weight: 600;
                margin: 0 0 16px;
            }

            h2 {
                font-size: 1.1rem;
                font-weight: 600;
                margin: 32px 0 12px;
            }

            a {
                color: var(--accent);
            }

            .card {
                background: #fff;
                border: 1px solid var(--line);
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 16px;
            }

            .hero {
                text-align: center;
                padding: 72px 0;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .muted {
                color: var(--muted);
            }

            .label {
                margin: 14px 0 2px;
                font-size: .78rem;
                text-transform: uppercase;
                letter-spacing: .04em;
                color: var(--muted);
            }

            label {
                display: block;
                margin: 14px 0 4px;
                font-size: .9rem;
                color: var(--muted);
            }

            input,
            select,
            textarea {
                width: 100%;
                padding: 9px 10px;
                border: 1px solid var(--line);
                border-radius: 6px;
                font: inherit;
                background: #fff;
            }

            input:focus,
            select:focus,
            textarea:focus {
                outline: 2px solid var(--accent);
                border-color: transparent;
            }

            input[type=checkbox] {
                width: auto;
                margin-right: 6px;
            }

            .row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
                gap: 12px;
            }

            button,
            .btn {
                display: inline-block;
                margin-top: 16px;
                padding: 9px 18px;
                background: var(--accent);
                color: #fff;
                border: 0;
                border-radius: 6px;
                font: inherit;
                cursor: pointer;
                text-decoration: none;
            }

            button:hover,
            .btn:hover {
                opacity: .9;
            }

            .btn.secondary {
                background: #fff;
                color: var(--accent);
                border: 1px solid var(--accent);
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background: #fff;
                border: 1px solid var(--line);
                font-size: .92rem;
            }

            th,
            td {
                padding: 10px 12px;
                text-align: left;
                border-bottom: 1px solid var(--line);
                vertical-align: top;
            }

            th {
                background: #f3f4f6;
                font-weight: 600;
            }

            .error {
                background: #fef2f2;
                color: #b91c1c;
                border: 1px solid #fecaca;
                padding: 10px 14px;
                border-radius: 6px;
                margin-bottom: 10px;
            }

            .success {
                background: #f0fdf4;
                color: #166534;
                border: 1px solid #bbf7d0;
                padding: 10px 14px;
                border-radius: 6px;
                margin-bottom: 10px;
            }

            .badge {
                display: inline-block;
                margin-left: 8px;
                padding: 2px 10px;
                border-radius: 999px;
                font-size: .8rem;
            }

            .badge.open {
                background: #fef3c7;
                color: #92400e;
            }

            .badge.closed {
                background: #dcfce7;
                color: #166534;
            }

            .thumb {
                display: block;
                max-width: 220px;
                margin-top: 8px;
                border: 1px solid var(--line);
                border-radius: 6px;
            }

            .inline {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .inline select {
                width: auto;
            }

            .inline button {
                margin-top: 0;
                padding: 6px 12px;
            }

            .menu a {
                display: block;
                padding: 14px 18px;
                margin-bottom: 10px;
                background: #fff;
                border: 1px solid var(--line);
                border-radius: 8px;
                text-decoration: none;
                color: var(--text);
            }

            .menu a:hover {
                border-color: var(--accent);
                color: var(--accent);
            }
        </style>
    </head>

    <body>
        <nav>
            <a class="brand" href="index.php">Complaint Tracker</a>
            <span>
                <?php if (isset($_SESSION['role'])) : ?>
                    <a href="index.php">Home</a>
                    <a href="login.php?logout=1">Log Out</a>
                <?php else : ?>
                    <a href="login.php">Log In</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </span>
        </nav>
        <main>
        <?php
    }

    function pageFooter()
    {
        echo "</main>\n</body>\n</html>\n";
    }

    function customerFields($row = [])
    {
        ?>
            <div class="row">
                <div>
                    <label>First Name</label>
                    <input type="text" name="first_name" maxlength="50" value="<?= val('first_name', $row) ?>" required>
                </div>
                <div>
                    <label>Last Name</label>
                    <input type="text" name="last_name" maxlength="50" value="<?= val('last_name', $row) ?>" required>
                </div>
            </div>

            <label>Email</label>
            <input type="email" name="email" maxlength="100" value="<?= val('email', $row) ?>" required>

            <label>Street Address</label>
            <input type="text" name="street_address" maxlength="100" value="<?= val('street_address', $row) ?>" required>

            <div class="row">
                <div>
                    <label>City</label>
                    <input type="text" name="city" maxlength="50" value="<?= val('city', $row) ?>" required>
                </div>
                <div>
                    <label>State</label>
                    <input type="text" name="state" maxlength="2" placeholder="VA" value="<?= val('state', $row) ?>" required>
                </div>
                <div>
                    <label>Zip Code</label>
                    <input type="text" name="zip_code" maxlength="10" value="<?= val('zip_code', $row) ?>" required>
                </div>
            </div>

            <label>Phone</label>
            <input type="text" name="phone" maxlength="20" placeholder="757-555-0100" value="<?= val('phone', $row) ?>" required>
        <?php
    }

    function employeeFields($row = [])
    {
        $currentRole = $_POST['role'] ?? ($row['role'] ?? '');
        ?>
            <div class="row">
                <div>
                    <label>First Name</label>
                    <input type="text" name="first_name" maxlength="50" value="<?= val('first_name', $row) ?>" required>
                </div>
                <div>
                    <label>Last Name</label>
                    <input type="text" name="last_name" maxlength="50" value="<?= val('last_name', $row) ?>" required>
                </div>
            </div>

            <label>Email</label>
            <input type="email" name="email" maxlength="100" value="<?= val('email', $row) ?>" required>

            <div class="row">
                <div>
                    <label>Phone Extension</label>
                    <input type="text" name="phone_ext" maxlength="10" value="<?= val('phone_ext', $row) ?>">
                </div>
                <div>
                    <label>Level</label>
                    <select name="role" required>
                        <?php foreach (['Technician', 'Administrator'] as $r) : ?>
                            <option value="<?= $r ?>" <?= $currentRole === $r ? 'selected' : '' ?>><?= $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php
    }
