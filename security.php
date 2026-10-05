<?php

// HTTPS: redirect plain http to https
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}

session_set_cookie_params(['httponly' => true, 'secure' => true, 'samesite' => 'Strict']);
session_start();

// Authentication: must be logged in
function requireLogin() {
    if (!isset($_SESSION['employee_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Authorization: must be an Admin
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'Admin') {
        http_response_code(403);
        exit('Access denied.');
    }
}
