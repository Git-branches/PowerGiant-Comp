<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../config/auth.php';
requirePermission('view_dashboard');

$stats = [
    'total' => 0,
    'new_count' => 0,
    'in_progress_count' => 0,
    'quoted_count' => 0,
    'completed_count' => 0
];

try {
    $database = new Database();
    $db = $database->connect();

    $stats_sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                    SUM(CASE WHEN status = 'quoted' THEN 1 ELSE 0 END) as quoted_count,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
                  FROM quote_requests";
                  
    $stats_stmt = $db->query($stats_sql);
    $stats_raw = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    if ($stats_raw) $stats = array_merge($stats, $stats_raw);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
}

$total_quotes = $stats['total'];
$completed_quotes = $stats['completed_count'];
$success_rate = $total_quotes > 0 ? round(($completed_quotes / $total_quotes) * 100, 1) : 0;

$admin_name = $_SESSION['admin_name'] ?? 'Admin User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quote Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="POWER-GIANT.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        #sidebarOverlay, #modalOverlay, #userDropdown { display: none; }
        #sidebarOverlay.show, #modalOverlay.show, #userDropdown.show { display: block; }
        #sidebar { transition: transform 0.3s ease-in-out; }
        #settingsModal { transition: all 0.3s ease-in-out; transform: scale(0.9); opacity: 0; }
        #settingsModal.show { transform: scale(1); opacity: 1; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .password-toggle:hover { color: #3b82f6; }
        .dropdown-content { transition: max-height 0.3s ease, padding 0.3s ease; overflow: hidden; }
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

    <!-- Sidebar (EXACTLY LIKE about_editor.php) -->
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
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Analytics Dashboard</h1>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button onclick="toggleUserDropdown()" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition">
                            <span class="hidden sm:inline text-sm md:text-base font-medium">Welcome, <span class="text-blue-600 font-semibold"><?= htmlspecialchars($admin_name) ?></span></span>
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

            <!-- STATISTICS CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <?php
                $cards = [
                    ['label' => 'Total Quotes',     'count' => $stats['total'],               'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2m-6 0h6', 'bg' => 'bg-blue-600', 'text' => 'text-blue-700'],
                    ['label' => 'New Requests',    'count' => $stats['new_count'],           'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.503-1.685 1.745-3.084L13.14 4.54a2 2 0 00-3.48 0L3.338 15.916c-.758 1.399.205 3.084 1.745 3.084z', 'bg' => 'bg-orange-500', 'text' => 'text-orange-700'],
                    ['label' => 'In Progress',     'count' => $stats['in_progress_count'],   'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'bg' => 'bg-yellow-500', 'text' => 'text-yellow-700'],
                    ['label' => 'Quoted',          'count' => $stats['quoted_count'],        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'bg' => 'bg-purple-600', 'text' => 'text-purple-700'],
                    ['label' => 'Completed',       'count' => $stats['completed_count'],     'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-green-600', 'text' => 'text-green-700'],
                ];

                foreach ($cards as $card):
                ?>
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-5 border-l-4 <?= str_replace('bg-', 'border-', $card['bg']) ?> flex items-center space-x-4 h-28">
                    <div class="flex-shrink-0 w-14 h-14 <?= $card['bg'] ?> rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $card['icon'] ?>"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider truncate"><?= $card['label'] ?></p>
                        <p class="text-2xl font-bold <?= $card['text'] ?> mt-1"><?= $card['count'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CHARTS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Quote Status Distribution</h3>
                    <div class="h-96">
                        <canvas id="statusDistributionChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8 flex flex-col justify-between">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Quote Success Rate (KPI)</h3>
                    <div class="relative w-full" style="aspect-ratio: 2 / 1; min-height: 150px;"> 
                        <canvas id="successRateGauge" class="w-full h-full"></canvas>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h4 class="text-lg font-semibold mb-2 flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.276a11.052 11.052 0 010 12.552c-.783 2.37-2.316 4.385-4.385 5.567-2.069 1.182-4.498 1.773-6.927 1.773s-4.858-.591-6.927-1.773c-2.069-1.182-3.602-3.197-4.385-5.567a11.052 11.052 0 010-12.552c.783-2.37 2.316-4.385 4.385-5.567 2.069-1.182 4.498-1.773 6.927-1.773s4.858.591 6.927 1.773c2.069 1.182 3.602 3.197 4.385 5.567z"></path>
                            </svg>
                            KPI Definition
                        </h4>
                        <p class="text-sm text-gray-600">
                            Quote Success Rate = (Completed Quotes / Total Quotes) × 100%
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200 p-4 text-center text-sm text-gray-500 mt-auto">
            © <?= date('Y') ?> Power Giant RMT. All rights reserved.
        </footer>
    </div>
</div>

<script>
    // ==================== ALL YOUR EXISTING FUNCTIONS (Keep them!) ====================
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('show');
        document.body.style.overflow = sidebar.classList.contains('-translate-x-full') ? 'auto' : 'hidden';
    }

    function togglePageManagement() {
        const dropdown = document.getElementById('pageManagementDropdown');
        const caret = document.getElementById('pageMgmtCaret');
        dropdown.classList.toggle('max-h-0');
        dropdown.classList.toggle('max-h-96');
        caret.classList.toggle('rotate-180');
    }

    function toggleUserDropdown() {
        document.getElementById('userDropdown').classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('userDropdown');
        const button = e.target.closest('button[onclick="toggleUserDropdown()"]');
        if (!button && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    });

    // Settings Modal Functions
    function openSettingsModal() {
        const modal = document.getElementById('settingsModal');
        const overlay = document.getElementById('modalOverlay');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('show');
            overlay.classList.add('show');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeSettingsModal() {
        const modal = document.getElementById('settingsModal');
        const overlay = document.getElementById('modalOverlay');
        modal.classList.remove('show');
        overlay.classList.remove('show');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
        resetPasswordForm();
    }

    // Toggle Password Visibility
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

    // Reset form after close
    function resetPasswordForm() {
        document.getElementById('changePasswordForm').reset();
        const msg = document.getElementById('passwordMessage');
        msg.classList.add('hidden');
        msg.classList.remove('bg-green-50', 'text-green-800', 'border-green-300', 'bg-red-50', 'text-red-800', 'border-red-300');
        document.getElementById('submitSpinner').classList.add('hidden');
        document.getElementById('submitText').textContent = 'Change Password';
        document.getElementById('submitPassword').disabled = false;
    }

    // ==================== FIXED CHANGE PASSWORD WITH POPUP MESSAGES ====================
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const msgBox     = document.getElementById('passwordMessage');
        const submitBtn  = document.getElementById('submitPassword');
        const submitText = document.getElementById('submitText');
        const spinner    = document.getElementById('submitSpinner');

        // Reset message box
        msgBox.classList.add('hidden');
        msgBox.classList.remove('bg-green-50', 'text-green-800', 'border-green-300', 'bg-red-50', 'text-red-800', 'border-red-300');

        const currentPass = document.getElementById('current_password').value.trim();
        const newPass     = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;

        // Client-side validation
        if (!currentPass || !newPass || !confirmPass) {
            msgBox.classList.remove('hidden');
            msgBox.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-300');
            msgBox.innerHTML = 'All fields are required.';
            return;
        }

        if (newPass !== confirmPass) {
            msgBox.classList.remove('hidden');
            msgBox.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-300');
            msgBox.innerHTML = 'New password and confirmation do not match.';
            return;
        }

        if (newPass.length < 6) {
            msgBox.classList.remove('hidden');
            msgBox.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-300');
            msgBox.innerHTML = 'New password must be at least 6 characters.';
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Changing...';
        spinner.classList.remove('hidden');

        try {
            const formData = new FormData(this);
            const response = await fetch('change_password.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // SUCCESS MESSAGE (Green)
                msgBox.classList.remove('hidden');
                msgBox.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-300');
                msgBox.innerHTML = '<strong>Success!</strong> ' + data.message;

                // Auto close modal after 2 seconds
                setTimeout(() => {
                    closeSettingsModal();
                }, 2000);

            } else {
                // ERROR FROM SERVER (Red)
                msgBox.classList.remove('hidden');
                msgBox.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-300');
                msgBox.innerHTML = '<strong>Error:</strong> ' + (data.message || 'Unknown error.');
            }

        } catch (error) {
            console.error('Password change error:', error);
            msgBox.classList.remove('hidden');
            msgBox.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-300');
            msgBox.innerHTML = '<strong>Error:</strong> Network or server error. Please try again.';
        } finally {
            // Reset button
            submitBtn.disabled = false;
            submitText.textContent = 'Change Password';
            spinner.classList.add('hidden');
        }
    });

    // ==================== CHART.JS CODES (Keep your existing charts) ====================
    const successRate = <?= $success_rate ?>;
    const statsData = {
        new: <?= $stats['new_count'] ?>,
        in_progress: <?= $stats['in_progress_count'] ?>,
        quoted: <?= $stats['quoted_count'] ?>,
        completed: <?= $stats['completed_count'] ?>
    };

    const gaugeTextPlugin = {
        id: 'gaugeText',
        beforeDatasetsDraw(chart) {
            const { ctx } = chart;
            ctx.save();
            const xCenter = (chart.chartArea.left + chart.chartArea.right) / 2;
            const yCenter = chart.chartArea.bottom - 20;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = '700 3.5rem Inter';
            ctx.fillStyle = '#1e40af';
            ctx.fillText(`${successRate.toFixed(1)}%`, xCenter, yCenter);
            ctx.font = '400 1rem Inter';
            ctx.fillStyle = '#6b7280';
            ctx.fillText('Success Rate', xCenter, yCenter + 40);
            ctx.restore();
        }
    };

    const distributionConfig = {
        type: 'doughnut',
        data: {
            labels: ['New Requests', 'In Progress', 'Quoted', 'Completed'],
            datasets: [{
                data: [statsData.new, statsData.in_progress, statsData.quoted, statsData.completed],
                backgroundColor: ['#f97316', '#facc15', '#a855f7', '#22c55e'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { font: { family: 'Inter', size: 14 }, padding: 20 } }
            }
        }
    };

    const gaugeConfig = {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [successRate, 100 - successRate],
                backgroundColor: ['#2563eb', '#e5e7eb'],
                borderWidth: 0,
                cutout: '80%',
                circumference: 180,
                rotation: -90
            }]
        },
        plugins: [gaugeTextPlugin],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        }
    };

    // Initialize charts when page loads
    window.onload = function() {
        new Chart(document.getElementById('statusDistributionChart'), distributionConfig);
        new Chart(document.getElementById('successRateGauge'), gaugeConfig);
    };
</script>
</body>
</html>