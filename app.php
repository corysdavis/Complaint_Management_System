<?php
require_once __DIR__ . '/Database.php';

//HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}

session_set_cookie_params(['httponly' => true, 'secure' => true, 'samesite' => 'Strict']);
session_start();

//authentication
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// authorization
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('Access denied.');
    }
}


function db() {
    static $conn = null;
    if ($conn === null) {
        $conn = (new Database())->connect();
    }
    return $conn;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function val($name, $row = []) {
    return e($_POST[$name] ?? ($row[$name] ?? ''));
}

function showErrors($errors) {
    foreach ($errors as $error) {
        echo '<div class="error">' . e($error) . '</div>';
    }
}

//Validation

function validateText($value, $label, $max, $required = true) {
    $value = trim($value);
    if ($required && $value === '') {
        return "$label is required.";
    }
    if (strlen($value) > $max) {
        return "$label must be $max characters or less.";
    }
    return '';
}

function validateEmail($value) {
    $value = trim($value);
    if (!filter_var($value, FILTER_VALIDATE_EMAIL) || strlen($value) > 100) {
        return 'Enter a valid email address (max 100 characters).';
    }
    return '';
}

function validatePhone($value) {
    if (!preg_match('/^\(?\d{3}\)?[-. ]?\d{3}[-. ]?\d{4}$/', trim($value))) {
        return 'Phone must be a 10-digit number (example: 757-555-0100).';
    }
    return '';
}

function validateState($value) {
    return preg_match('/^[A-Za-z]{2}$/', trim($value)) ? '' : 'State must be a 2-letter abbreviation.';
}

function validateZip($value) {
    return preg_match('/^\d{5}(-\d{4})?$/', trim($value)) ? '' : 'Zip code must be 5 digits (or ZIP+4).';
}

function validatePassword($value) {
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $value)) {
        return 'Password needs 8+ characters with an uppercase letter, a lowercase letter, and a number.';
    }
    return '';
}

function validateCustomerFields($d) {
    return array_values(array_filter([
        validateText($d['first_name'] ?? '', 'First name', 50),
        validateText($d['last_name'] ?? '', 'Last name', 50),
        validateEmail($d['email'] ?? ''),
        validateText($d['street_address'] ?? '', 'Street address', 100),
        validateText($d['city'] ?? '', 'City', 50),
        validateState($d['state'] ?? ''),
        validateZip($d['zip_code'] ?? ''),
        validatePhone($d['phone'] ?? ''),
    ]));
}

function validateEmployeeFields($d) {
    return array_values(array_filter([
        validateText($d['first_name'] ?? '', 'First name', 50),
        validateText($d['last_name'] ?? '', 'Last name', 50),
        validateEmail($d['email'] ?? ''),
        validateText($d['phone_ext'] ?? '', 'Phone extension', 10, false),
        in_array($d['role'] ?? '', ['Administrator', 'Technician']) ? '' : 'Choose a level.',
    ]));
}

//Image upload
function saveComplaintImage($file, &$error) {
    $error = '';

    if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Image upload failed. Try a smaller file.';
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $error = 'Image must be 2 MB or less.';
        return null;
    }

    $info = @getimagesize($file['tmp_name']);
    $extensions = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif'];

    if ($info === false || !isset($extensions[$info[2]])) {
        $error = 'Image must be a JPG, PNG, or GIF.';
        return null;
    }

    $dir = __DIR__ . '/../views/uploads/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $type = $info[2];
    $name = bin2hex(random_bytes(8)) . '.' . $extensions[$type];
    $dest = $dir . $name;

    if ($info[0] > 800 && function_exists('imagescale')) {
        if ($type === IMAGETYPE_JPEG) {
            $src = imagecreatefromjpeg($file['tmp_name']);
        } elseif ($type === IMAGETYPE_PNG) {
            $src = imagecreatefrompng($file['tmp_name']);
        } else {
            $src = imagecreatefromgif($file['tmp_name']);
        }

        $scaled = imagescale($src, 800);

        if ($type === IMAGETYPE_JPEG) {
            imagejpeg($scaled, $dest, 85);
        } elseif ($type === IMAGETYPE_PNG) {
            imagepng($scaled, $dest);
        } else {
            imagegif($scaled, $dest);
        }
    } else {
        move_uploaded_file($file['tmp_name'], $dest);
    }

    return $name;
}

require_once __DIR__ . '/../views/layout.php';
