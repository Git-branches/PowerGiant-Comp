<?php
// change_password.php - Fully Fixed & Bulletproof Version (Nov 2025)

session_start();  // ← MUST be at the very top, before any output!

require_once '../config/database.php';
require_once '../config/config.php';

// === CRITICAL: Check login session FIRST (before ANY output) ===
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in first.']);
    exit;
}

// Now safe to send JSON header
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Use POST only.']);
    exit;
}

// Get POST data safely
$current_password = $_POST['current_password'] ?? '';
$new_password     = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
    exit;
}

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
    exit;
}

if ($new_password === $current_password) {
    echo json_encode(['success' => false, 'message' => 'New password must be different from current password.']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();

    $admin_id = (int)$_SESSION['admin_id'];

    // Fetch current hashed password
    $stmt = $db->prepare("SELECT password FROM admin_user WHERE id = ? LIMIT 1");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo json_encode(['success' => false, 'message' => 'Admin account not found.']);
        exit;
    }

    // Verify current password
    if (!password_verify($current_password, $admin['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit;
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $update = $db->prepare("UPDATE admin_user SET password = ?, updated_at = NOW() WHERE id = ?");
    $update->execute([$hashed_password, $admin_id]);

    // Success!
    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully! Please log in again for security.'
    ]);

} catch (Exception $e) {
    // Never expose raw errors to frontend
    error_log("Change Password Error (Admin ID: " . ($_SESSION['admin_id'] ?? 'unknown') . "): " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Server error. Please try again later.'
    ]);
}