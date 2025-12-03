<?php
// config/auth.php
// Centralized Authentication + Permission System (FIXED)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

$database = new Database();
$conn = $database->connect();

// ========================================
// CHECK IF LOGGED IN
// ========================================
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// ========================================
// REQUIRE LOGIN
// ========================================
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// ========================================
// CHECK PERMISSION (MAIN FUNCTION)
// ========================================
function hasPermission($permission) {
    global $conn;

    if (!isLoggedIn() || empty($_SESSION['admin_role'])) {
        return false;
    }

    $role = $_SESSION['admin_role'];

    try {
        $stmt = $conn->prepare("SELECT allowed FROM admin_permissions WHERE role = ? AND permission = ?");
        $stmt->execute([$role, $permission]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result && $result['allowed'] == 1;
    } catch (Exception $e) {
        error_log("Permission check failed: " . $e->getMessage());
        return false;
    }
}

// ========================================
// GET DEFAULT PAGE FOR USER ROLE
// ========================================
function getDefaultPage() {
    // Admin goes to dashboard
    if (hasPermission('view_dashboard')) {
        return 'dashboard.php';
    }
    
    // Editor goes to first page editor
    if (hasPermission('edit_pages')) {
        return 'index_editor.php';
    }
    
    // No permissions - logout
    return 'logout.php';
}

// ========================================
// REQUIRE PERMISSION (FIXED - No loop)
// ========================================
function requirePermission($permission, $redirect_to = null) {
    requireLogin();

    if (!hasPermission($permission)) {
        // If no redirect specified, send to default page for their role
        if ($redirect_to === null) {
            $redirect_to = getDefaultPage();
        }
        
        $_SESSION['error_msg'] = "Access Denied: You don't have permission to access this page.";
        header("Location: $redirect_to");
        exit();
    }
}

// ========================================
// HELPER: Quick check if Admin
// ========================================
function isAdmin() {
    return ($_SESSION['admin_role'] ?? '') === 'admin';
}

// ========================================
// OLD requireRole() - Backward compatible
// ========================================
function requireRole($required_role) {
    requireLogin();
    if (($_SESSION['admin_role'] ?? '') !== $required_role) {
        $_SESSION['error_msg'] = "Access denied. {$required_role} role required.";
        $redirect = getDefaultPage();
        header("Location: $redirect");
        exit();
    }
}

// ========================================
// LOGOUT
// ========================================
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>