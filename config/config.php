<?php
/*=========================================
  SITE CONFIGURATION
=========================================*/
define('SITE_NAME', 'Power Giant RMT');
define('SITE_URL', 'https://powergiantrmt.com');
define('ADMIN_EMAIL', 'admin@powergiantrmt.com');

/*=========================================
  FILE UPLOAD CONFIGURATION
=========================================*/
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'pdf']);

/*=========================================
  SERVER SETTINGS
=========================================*/
date_default_timezone_set('Asia/Manila');

/*=========================================
  ERROR REPORTING
=========================================*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*=========================================
  HELPER FUNCTIONS
=========================================*/
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// ALISIN ANG requireLogin() at isLoggedIn() DITO
// Gamitin na lang sa auth.php
?>