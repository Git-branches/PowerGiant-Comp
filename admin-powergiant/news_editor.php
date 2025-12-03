<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../config/auth.php';
requireLogin();

try {
    $db = (new Database())->connect();
} catch (Exception $e) {
    error_log('DB Error: ' . $e->getMessage());
    die('<div class="bg-red-100 text-red-800 p-4 rounded-lg">Database connection failed.</div>');
}

/* ────────────────────── POST HANDLERS ────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD
    if (isset($_POST['add_news'])) {
        $title     = trim($_POST['news_title']);
        $content   = trim($_POST['news_content']);
        $excerpt   = trim($_POST['news_excerpt']);
        $author    = trim($_POST['author']);
        $date      = $_POST['publish_date'];
        $order     = (int)$_POST['display_order'];
        $published = isset($_POST['is_published']) ? 1 : 0;

        $image_path = null;
        if (!empty($_FILES['news_image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['news_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['news_image']['size'] <= MAX_FILE_SIZE) {
                $dir = UPLOAD_DIR . 'news/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $file = 'news_' . uniqid() . '.' . $ext;
                $target = $dir . $file;
                if (move_uploaded_file($_FILES['news_image']['tmp_name'], $target)) {
                    // FIXED: Store path as "news/filename.jpg" instead of "uploads/news/filename.jpg"
                    $image_path = 'news/' . $file;
                }
            }
        }

        try {
            $stmt = $db->prepare("INSERT INTO news_content 
                (news_title, news_content, news_excerpt, image_path, author, publish_date, is_published, display_order) 
                VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $content, $excerpt, $image_path, $author, $date, $published, $order]);
            $_SESSION['success_msg'] = "News article added!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Add failed: " . $e->getMessage();
        }
        header("Location: news_editor.php"); exit();
    }

    // UPDATE
    if (isset($_POST['update_news'])) {
        $id        = (int)$_POST['news_id'];
        $title     = trim($_POST['news_title']);
        $content   = trim($_POST['news_content']);
        $excerpt   = trim($_POST['news_excerpt']);
        $author    = trim($_POST['author']);
        $date      = $_POST['publish_date'];
        $order     = (int)$_POST['display_order'];
        $published = isset($_POST['is_published']) ? 1 : 0;
        $current   = $_POST['current_image'];

        $image_path = $current;
        if (!empty($_FILES['news_image']['name'])) {
            // FIXED: Delete old file with correct path
            if ($current && file_exists('../uploads/' . $current)) @unlink('../uploads/' . $current);
            $ext = strtolower(pathinfo($_FILES['news_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['news_image']['size'] <= MAX_FILE_SIZE) {
                $dir = UPLOAD_DIR . 'news/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $file = 'news_' . uniqid() . '.' . $ext;
                $target = $dir . $file;
                if (move_uploaded_file($_FILES['news_image']['tmp_name'], $target)) {
                    // FIXED: Store path as "news/filename.jpg" instead of "uploads/news/filename.jpg"
                    $image_path = 'news/' . $file;
                }
            }
        }

        try {
            $stmt = $db->prepare("UPDATE news_content SET 
                news_title=?, news_content=?, news_excerpt=?, image_path=?, author=?, publish_date=?, is_published=?, display_order=? 
                WHERE id=?");
            $stmt->execute([$title, $content, $excerpt, $image_path, $author, $date, $published, $order, $id]);
            $_SESSION['success_msg'] = "News article updated!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Update failed: " . $e->getMessage();
        }
        header("Location: news_editor.php"); exit();
    }

    // DELETE
    if (isset($_POST['delete_news'])) {
        $id = (int)$_POST['news_id'];
        try {
            $stmt = $db->prepare("SELECT image_path FROM news_content WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            // FIXED: Delete file with correct path
            if ($row && $row['image_path'] && file_exists('../uploads/' . $row['image_path'])) {
                @unlink('../uploads/' . $row['image_path']);
            }
            $stmt = $db->prepare("DELETE FROM news_content WHERE id=?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "News article deleted!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Delete failed: " . $e->getMessage();
        }
        header("Location: news_editor.php"); exit();
    }

    // REORDER
    if (isset($_POST['update_order'])) {
        $order = json_decode($_POST['order'], true);
        if (is_array($order)) {
            $stmt = $db->prepare("UPDATE news_content SET display_order=? WHERE id=?");
            foreach ($order as $i => $id) {
                $stmt->execute([$i + 1, $id]);
            }
        }
        exit(json_encode(['success' => true]));
    }
}

/* ────────────────────── FETCH ────────────────────── */
try {
    $news_articles = $db->query("SELECT * FROM news_content ORDER BY display_order, publish_date DESC, id")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error_msg'] = "Error loading news: " . $e->getMessage();
    $news_articles = [];
}

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

$admin_name = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Editor - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="POWER-GIANT.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body { font-family: 'Inter', sans-serif; }

        #sidebarOverlay, #modalOverlay, #deleteModalOverlay, #settingsModalOverlay { 
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:30; transition:opacity .3s; 
        }
        #sidebarOverlay.show, #modalOverlay.show, #deleteModalOverlay.show, #settingsModalOverlay.show { 
            display:block; animation:fadeIn .3s; 
        }

        #sidebar { transition:transform .3s ease-in-out; }
        #editModal, #deleteModal, #settingsModal { 
            transition:all .3s; transform:scale(.95); opacity:0; 
        }
        #editModal.show, #deleteModal.show, #settingsModal.show { 
            transform:scale(1); opacity:1; 
        }

        @keyframes fadeIn { from{opacity:0} to{opacity:1} }

        .drag-handle { cursor:move; }
        .dragging { opacity:.5; background:#dbeafe; }

        .toast-notification {
            position:fixed; top:20px; left:50%; transform:translateX(-50%);
            z-index:99999; min-width:320px; max-width:500px;
            animation:slideDown .4s ease-out;
            box-shadow:0 10px 40px rgba(0,0,0,.2);
        }
        .toast-fade-out { animation:fadeOut .5s ease-out forwards; }
        @keyframes slideDown { from{opacity:0;transform:translateX(-50%) translateY(-100%)} to{opacity:1;transform:translateX(-50%) translateY(0)} }
        @keyframes fadeOut { to{opacity:0;transform:translateX(-50%) translateY(-20px)} }

        @media (max-width: 768px) {
            .mobile-stack { display: flex; flex-direction: column; }
            .mobile-full { width: 100%; }
            .mobile-space-y-4 > * + * { margin-top: 1rem; }
            .mobile-p-4 { padding: 1rem; }
            .mobile-text-sm { font-size: 0.875rem; }
            .mobile-card-height { min-height: auto; height: auto; }
            .mobile-flex-col { flex-direction: column; }
            .mobile-items-start { align-items: flex-start; }
            .mobile-text-xs { font-size: 0.75rem; }
            .mobile-p-3 { padding: 0.75rem; }
            .mobile-min-w-11 { min-width: 2.75rem; }
            .mobile-min-h-11 { min-height: 2.75rem; }
            .mobile-gap-1 { gap: 0.25rem; }
            .mobile-mt-1 { margin-top: 0.25rem; }
            .mobile-line-clamp-2 { 
                display: -webkit-box; 
                -webkit-line-clamp: 2; 
                -webkit-box-orient: vertical; 
                overflow: hidden; 
            }
            .mobile-justify-end { justify-content: flex-end; }
            .mobile-w-full { width: 100%; }
        }

        .cards-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .cards-container {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
                align-items: start;
            }
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Overlays -->
    <div id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div id="modalOverlay" onclick="closeEditModal()"></div>
    <div id="deleteModalOverlay" onclick="closeDeleteModal()"></div>
    <div id="settingsModalOverlay" onclick="closeSettingsModal()"></div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Delete News</h3>
                <p class="text-gray-600 mb-6">This action cannot be undone.</p>
                <div class="flex space-x-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <form method="POST" id="deleteForm" class="flex-1">
                        <input type="hidden" name="news_id" id="delete_news_id">
                        <button name="delete_news" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Edit News Article</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="news_id" id="edit_id">
                    <input type="hidden" name="current_image" id="edit_current_image">

                    <div class="space-y-3">
                        <input type="text" name="news_title" id="edit_title" required placeholder="Title" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <textarea name="news_excerpt" id="edit_excerpt" rows="2" required placeholder="Excerpt" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                        <textarea name="news_content" id="edit_content" rows="5" required placeholder="Full Content" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                        <input type="text" name="author" id="edit_author" required placeholder="Author" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <input type="date" name="publish_date" id="edit_date" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <input type="number" name="display_order" id="edit_order" required placeholder="Display Order" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_published" id="edit_published" class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Published</span>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Image</label>
                            <div id="currentImagePreview" class="mb-2"></div>
                            <input type="file" name="news_image" accept="image/*" class="w-full px-3 py-2 border rounded-lg file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:bg-blue-50 file:text-blue-700 text-sm">
                        </div>

                        <div class="md:col-span-2 flex space-x-3 pt-2 mobile:flex-col mobile:space-y-3 mobile:space-x-0">
                            <button type="button" onclick="closeEditModal()" 
                                    class="flex-1 h-11 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition text-sm flex items-center justify-center mobile:w-full">
                                Cancel
                            </button>
                            <button name="update_news" 
                                    class="flex-1 h-11 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm flex items-center justify-center mobile:w-full">
                                Update News
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                            <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('current_password','current_eye')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
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
                            <input type="password" id="new_password" name="new_password" required minlength="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('new_password','new_eye')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
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
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('confirm_password','confirm_eye')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg id="confirm_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 10 5c2.478 0 6.268 2.943 7.542 7-1.274 4.057-5.064 7-7.542 7-2.478 0-6.268-2.943-7.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="passwordMessage" class="hidden p-3 rounded-lg text-sm"></div>
                    <div class="flex flex-col md:flex-row gap-3 pt-4">
                        <button type="button" onclick="closeSettingsModal()" class="w-full md:w-auto px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="submitPassword" class="w-full md:w-auto px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center justify-center">
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
                        <button onclick="toggleSidebar()" class="lg:hidden text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">News Management</h1>
                    </div>

                    <!-- USER DROPDOWN -->
                    <div class="relative">
                        <button onclick="toggleUserDropdown()" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition">
                            <span class="hidden sm:inline text-sm md:text-base font-medium">Welcome, <span class="text-blue-600 font-semibold"><?= $admin_name ?></span></span>
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

        <!-- Toast Messages -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <?php if ($success_msg): ?>
                <div id="toast-success" class="toast-notification bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-xl flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-1"><p class="font-semibold">Success!</p><p class="text-sm"><?= htmlspecialchars($success_msg) ?></p></div>
                    <button onclick="this.parentElement.remove()" class="ml-3 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div id="toast-error" class="toast-notification bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-xl flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div class="flex-1"><p class="font-semibold">Error!</p><p class="text-sm"><?= htmlspecialchars($error_msg) ?></p></div>
                    <button onclick="this.parentElement.remove()" class="ml-3 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <?php endif; ?>

            <!-- CARDS -->
            <div class="cards-container max-w-7xl mx-auto">
                <!-- Add Form -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">Add News Article</h3>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="text" name="news_title" placeholder="Title" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <textarea name="news_excerpt" placeholder="Excerpt" rows="2" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        <textarea name="news_content" placeholder="Full Content" rows="5" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        <input type="text" name="author" placeholder="Author" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <input type="date" name="publish_date" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <input type="file" name="news_image" accept="image/*" class="w-full px-4 py-3 border rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-blue-50 file:text-blue-700">
                        <input type="number" name="display_order" value="0" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_published" checked class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Published</span>
                        </label>
                        <button name="add_news" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">Add News</button>
                    </form>
                </div>

                <!-- List -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">News Articles</h3>
                    <div id="newsList" class="space-y-3">
                        <?php foreach ($news_articles as $n): ?>
                        <div data-id="<?= $n['id'] ?>" class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md drag-handle group mobile-p-3 mobile-flex-col mobile-items-start">
                            <div class="flex items-center gap-3 flex-1 min-w-0 mobile-w-full">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0 cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                <?php if ($n['image_path']): ?>
                                    <!-- FIXED: Display image with correct path -->
                                    <img src="../uploads/<?= htmlspecialchars($n['image_path']) ?>" class="w-12 h-12 object-cover rounded-lg flex-shrink-0" alt="">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-800 truncate mobile-text-xs"><?= htmlspecialchars($n['news_title']) ?></h4>
                                    <p class="text-xs text-gray-600 mobile-line-clamp-2">
                                        <?= htmlspecialchars($n['author']) ?> • <?= date('M d, Y', strtotime($n['publish_date'])) ?>
                                        <span class="ml-2 px-2 py-1 rounded-full text-xs <?= $n['is_published'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                            <?= $n['is_published'] ? 'Published' : 'Draft' ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 mobile-gap-1 mobile-mt-1 mobile-justify-end mobile-w-full">
                                <button onclick='editNews(<?= json_encode($n, JSON_HEX_QUOT | JSON_HEX_APOS) ?>)' class="p-2.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition mobile-min-w-11 mobile-min-h-11">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick='openDeleteModal(<?= $n['id'] ?>)' class="p-2.5 text-red-600 hover:bg-red-50 rounded-lg transition mobile-min-w-11 mobile-min-h-11">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($news_articles)): ?>
                        <p class="text-center text-gray-500 py-8">No news articles yet. Add one to get started!</p>
                        <?php endif; ?>
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
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebarOverlay');
    sb.classList.toggle('-translate-x-full');
    ov.classList.toggle('show');
    document.body.style.overflow = sb.classList.contains('-translate-x-full') ? 'auto' : 'hidden';
}

function togglePageManagement() {
    const d = document.getElementById('pageManagementDropdown');
    const c = document.getElementById('pageMgmtCaret');
    d.classList.toggle('max-h-0'); d.classList.toggle('max-h-96');
    c.classList.toggle('rotate-180');
}

function toggleUserDropdown() {
    document.getElementById('userDropdown').classList.toggle('hidden');
}
document.addEventListener('click', e => {
    const dd = document.getElementById('userDropdown');
    if (!e.target.closest('button[onclick="toggleUserDropdown()"]') && !dd.classList.contains('hidden')) {
        dd.classList.add('hidden');
    }
});

function editNews(n) {
    document.getElementById('edit_id').value = n.id;
    document.getElementById('edit_title').value = n.news_title;
    document.getElementById('edit_excerpt').value = n.news_excerpt;
    document.getElementById('edit_content').value = n.news_content;
    document.getElementById('edit_author').value = n.author;
    document.getElementById('edit_date').value = n.publish_date;
    document.getElementById('edit_order').value = n.display_order;
    document.getElementById('edit_published').checked = n.is_published == 1;
    document.getElementById('edit_current_image').value = n.image_path || '';

    const prev = document.getElementById('currentImagePreview');
    // FIXED: Display image with correct path
    prev.innerHTML = n.image_path 
        ? `<img src="../uploads/${n.image_path}" class="w-20 h-20 object-cover rounded-lg">` 
        : '<p class="text-gray-500 text-sm">No image</p>';

    const modal = document.getElementById('editModal');
    const overlay = document.getElementById('modalOverlay');
    modal.classList.remove('hidden');
    setTimeout(() => { modal.classList.add('show'); overlay.classList.add('show'); }, 10);
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    const overlay = document.getElementById('modalOverlay');
    modal.classList.remove('show'); overlay.classList.remove('show');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
}

function openDeleteModal(id) {
    document.getElementById('delete_news_id').value = id;
    const modal = document.getElementById('deleteModal');
    const overlay = document.getElementById('deleteModalOverlay');
    modal.classList.remove('hidden');
    setTimeout(() => { modal.classList.add('show'); overlay.classList.add('show'); }, 10);
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const overlay = document.getElementById('deleteModalOverlay');
    modal.classList.remove('show'); overlay.classList.remove('show');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
}

function openSettingsModal() {
    const modal = document.getElementById('settingsModal');
    const overlay = document.getElementById('settingsModalOverlay');
    modal.classList.remove('hidden');
    setTimeout(() => { modal.classList.add('show'); overlay.classList.add('show'); }, 10);
    document.body.style.overflow = 'hidden';
}

function closeSettingsModal() {
    const modal = document.getElementById('settingsModal');
    const overlay = document.getElementById('settingsModalOverlay');
    modal.classList.remove('show'); overlay.classList.remove('show');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
}

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

/* Drag Reorder */
let dragged;
document.querySelectorAll('.drag-handle').forEach(el => {
    el.draggable = true;
    el.addEventListener('dragstart', () => { dragged = el; el.classList.add('dragging'); });
    el.addEventListener('dragend', () => { el.classList.remove('dragging'); saveOrder(); });
    el.addEventListener('dragover', e => e.preventDefault());
    el.addEventListener('drop', e => {
        e.preventDefault();
        if (dragged !== el) {
            const all = Array.from(document.querySelectorAll('.drag-handle'));
            const from = all.indexOf(dragged), to = all.indexOf(el);
            if (from < to) el.after(dragged); else el.before(dragged);
        }
    });
});
function saveOrder() {
    const order = Array.from(document.querySelectorAll('.drag-handle')).map(el => el.dataset.id);
    fetch('', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'update_order=1&order=' + JSON.stringify(order) });
}

// Toast auto-hide
document.addEventListener('DOMContentLoaded', () => {
    ['toast-success', 'toast-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) setTimeout(() => { el.classList.add('toast-fade-out'); setTimeout(() => el.remove(), 500); }, 4000);
    });
});
</script>
</body>
</html>