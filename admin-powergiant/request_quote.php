<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../config/auth.php';
requirePermission('view_inquiries'); // ← ADD THIS


$database = new Database();
$db = $database->connect();

$success_msg = null;
$error_msg = null;

// ---------- UPDATE QUOTE ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quote'])) {
    $quote_id     = intval($_POST['quote_id']);
    $new_status   = sanitize($_POST['status']);
    $new_priority = sanitize($_POST['priority']);
    $notes        = sanitize($_POST['notes'] ?? '');

    try {
        $old_sql  = "SELECT status, priority FROM quote_requests WHERE id = :id";
        $old_stmt = $db->prepare($old_sql);
        $old_stmt->execute([':id' => $quote_id]);
        $old_data = $old_stmt->fetch(PDO::FETCH_ASSOC);

        $sql  = "UPDATE quote_requests SET status = :status, priority = :priority, notes = :notes, updated_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':status'   => $new_status,
            ':priority' => $new_priority,
            ':notes'    => $notes,
            ':id'       => $quote_id
        ]);

        if ($old_data['status'] !== $new_status) {
            $log_sql = "INSERT INTO activity_logs (quote_id, action, old_value, new_value, description)
                        VALUES (:quote_id, 'status_changed', :old_value, :new_value, :description)";
            $log_stmt = $db->prepare($log_sql);
            $log_stmt->execute([
                ':quote_id'    => $quote_id,
                ':old_value'   => $old_data['status'],
                ':new_value'   => $new_status,
                ':description' => "Status changed from {$old_data['status']} to $new_status"
            ]);
        }

        if ($old_data['priority'] !== $new_priority) {
            $log_sql = "INSERT INTO activity_logs (quote_id, action, old_value, new_value, description)
                        VALUES (:quote_id, 'priority_changed', :old_value, :new_value, :description)";
            $log_stmt = $db->prepare($log_sql);
            $log_stmt->execute([
                ':quote_id'    => $quote_id,
                ':old_value'   => $old_data['priority'],
                ':new_value'   => $new_priority,
                ':description' => "Priority changed from {$old_data['priority']} to $new_priority"
            ]);
        }

        $_SESSION['success_msg'] = "Quote #$quote_id updated successfully!";
        header("Location: request_quote.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Error updating quote: " . $e->getMessage();
        header("Location: request_quote.php");
        exit();
    }
}

// ---------- DELETE QUOTE ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_quote'])) {
    $quote_id = intval($_POST['quote_id']);

    try {
        $log_sql = "INSERT INTO activity_logs (quote_id, action, old_value, new_value, description)
                    VALUES (:quote_id, 'quote_deleted', 'active', 'deleted', :description)";
        $log_stmt = $db->prepare($log_sql);
        $log_stmt->execute([
            ':quote_id'    => $quote_id,
            ':description' => "Quote request #$quote_id was permanently deleted by Admin."
        ]);

        $sql  = "DELETE FROM quote_requests WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $quote_id]);

        $_SESSION['success_msg'] = "Quote #$quote_id successfully deleted.";
        header("Location: request_quote.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Error deleting quote: " . $e->getMessage();
        header("Location: request_quote.php");
        exit();
    }
}

// ---------- FILTER & SEARCH ----------
$filter_status = $_GET['status'] ?? '';
$search        = $_GET['search'] ?? '';

// Get messages from session
if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

$sql    = "SELECT * FROM quote_requests WHERE 1=1";
$params = [];

if ($filter_status) {
    $sql .= " AND status = :status";
    $params[':status'] = $filter_status;
}
if ($search) {
    $sql .= " AND (company_name LIKE :search_company OR contact_person LIKE :search_contact OR email LIKE :search_email OR service LIKE :search_service)";
    $search_term = "%$search%";
    $params[':search_company'] = $search_term;
    $params[':search_contact'] = $search_term;
    $params[':search_email']   = $search_term;
    $params[':search_service'] = $search_term;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$quotes = $stmt->fetchAll();

$admin_name = $_SESSION['admin_name'] ?? 'Admin User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Management - Power Giant RMT</title>
    <link rel="icon" type="image/png" href="assets/images/POWER-GIANT.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        #sidebarOverlay, #modalOverlay, #userDropdown { display: none; }
        #sidebarOverlay.show, #modalOverlay.show, #userDropdown.show { display: block; }
        #sidebar { transition: transform 0.3s ease-in-out; }
        #settingsModal { transition: all 0.3s ease-in-out; transform: scale(0.9); opacity: 0; }
        #settingsModal.show { transform: scale(1); opacity: 1; }

        .dropdown-content { transition: max-height 0.3s ease, padding 0.3s ease; overflow: hidden; }
        .badge { @apply inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold; }
        .priority-dot { @apply w-2 h-2 rounded-full; }

        .toast-notification {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            z-index: 99999; min-width: 320px; max-width: 500px;
            animation: slideDown 0.4s ease-out;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .toast-fade-out { animation: fadeOut 0.5s ease-out forwards; }
        @keyframes slideDown { from { opacity: 0; transform: translateX(-50%) translateY(-100%); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
        @keyframes fadeOut { to { opacity: 0; transform: translateX(-50%) translateY(-20px); } }

        @media (max-width: 640px) { .table-desktop { display: none; } .card-mobile { display: block; } }
        @media (min-width: 641px) { .table-desktop { display: table; } .card-mobile { display: none; } }
    </style>
</head>
<body class="bg-gray-50 antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Overlay (Mobile Sidebar) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Settings Modal Overlay -->
    <div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50" onclick="closeSettingsModal()"></div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Change Password</h3>
                <button onclick="closeSettingsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form id="changePasswordForm" method="POST" action="change_password.php" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('current_password', 'current_eye')"
                                    class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg id="current_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 10 5c2.478 0 6.268 2.943 7.542 7-1.274 4.057-5.064 7-7.542 7-2.478 0-6.268-2.943-7.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" id="new_password" name="new_password" required minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('new_password', 'new_eye')"
                                    class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg id="new_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 10 5c2.478 0 6.268 2.943 7.542 7-1.274 4.057-5.064 7-7.542 7-2.478 0-6.268-2.943-7.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('confirm_password', 'confirm_eye')"
                                    class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg id="confirm_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 10 5c2.478 0 6.268 2.943 7.542 7-1.274 4.057-5.064 7-7.542 7-2.478 0-6.268-2.943-7.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="passwordMessage" class="hidden p-3 rounded-lg text-sm"></div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeSettingsModal()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" id="submitPassword" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center justify-center">
                            <span id="submitText">Change Password</span>
                            <svg id="submitSpinner" class="hidden animate-spin ml-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <!-- Role-Based Sidebar Menu -->
<aside id="sidebar" class="bg-gray-900 text-white w-64 fixed inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 lg:static shadow-2xl">
    <div class="p-6 h-full flex flex-col">
        <div class="flex items-center justify-between mb-10 border-b border-gray-700 pb-4">
            <h2 class="text-3xl font-extrabold text-white">PG RMT <span class="text-blue-400">Pro</span></h2>
            <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg bg-gray-800 hover:bg-red-600 text-gray-300 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="space-y-4 flex-1">
            <!-- DASHBOARD - Admin Only -->
            <?php if (hasPermission('view_dashboard')): ?>
            <a href="dashboard.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-xl transition hover:text-white">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1-1h-3M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Dashboard
            </a>
            <?php endif; ?>

            <!-- INQUIRIES - Admin Only -->
            <?php if (hasPermission('view_inquiries')): ?>
            <a href="request_quote.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-xl transition hover:text-white">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Inquire Request
            </a>
            <?php endif; ?>

            <!-- USER MANAGEMENT - Admin Only -->
            <?php if (hasPermission('manage_users')): ?>
            <a href="user_management.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-xl transition hover:text-white">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                User Management
            </a>
            <?php endif; ?>

            <!-- PAGE MANAGEMENT - Both Admin & Editor -->
            <?php if (hasPermission('edit_pages')): ?>
            <div class="border-t border-gray-700 pt-4">
                <button onclick="togglePageManagement()" class="w-full flex items-center justify-between px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-white transition group">
                    <span class="flex items-center">
                        <svg id="pageMgmtCaret" class="w-4 h-4 mr-2 transition-transform duration-200 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        Page Management
                    </span>
                </button>

                <div id="pageManagementDropdown" class="max-h-0 overflow-hidden transition-all duration-300 px-2">
                    <div class="space-y-1 py-2">
                        <a href="index_editor.php" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 rounded-lg transition hover:text-white text-sm">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1-1h-3M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Home Page
                        </a>
                        <a href="about_editor.php" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 rounded-lg transition hover:text-white text-sm">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            About Page
                        </a>
                        <a href="service_editor.php" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 rounded-lg transition hover:text-white text-sm">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Services Page
                        </a>
                        <a href="project_editor.php" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 rounded-lg transition hover:text-white text-sm">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Projects Page
                        </a>
                        <a href="contact_editor.php" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 rounded-lg transition hover:text-white text-sm">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contact Page
                        </a>
                        <a href="compliance_editor.php" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 rounded-lg transition hover:text-white text-sm">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Compliance Page
                        </a>
                        <a href="news_editor.php" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 rounded-lg transition hover:text-white text-sm">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            News Page
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </nav>
    </div>
</aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-lg sticky top-0 z-20">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="lg:hidden text-gray-600 p-2 rounded-lg hover:bg-gray-100 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Manage Inquiry</h1>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button onclick="toggleUserDropdown()" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition">
                            <span class="hidden sm:inline text-sm md:text-base font-medium">Welcome, <span class="text-blue-600 font-semibold"><?php echo htmlspecialchars($admin_name); ?></span></span>
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg shadow-md">A</div>
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="userDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 hidden">
                            <a href="#" onclick="openSettingsModal(); toggleUserDropdown();" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 10 5c2.478 0 6.268 2.943 7.542 7-1.274 4.057-5.064 7-7.542 7-2.478 0-6.268-2.943-7.542-7z"/>
                                </svg>
                                Change Password
                            </a>
                            <a href="backup_maintenance.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m-8 6h.01M12 13h.01M12 16h.01"/>
                                </svg>
                                Backup & Maintenance
                            </a>
                            <hr class="my-1 border-gray-200">
                            <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <?php if ($success_msg): ?>
                <div id="successMessage" class="toast-notification bg-green-50 border-l-4 border-green-500 text-gray-800 px-6 py-4 rounded-lg shadow-xl flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-green-800">Success!</p>
                        <p class="text-sm text-gray-700"><?php echo htmlspecialchars($success_msg); ?></p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div id="errorMessage" class="toast-notification bg-red-50 border-l-4 border-red-500 text-gray-800 px-6 py-4 rounded-lg shadow-xl flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-red-800">Error!</p>
                        <p class="text-sm text-gray-700"><?php echo htmlspecialchars($error_msg); ?></p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-800">Search & Filter Quotes</h3>
                </div>
                <form method="GET" class="flex flex-wrap items-end gap-3 lg:gap-4">
                    <div class="flex-1 min-w-[200px] lg:min-w-80">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Keywords</label>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Company, contact, email, or service..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="min-w-[150px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                        <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">— All Status —</option>
                            <option value="new" <?php echo $filter_status === 'new' ? 'selected' : ''; ?>>New Request</option>
                            <option value="in_progress" <?php echo $filter_status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="quoted" <?php echo $filter_status === 'quoted' ? 'selected' : ''; ?>>Quoted</option>
                            <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 sm:px-6 py-2.5 rounded-xl transition shadow-md">Apply Filter</button>
                    <a href="request_quote.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 sm:px-6 py-2.5 rounded-xl transition shadow-md">Reset</a>
                </form>
            </div>

            <?php
            $statusConfig = [
                'new' => ['class' => 'bg-blue-100 text-blue-700', 'label' => 'New'],
                'in_progress' => ['class' => 'bg-amber-100 text-amber-700', 'label' => 'In Progress'],
                'quoted' => ['class' => 'bg-purple-100 text-purple-700', 'label' => 'Quoted'],
                'completed' => ['class' => 'bg-green-100 text-green-700', 'label' => 'Completed'],
                'cancelled' => ['class' => 'bg-gray-100 text-gray-700', 'label' => 'Cancelled']
            ];
            $priorityConfig = [
                'low' => ['class' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400', 'label' => 'Low'],
                'medium' => ['class' => 'bg-yellow-100 text-yellow-700', 'dot' => 'bg-yellow-500', 'label' => 'Medium'],
                'high' => ['class' => 'bg-orange-100 text-orange-700', 'dot' => 'bg-orange-500', 'label' => 'High'],
                'urgent' => ['class' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-500', 'label' => 'Urgent']
            ];
            ?>

            <!-- Desktop Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden table-desktop w-full">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Company</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php foreach ($quotes as $q): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 whitespace-nowrap"><span class="text-sm font-semibold text-gray-900">#<?php echo $q['id']; ?></span></td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($q['company_name']); ?></div>
                                <div class="text-xs text-gray-500 mt-0.5"><?php echo htmlspecialchars($q['delivery_location']); ?></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($q['contact_person']); ?></div>
                                <div class="text-xs text-gray-500 mt-0.5"><?php echo htmlspecialchars($q['email']); ?></div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-blue-700">
                                <?php echo htmlspecialchars($q['service'] ?? '—'); ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <?php $s = $statusConfig[strtolower($q['status'])] ?? ['class' => 'bg-gray-100 text-gray-700', 'label' => ucfirst($q['status'])]; ?>
                                <span class="badge <?php echo $s['class']; ?>"><?php echo $s['label']; ?></span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <?php $p = $priorityConfig[strtolower($q['priority'])] ?? ['class' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400', 'label' => ucfirst($q['priority'])]; ?>
                                <span class="badge <?php echo $p['class']; ?>"><span class="priority-dot <?php echo $p['dot']; ?>"></span><?php echo $p['label']; ?></span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($q['created_at'])); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <button onclick="openQuoteDetailsModal(<?php echo $q['id']; ?>)" class="text-blue-600 hover:text-blue-800 font-medium mr-3">View</button>
                                <button onclick="openDeleteConfirmation(<?php echo $q['id']; ?>)" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($quotes)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="mt-2 text-sm font-medium text-gray-900">No Quote Requests Found</p>
                                <p class="mt-1 text-sm text-gray-500">Adjust your filters or wait for new client requests.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="card-mobile space-y-4">
                <?php foreach ($quotes as $q): ?>
                <div class="bg-white rounded-xl shadow p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="font-semibold text-gray-900">#<?php echo $q['id']; ?></div>
                        <div class="flex gap-3">
                            <button onclick="openQuoteDetailsModal(<?php echo $q['id']; ?>)" class="text-sm text-blue-600 hover:underline font-medium">View</button>
                            <button onclick="openDeleteConfirmation(<?php echo $q['id']; ?>)" class="text-sm text-red-600 hover:underline font-medium">Delete</button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div><div class="font-medium text-gray-900"><?php echo htmlspecialchars($q['company_name']); ?></div><div class="text-sm text-gray-500"><?php echo htmlspecialchars($q['delivery_location']); ?></div></div>
                        <div class="text-sm"><div class="text-gray-900"><?php echo htmlspecialchars($q['contact_person']); ?></div><div class="text-gray-500"><?php echo htmlspecialchars($q['email']); ?></div></div>
                        <div class="text-sm font-medium text-blue-700"><?php echo htmlspecialchars($q['service'] ?? '—'); ?></div>
                        <div class="flex flex-wrap gap-2 pt-2">
                            <?php $s = $statusConfig[strtolower($q['status'])] ?? ['class' => 'bg-gray-100 text-gray-700', 'label' => ucfirst($q['status'])]; ?>
                            <span class="badge <?php echo $s['class']; ?>"><?php echo $s['label']; ?></span>
                            <?php $p = $priorityConfig[strtolower($q['priority'])] ?? ['class' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400', 'label' => ucfirst($q['priority'])]; ?>
                            <span class="badge <?php echo $p['class']; ?>"><span class="priority-dot <?php echo $p['dot']; ?>"></span><?php echo $p['label']; ?></span>
                        </div>
                        <div class="text-xs text-gray-500 pt-1"><?php echo date('M d, Y', strtotime($q['created_at'])); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($quotes)): ?>
                <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="mt-2">No Quote Requests Found</p>
                </div>
                <?php endif; ?>
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200 p-4 text-center text-xs sm:text-sm text-gray-500 mt-auto">
            &copy; <?php echo date('Y'); ?> Power Giant RMT. All rights reserved.
        </footer>
    </div>
</div>

<!-- Modals -->
<div id="quoteModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Quote Details</h2>
            <button onclick="closeModal('quoteModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modalBody" class="p-6">
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        </div>
    </div>
</div>

<div id="deleteConfirmationModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="bg-red-600 text-white px-6 py-4 flex justify-between items-center rounded-t-2xl">
            <h2 class="text-xl font-bold">Confirm Deletion</h2>
            <button onclick="closeModal('deleteConfirmationModal')" class="text-white hover:text-red-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-lg font-semibold text-gray-800 mb-2">Are you sure?</p>
            <p class="text-sm text-gray-600">This will permanently delete Quote <span id="deleteQuoteIdDisplay" class="font-bold text-red-700"></span>. This cannot be undone.</p>
            <form id="deleteForm" method="POST" class="mt-6 flex justify-end gap-3">
                <input type="hidden" name="delete_quote" value="1">
                <input type="hidden" id="quoteIdToDelete" name="quote_id">
                <button type="button" onclick="closeModal('deleteConfirmationModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('show');
        document.body.style.overflow = sidebar.classList.contains('-translate-x-full') ? 'auto' : 'hidden';
    }

    // Toggle Page Management Dropdown
    function togglePageManagement() {
        const dropdown = document.getElementById('pageManagementDropdown');
        const caret = document.getElementById('pageMgmtCaret');
        dropdown.classList.toggle('max-h-0');
        dropdown.classList.toggle('max-h-96');
        caret.classList.toggle('rotate-180');
    }

    // Toggle User Dropdown
    function toggleUserDropdown() {
        document.getElementById('userDropdown').classList.toggle('show');
    }

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('userDropdown');
        const button = e.target.closest('button[onclick="toggleUserDropdown()"]');
        if (!button && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    });

    // Settings Modal
    function openSettingsModal() {
        const modal = document.getElementById('settingsModal');
        const overlay = document.getElementById('modalOverlay');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.add('show'); overlay.classList.add('show'); }, 10);
        document.body.style.overflow = 'hidden';
    }
    function closeSettingsModal() {
        const modal = document.getElementById('settingsModal');
        const overlay = document.getElementById('modalOverlay');
        modal.classList.remove('show');
        overlay.classList.remove('show');
        setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
        resetPasswordForm();
    }

    // Toggle Password
    function togglePassword(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(eyeId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 10 5c2.478 0 6.268 2.943 7.542 7-1.274 4.057-5.064 7-7.542 7-2.478 0-6.268-2.943-7.542-7z"/>`;
        }
    }

    function resetPasswordForm() {
        document.getElementById('changePasswordForm').reset();
        const msg = document.getElementById('passwordMessage');
        msg.classList.add('hidden'); msg.innerHTML = '';
        document.getElementById('submitSpinner').classList.add('hidden');
        document.getElementById('submitText').textContent = 'Change Password';
        document.getElementById('submitPassword').disabled = false;
    }

    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const msg = document.getElementById('passwordMessage');
        const btn = document.getElementById('submitPassword');
        const text = document.getElementById('submitText');
        const spinner = document.getElementById('submitSpinner');
        const newPass = document.getElementById('new_password').value;
        const confirm = document.getElementById('confirm_password').value;

        msg.classList.add('hidden');
        if (newPass !== confirm) { msg.classList.remove('hidden'); msg.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200'); msg.innerHTML = 'Passwords do not match.'; return; }
        if (newPass.length < 6) { msg.classList.remove('hidden'); msg.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200'); msg.innerHTML = 'Password must be at least 6 characters.'; return; }

        btn.disabled = true; text.textContent = 'Changing...'; spinner.classList.remove('hidden');
        try {
            const formData = new FormData(this);
            const res = await fetch(this.action, { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                msg.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'border-red-200');
                msg.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
                msg.innerHTML = data.message;
                setTimeout(closeSettingsModal, 2000);
            } else throw new Error(data.message);
        } catch (err) {
            msg.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'border-green-200');
            msg.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
            msg.innerHTML = err.message || 'Error occurred.';
        } finally {
            btn.disabled = false; text.textContent = 'Change Password'; spinner.classList.add('hidden');
        }
    });

    // Modals
    function openQuoteDetailsModal(id) {
        const modal = document.getElementById('quoteModal');
        const body = document.getElementById('modalBody');
        modal.classList.remove('hidden');
        body.innerHTML = `<div class="flex justify-center items-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div></div>`;
        fetch('view_quote.php?id=' + id).then(r => r.text()).then(html => body.innerHTML = html).catch(() => body.innerHTML = `<p class="text-center text-red-600">Failed to load.</p>`);
    }
    function openDeleteConfirmation(id) {
        document.getElementById('quoteIdToDelete').value = id;
        document.getElementById('deleteQuoteIdDisplay').textContent = '#' + id;
        document.getElementById('deleteConfirmationModal').classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Auto-hide toasts
    document.addEventListener('DOMContentLoaded', () => {
        ['successMessage', 'errorMessage'].forEach(id => {
            const el = document.getElementById(id);
            if (el) setTimeout(() => { el.classList.add('toast-fade-out'); setTimeout(() => el.remove(), 500); }, 4000);
        });
    });
</script>
</body>
</html>