<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

try {
    $database = new Database();
    $db = $database->connect();
} catch (Exception $e) {
    error_log('DB Error: ' . $e->getMessage());
    die('<div class="bg-red-100 text-red-800 p-4 rounded-lg">Database connection failed.</div>');
}

/* ────────────────────── POST HANDLERS ────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD HERO SECTION
    if (isset($_POST['add_hero'])) {
        $title = trim($_POST['hero_title'] ?? '');
        $subtitle = trim($_POST['hero_subtitle'] ?? '');
        $description = trim($_POST['hero_description'] ?? '');
        $primary_btn_text = trim($_POST['primary_button_text'] ?? '');
        $primary_btn_link = trim($_POST['primary_button_link'] ?? '');
        $secondary_btn_text = trim($_POST['secondary_button_text'] ?? '');
        $secondary_btn_link = trim($_POST['secondary_button_link'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Handle background images - FIXED PATH STRUCTURE
        $background_images = [];
        if (!empty($_FILES['background_images']['name'][0])) {
            foreach ($_FILES['background_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['background_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['background_images']['name'][$key], PATHINFO_EXTENSION));
                    if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['background_images']['size'][$key] <= MAX_FILE_SIZE) {
                        $dir = UPLOAD_DIR . 'homepage/hero/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $file = 'hero_' . uniqid() . '.' . $ext;
                        $target = $dir . $file;
                        if (move_uploaded_file($tmp_name, $target)) {
                            // FIXED: Store relative path without "uploads/" prefix
                            $background_images[] = 'homepage/hero/' . $file;
                        }
                    }
                }
            }
        }

        try {
            $stmt = $db->prepare("INSERT INTO homepage_hero 
                (title, subtitle, description, primary_button_text, primary_button_link, secondary_button_text, secondary_button_link, background_images, is_active) 
                VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $subtitle, $description, $primary_btn_text, $primary_btn_link, $secondary_btn_text, $secondary_btn_link, json_encode($background_images), $is_active]);
            $_SESSION['success_msg'] = "Hero section added!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Add failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // UPDATE HERO SECTION
    if (isset($_POST['update_hero'])) {
        $id = (int)$_POST['hero_id'];
        $title = trim($_POST['hero_title'] ?? '');
        $subtitle = trim($_POST['hero_subtitle'] ?? '');
        $description = trim($_POST['hero_description'] ?? '');
        $primary_btn_text = trim($_POST['primary_button_text'] ?? '');
        $primary_btn_link = trim($_POST['primary_button_link'] ?? '');
        $secondary_btn_text = trim($_POST['secondary_button_text'] ?? '');
        $secondary_btn_link = trim($_POST['secondary_button_link'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $current_images = json_decode($_POST['current_images'] ?? '[]', true);

        // Handle new background images - FIXED PATH STRUCTURE
        $background_images = $current_images;
        if (!empty($_FILES['background_images']['name'][0])) {
            if (isset($_POST['replace_images'])) {
                foreach ($current_images as $old_image) {
                    // FIXED: Update path for deletion
                    $full_path = UPLOAD_DIR . $old_image;
                    if (file_exists($full_path)) @unlink($full_path);
                }
                $background_images = [];
            }

            foreach ($_FILES['background_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['background_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['background_images']['name'][$key], PATHINFO_EXTENSION));
                    if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['background_images']['size'][$key] <= MAX_FILE_SIZE) {
                        $dir = UPLOAD_DIR . 'homepage/hero/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $file = 'hero_' . uniqid() . '.' . $ext;
                        $target = $dir . $file;
                        if (move_uploaded_file($tmp_name, $target)) {
                            // FIXED: Store relative path without "uploads/" prefix
                            $background_images[] = 'homepage/hero/' . $file;
                        }
                    }
                }
            }
        }

        try {
            $stmt = $db->prepare("UPDATE homepage_hero SET 
                title=?, subtitle=?, description=?, primary_button_text=?, primary_button_link=?, secondary_button_text=?, secondary_button_link=?, background_images=?, is_active=?, updated_at=CURRENT_TIMESTAMP 
                WHERE id=?");
            $stmt->execute([$title, $subtitle, $description, $primary_btn_text, $primary_btn_link, $secondary_btn_text, $secondary_btn_link, json_encode($background_images), $is_active, $id]);
            $_SESSION['success_msg'] = "Hero section updated!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Update failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // DELETE HERO SECTION
    if (isset($_POST['delete_hero'])) {
        $id = (int)$_POST['hero_id'];
        try {
            $stmt = $db->prepare("SELECT background_images FROM homepage_hero WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row && $row['background_images']) {
                $images = json_decode($row['background_images'], true);
                foreach ($images as $image) {
                    // FIXED: Update path for deletion
                    $full_path = UPLOAD_DIR . $image;
                    if (file_exists($full_path)) @unlink($full_path);
                }
            }
            $stmt = $db->prepare("DELETE FROM homepage_hero WHERE id=?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "Hero section deleted!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Delete failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // ADD STAT
    if (isset($_POST['add_stat'])) {
        $label = trim($_POST['stat_label'] ?? '');
        $value = trim($_POST['stat_value'] ?? '');
        $prefix = trim($_POST['stat_prefix'] ?? '');
        $suffix = trim($_POST['stat_suffix'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        try {
            $stmt = $db->prepare("INSERT INTO homepage_stats 
                (label, value, prefix, suffix, display_order, is_active) 
                VALUES (?,?,?,?,?,?)");
            $stmt->execute([$label, $value, $prefix, $suffix, $display_order, $is_active]);
            $_SESSION['success_msg'] = "Stat added!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Add failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // UPDATE STAT
    if (isset($_POST['update_stat'])) {
        $id = (int)$_POST['stat_id'];
        $label = trim($_POST['stat_label'] ?? '');
        $value = trim($_POST['stat_value'] ?? '');
        $prefix = trim($_POST['stat_prefix'] ?? '');
        $suffix = trim($_POST['stat_suffix'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        try {
            $stmt = $db->prepare("UPDATE homepage_stats SET 
                label=?, value=?, prefix=?, suffix=?, display_order=?, is_active=? 
                WHERE id=?");
            $stmt->execute([$label, $value, $prefix, $suffix, $display_order, $is_active, $id]);
            $_SESSION['success_msg'] = "Stat updated!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Update failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // DELETE STAT
    if (isset($_POST['delete_stat'])) {
        $id = (int)$_POST['stat_id'];
        try {
            $stmt = $db->prepare("DELETE FROM homepage_stats WHERE id=?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "Stat deleted!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Delete failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // ADD TESTIMONIAL
    if (isset($_POST['add_testimonial'])) {
        $author_name = trim($_POST['author_name'] ?? '');
        $author_position = trim($_POST['author_position'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $avatar_path = '';
        if (!empty($_FILES['avatar']['name'])) {
            if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['avatar']['size'] <= MAX_FILE_SIZE) {
                    $dir = UPLOAD_DIR . 'testimonials/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $file = 'testimonial_' . uniqid() . '.' . $ext;
                    $target = $dir . $file;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                        // FIXED: Store relative path without "uploads/" prefix
                        $avatar_path = 'testimonials/' . $file;
                    }
                }
            }
        }

        try {
            $stmt = $db->prepare("INSERT INTO testimonials 
                (author_name, author_position, company, testimonial_text, rating, avatar_path, display_order, is_active) 
                VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$author_name, $author_position, $company, $content, $rating, $avatar_path, $display_order, $is_active]);
            $_SESSION['success_msg'] = "Testimonial added!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Add failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // UPDATE TESTIMONIAL
    if (isset($_POST['update_testimonial'])) {
        $id = (int)$_POST['testimonial_id'];
        $author_name = trim($_POST['author_name'] ?? '');
        $author_position = trim($_POST['author_position'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $avatar_path = $_POST['current_avatar'] ?? '';
        if (!empty($_FILES['avatar']['name'])) {
            if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['avatar']['size'] <= MAX_FILE_SIZE) {
                    // FIXED: Update path for deletion
                    if (!empty($avatar_path)) {
                        $full_path = UPLOAD_DIR . $avatar_path;
                        if (file_exists($full_path)) @unlink($full_path);
                    }
                    $dir = UPLOAD_DIR . 'testimonials/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $file = 'testimonial_' . uniqid() . '.' . $ext;
                    $target = $dir . $file;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                        // FIXED: Store relative path without "uploads/" prefix
                        $avatar_path = 'testimonials/' . $file;
                    }
                }
            }
        }

        try {
            $stmt = $db->prepare("UPDATE testimonials SET 
                author_name=?, author_position=?, company=?, testimonial_text=?, rating=?, avatar_path=?, display_order=?, is_active=?, updated_at=CURRENT_TIMESTAMP 
                WHERE id=?");
            $stmt->execute([$author_name, $author_position, $company, $content, $rating, $avatar_path, $display_order, $is_active, $id]);
            $_SESSION['success_msg'] = "Testimonial updated!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Update failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // DELETE TESTIMONIAL
    if (isset($_POST['delete_testimonial'])) {
        $id = (int)$_POST['testimonial_id'];
        try {
            $stmt = $db->prepare("SELECT avatar_path FROM testimonials WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row && $row['avatar_path']) {
                // FIXED: Update path for deletion
                $full_path = UPLOAD_DIR . $row['avatar_path'];
                if (file_exists($full_path)) @unlink($full_path);
            }
            $stmt = $db->prepare("DELETE FROM testimonials WHERE id=?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "Testimonial deleted!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Delete failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // ADD PARTNER
    if (isset($_POST['add_partner'])) {
        $name = trim($_POST['name'] ?? '');
        $website_url = trim($_POST['website_url'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $logo_path = '';
        if (!empty($_FILES['logo']['name'])) {
            if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['logo']['size'] <= MAX_FILE_SIZE) {
                    $dir = UPLOAD_DIR . 'partners/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $file = 'partner_' . uniqid() . '.' . $ext;
                    $target = $dir . $file;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                        // FIXED: Store relative path without "uploads/" prefix
                        $logo_path = 'partners/' . $file;
                    }
                }
            }
        }

        try {
            $stmt = $db->prepare("INSERT INTO partners 
                (name, website_url, logo_path, display_order, is_active) 
                VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $website_url, $logo_path, $display_order, $is_active]);
            $_SESSION['success_msg'] = "Partner added!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Add failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // UPDATE PARTNER
    if (isset($_POST['update_partner'])) {
        $id = (int)$_POST['partner_id'];
        $name = trim($_POST['name'] ?? '');
        $website_url = trim($_POST['website_url'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $logo_path = $_POST['current_logo'] ?? '';
        if (!empty($_FILES['logo']['name'])) {
            if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_FILE_TYPES) && $_FILES['logo']['size'] <= MAX_FILE_SIZE) {
                    // FIXED: Update path for deletion
                    if (!empty($logo_path)) {
                        $full_path = UPLOAD_DIR . $logo_path;
                        if (file_exists($full_path)) @unlink($full_path);
                    }
                    $dir = UPLOAD_DIR . 'partners/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $file = 'partner_' . uniqid() . '.' . $ext;
                    $target = $dir . $file;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                        // FIXED: Store relative path without "uploads/" prefix
                        $logo_path = 'partners/' . $file;
                    }
                }
            }
        }

        try {
            $stmt = $db->prepare("UPDATE partners SET 
                name=?, website_url=?, logo_path=?, display_order=?, is_active=? 
                WHERE id=?");
            $stmt->execute([$name, $website_url, $logo_path, $display_order, $is_active, $id]);
            $_SESSION['success_msg'] = "Partner updated!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Update failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // DELETE PARTNER
    if (isset($_POST['delete_partner'])) {
        $id = (int)$_POST['partner_id'];
        try {
            $stmt = $db->prepare("SELECT logo_path FROM partners WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row && $row['logo_path']) {
                // FIXED: Update path for deletion
                $full_path = UPLOAD_DIR . $row['logo_path'];
                if (file_exists($full_path)) @unlink($full_path);
            }
            $stmt = $db->prepare("DELETE FROM partners WHERE id=?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "Partner deleted!";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Delete failed: " . $e->getMessage();
        }
        header("Location: index_editor.php"); exit();
    }

    // REORDER STATS
    if (isset($_POST['update_stat_order'])) {
        $order = json_decode($_POST['order'], true);
        if (is_array($order)) {
            $stmt = $db->prepare("UPDATE homepage_stats SET display_order=? WHERE id=?");
            foreach ($order as $i => $id) {
                $stmt->execute([$i + 1, $id]);
            }
        }
        exit(json_encode(['success' => true]));
    }
}

/* ────────────────────── FETCH DATA ────────────────────── */
try {
    $hero_sections = $db->query("SELECT * FROM homepage_hero ORDER BY id")->fetchAll();
    $stats = $db->query("SELECT * FROM homepage_stats ORDER BY display_order, id")->fetchAll();
    $testimonials = $db->query("SELECT * FROM testimonials ORDER BY display_order")->fetchAll();
    $partners = $db->query("SELECT * FROM partners ORDER BY display_order")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error_msg'] = "Error loading data: " . $e->getMessage();
    $hero_sections = [];
    $stats = [];
    $testimonials = [];
    $partners = [];
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
    <title>Home Page Editor - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="POWER-GIANT.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body { font-family: 'Inter', sans-serif; }

        .overlay { 
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:30; transition:opacity .3s; 
        }
        .overlay.show { 
            display:block; animation:fadeIn .3s; 
        }

        .modal { 
            transition:all .3s; transform:scale(.95); opacity:0; 
        }
        .modal.show { 
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

        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .image-preview {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        .image-preview img {
            width: 100%;
            height: 80px;
            object-fit: cover;
        }
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .mobile-flex-col { flex-direction: column; }
            .mobile-w-full { width: 100%; }
            .mobile-space-y-2 > * + * { margin-top: 0.5rem; }
            .mobile-text-sm { font-size: 0.875rem; }
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

        .star-rating {
            display: flex;
            gap: 2px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            cursor: pointer;
            font-size: 1.5rem;
            color: #d1d5db;
            transition: color 0.2s;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #fbbf24;
        }
        .star-rating input:checked + label {
            color: #f59e0b;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Overlays -->
    <div id="sidebarOverlay" class="overlay" onclick="toggleSidebar()"></div>
    <div id="heroModalOverlay" class="overlay" onclick="closeHeroModal()"></div>
    <div id="statModalOverlay" class="overlay" onclick="closeStatModal()"></div>
    <div id="testimonialModalOverlay" class="overlay" onclick="closeTestimonialModal()"></div>
    <div id="partnerModalOverlay" class="overlay" onclick="closePartnerModal()"></div>
    <div id="deleteModalOverlay" class="overlay" onclick="closeDeleteModal()"></div>
    <div id="settingsModalOverlay" class="overlay" onclick="closeSettingsModal()"></div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2" id="deleteTitle">Delete Item</h3>
                <p class="text-gray-600 mb-6">This action cannot be undone.</p>
                <div class="flex space-x-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <form method="POST" id="deleteForm" class="flex-1">
                        <input type="hidden" name="hero_id" id="delete_hero_id">
                        <input type="hidden" name="stat_id" id="delete_stat_id">
                        <input type="hidden" name="testimonial_id" id="delete_testimonial_id">
                        <input type="hidden" name="partner_id" id="delete_partner_id">
                        <button type="submit" name="delete_hero" id="deleteHeroBtn" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition hidden">Delete Hero</button>
                        <button type="submit" name="delete_stat" id="deleteStatBtn" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition hidden">Delete Stat</button>
                        <button type="submit" name="delete_testimonial" id="deleteTestimonialBtn" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition hidden">Delete Testimonial</button>
                        <button type="submit" name="delete_partner" id="deletePartnerBtn" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition hidden">Delete Partner</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Modal -->
    <div id="heroModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="heroModalTitle">Add Hero Section</h3>
                <button onclick="closeHeroModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="hero_id" id="edit_hero_id">
                    <input type="hidden" name="current_images" id="current_images">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hero Title</label>
                            <input type="text" name="hero_title" id="hero_title" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                            <input type="text" name="hero_subtitle" id="hero_subtitle" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="hero_description" id="hero_description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Primary Button Text</label>
                            <input type="text" name="primary_button_text" id="primary_button_text" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Primary Button Link</label>
                            <input type="text" name="primary_button_link" id="primary_button_link" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Button Text</label>
                            <input type="text" name="secondary_button_text" id="secondary_button_text" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Button Link</label>
                            <input type="text" name="secondary_button_link" id="secondary_button_link" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Background Images</label>
                        <div id="currentImagesPreview" class="image-preview-container mb-2"></div>
                        <input type="file" name="background_images[]" multiple accept="image/*" class="w-full px-3 py-2 border rounded-lg file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:bg-blue-50 file:text-blue-700 text-sm">
                        <div id="replaceImagesOption" class="mt-2 hidden">
                            <label class="flex items-center">
                                <input type="checkbox" name="replace_images" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Replace all existing images</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="hero_is_active" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="hero_is_active" class="ml-2 text-sm text-gray-700">Active</label>
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" onclick="closeHeroModal()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition text-sm">Cancel</button>
                        <button type="submit" name="add_hero" id="addHeroBtn" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm">Add Hero Section</button>
                        <button type="submit" name="update_hero" id="updateHeroBtn" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm hidden">Update Hero Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stat Modal -->
    <div id="statModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="statModalTitle">Add Stat</h3>
                <button onclick="closeStatModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="stat_id" id="edit_stat_id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                        <input type="text" name="stat_label" id="stat_label" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prefix</label>
                            <input type="text" name="stat_prefix" id="stat_prefix" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" placeholder="+">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                            <input type="text" name="stat_value" id="stat_value" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" placeholder="100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                            <input type="text" name="stat_suffix" id="stat_suffix" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" placeholder="%">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="display_order" id="stat_display_order" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" value="0">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="stat_is_active" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="stat_is_active" class="ml-2 text-sm text-gray-700">Active</label>
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" onclick="closeStatModal()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition text-sm">Cancel</button>
                        <button type="submit" name="add_stat" id="addStatBtn" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm">Add Stat</button>
                        <button type="submit" name="update_stat" id="updateStatBtn" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm hidden">Update Stat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Testimonial Modal -->
    <div id="testimonialModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="testimonialModalTitle">Add Testimonial</h3>
                <button onclick="closeTestimonialModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="testimonial_id" id="edit_testimonial_id">
                    <input type="hidden" name="current_avatar" id="current_avatar">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Author Name *</label>
                            <input type="text" name="author_name" id="author_name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Author Position</label>
                            <input type="text" name="author_position" id="author_position" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                            <input type="text" name="company" id="company" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                            <input type="number" name="display_order" id="testimonial_display_order" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" value="0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonial Content *</label>
                        <textarea name="content" id="testimonial_content" rows="4" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating *</label>
                        <div class="star-rating" id="starRating">
                            <input type="radio" id="star5" name="rating" value="5" checked>
                            <label for="star5">Star</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4">Star</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3">Star</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2">Star</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1">Star</label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Author Avatar</label>
                        <div id="currentAvatarPreview" class="mb-2"></div>
                        <input type="file" name="avatar" accept="image/*" class="w-full px-3 py-2 border rounded-lg file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:bg-blue-50 file:text-blue-700 text-sm">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="testimonial_is_active" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="testimonial_is_active" class="ml-2 text-sm text-gray-700">Active</label>
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" onclick="closeTestimonialModal()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition text-sm">Cancel</button>
                        <button type="submit" name="add_testimonial" id="addTestimonialBtn" class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition text-sm">Add Testimonial</button>
                        <button type="submit" name="update_testimonial" id="updateTestimonialBtn" class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition text-sm hidden">Update Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Partner Modal -->
    <div id="partnerModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="partnerModalTitle">Add Partner</h3>
                <button onclick="closePartnerModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="partner_id" id="edit_partner_id">
                    <input type="hidden" name="current_logo" id="current_logo">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Partner Name *</label>
                        <input type="text" name="name" id="partner_name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                        <input type="url" name="website_url" id="website_url" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" placeholder="https://example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="display_order" id="partner_display_order" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Partner Logo</label>
                        <div id="currentLogoPreview" class="mb-2"></div>
                        <input type="file" name="logo" accept="image/*" class="w-full px-3 py-2 border rounded-lg file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:bg-blue-50 file:text-blue-700 text-sm">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="partner_is_active" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="partner_is_active" class="ml-2 text-sm text-gray-700">Active</label>
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" onclick="closePartnerModal()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition text-sm">Cancel</button>
                        <button type="submit" name="add_partner" id="addPartnerBtn" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition text-sm">Add Partner</button>
                        <button type="submit" name="update_partner" id="updatePartnerBtn" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition text-sm hidden">Update Partner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
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

    <!-- ORIGINAL SIDEBAR - WAG GALAWIN -->
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
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Home Page Management</h1>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0-2.21 3.582-4 8-4s8 1.79  8 4m-8 6h.01M12 13h.01M12 16h.01"/>
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
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 max-w-7xl mx-auto">
                <!-- Hero Sections -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-800">Hero Sections</h3>
                        <button onclick="openHeroModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Add Hero Section
                        </button>
                    </div>
                    <div class="space-y-4">
                        <?php foreach ($hero_sections as $hero): ?>
                        <div class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-800 truncate"><?= htmlspecialchars($hero['title'] ?? 'Untitled') ?></h4>
                                    <p class="text-xs text-gray-600 truncate"><?= htmlspecialchars($hero['subtitle'] ?? 'No subtitle') ?></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs"><?= $hero['is_active'] ? 'Active' : 'Inactive' ?></span>
                                        <?php if ($hero['background_images']): ?>
                                            <?php $images = json_decode($hero['background_images'], true); ?>
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs"><?= count($images) ?> images</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick='editHero(<?= json_encode($hero, JSON_HEX_QUOT | JSON_HEX_APOS) ?>)' class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick='openDeleteModal("hero", <?= $hero['id'] ?>, "<?= htmlspecialchars($hero['title'] ?? 'Hero Section') ?>")' class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($hero_sections)): ?>
                        <p class="text-center text-gray-500 py-8">No hero sections yet. Add one to get started!</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-800">Stats & Counters</h3>
                        <button onclick="openStatModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Add Stat
                        </button>
                    </div>
                    <div id="statsList" class="space-y-3">
                        <?php foreach ($stats as $stat): ?>
                        <div data-id="<?= $stat['id'] ?>" class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md drag-handle group">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0 cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($stat['label'] ?? 'Untitled') ?></h4>
                                    <p class="text-sm text-gray-600">
                                        <?= htmlspecialchars($stat['prefix'] ?? '') ?><?= htmlspecialchars($stat['value'] ?? '') ?><?= htmlspecialchars($stat['suffix'] ?? '') ?>
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Order: <?= $stat['display_order'] ?></span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs"><?= $stat['is_active'] ? 'Active' : 'Inactive' ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick='editStat(<?= json_encode($stat, JSON_HEX_QUOT | JSON_HEX_APOS) ?>)' class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick='openDeleteModal("stat", <?= $stat['id'] ?>, "<?= htmlspecialchars($stat['label'] ?? 'Stat') ?>")' class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($stats)): ?>
                        <p class="text-center text-gray-500 py-8">No stats yet. Add one to get started!</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Testimonials Section -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-800">Testimonials</h3>
                        <button onclick="openTestimonialModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Add Testimonial
                        </button>
                    </div>
                    <div class="space-y-4">
                        <?php foreach ($testimonials as $testimonial): ?>
                        <div class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <?php if ($testimonial['avatar_path']): ?>
                                <img src="../uploads/<?= htmlspecialchars($testimonial['avatar_path']) ?>" class="w-12 h-12 rounded-full object-cover flex-shrink-0" alt="<?= htmlspecialchars($testimonial['author_name']) ?>">
                                <?php else: ?>
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-purple-600 font-bold text-sm"><?= strtoupper(substr($testimonial['author_name'], 0, 1)) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($testimonial['author_name']) ?></h4>
                                    <p class="text-xs text-gray-600"><?= htmlspecialchars($testimonial['author_position']) ?> - <?= htmlspecialchars($testimonial['company']) ?></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                            <svg class="w-3 h-3 <?= $i < $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Order: <?= $testimonial['display_order'] ?></span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs"><?= $testimonial['is_active'] ? 'Active' : 'Inactive' ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick='editTestimonial(<?= json_encode($testimonial, JSON_HEX_QUOT | JSON_HEX_APOS) ?>)' class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick='openDeleteModal("testimonial", <?= $testimonial['id'] ?>, "<?= htmlspecialchars($testimonial['author_name'] ?? 'Testimonial') ?>")' class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($testimonials)): ?>
                        <p class="text-center text-gray-500 py-4">No testimonials yet. Add one to get started!</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Partners Section -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-800">Partners</h3>
                        <button onclick="openPartnerModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Add Partner
                        </button>
                    </div>
                    <div class="space-y-4">
                        <?php foreach ($partners as $partner): ?>
                        <div class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <?php if ($partner['logo_path']): ?>
                                <img src="../uploads/<?= htmlspecialchars($partner['logo_path']) ?>" class="w-12 h-12 object-contain bg-gray-100 rounded-lg flex-shrink-0" alt="<?= htmlspecialchars($partner['name']) ?>">
                                <?php else: ?>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-green-600 font-bold text-xs"><?= strtoupper(substr($partner['name'], 0, 2)) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($partner['name']) ?></h4>
                                    <?php if ($partner['website_url']): ?>
                                    <p class="text-xs text-blue-600 truncate"><?= htmlspecialchars($partner['website_url']) ?></p>
                                    <?php endif; ?>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Order: <?= $partner['display_order'] ?></span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs"><?= $partner['is_active'] ? 'Active' : 'Inactive' ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick='editPartner(<?= json_encode($partner, JSON_HEX_QUOT | JSON_HEX_APOS) ?>)' class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick='openDeleteModal("partner", <?= $partner['id'] ?>, "<?= htmlspecialchars($partner['name'] ?? 'Partner') ?>")' class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($partners)): ?>
                        <p class="text-center text-gray-500 py-4">No partners yet. Add one to get started!</p>
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
// Modal Functions
function openHeroModal() {
    document.getElementById('heroModalTitle').textContent = 'Add Hero Section';
    document.getElementById('addHeroBtn').classList.remove('hidden');
    document.getElementById('updateHeroBtn').classList.add('hidden');
    document.getElementById('replaceImagesOption').classList.add('hidden');
    resetHeroForm();
    showModal('heroModal', 'heroModalOverlay');
}

function closeHeroModal() {
    hideModal('heroModal', 'heroModalOverlay');
}

function openStatModal() {
    document.getElementById('statModalTitle').textContent = 'Add Stat';
    document.getElementById('addStatBtn').classList.remove('hidden');
    document.getElementById('updateStatBtn').classList.add('hidden');
    resetStatForm();
    showModal('statModal', 'statModalOverlay');
}

function closeStatModal() {
    hideModal('statModal', 'statModalOverlay');
}

function openTestimonialModal() {
    document.getElementById('testimonialModalTitle').textContent = 'Add Testimonial';
    document.getElementById('addTestimonialBtn').classList.remove('hidden');
    document.getElementById('updateTestimonialBtn').classList.add('hidden');
    resetTestimonialForm();
    showModal('testimonialModal', 'testimonialModalOverlay');
}

function closeTestimonialModal() {
    hideModal('testimonialModal', 'testimonialModalOverlay');
}

function openPartnerModal() {
    document.getElementById('partnerModalTitle').textContent = 'Add Partner';
    document.getElementById('addPartnerBtn').classList.remove('hidden');
    document.getElementById('updatePartnerBtn').classList.add('hidden');
    resetPartnerForm();
    showModal('partnerModal', 'partnerModalOverlay');
}

function closePartnerModal() {
    hideModal('partnerModal', 'partnerModalOverlay');
}

function openDeleteModal(type, id, title) {
    document.getElementById('deleteTitle').textContent = `Delete ${type.charAt(0).toUpperCase() + type.slice(1)}: "${title}"`;
    
    // Reset all delete buttons
    document.getElementById('deleteHeroBtn').classList.add('hidden');
    document.getElementById('deleteStatBtn').classList.add('hidden');
    document.getElementById('deleteTestimonialBtn').classList.add('hidden');
    document.getElementById('deletePartnerBtn').classList.add('hidden');
    document.getElementById('delete_hero_id').value = '';
    document.getElementById('delete_stat_id').value = '';
    document.getElementById('delete_testimonial_id').value = '';
    document.getElementById('delete_partner_id').value = '';
    
    // Set the appropriate delete button
    if (type === 'hero') {
        document.getElementById('deleteHeroBtn').classList.remove('hidden');
        document.getElementById('delete_hero_id').value = id;
    } else if (type === 'stat') {
        document.getElementById('deleteStatBtn').classList.remove('hidden');
        document.getElementById('delete_stat_id').value = id;
    } else if (type === 'testimonial') {
        document.getElementById('deleteTestimonialBtn').classList.remove('hidden');
        document.getElementById('delete_testimonial_id').value = id;
    } else if (type === 'partner') {
        document.getElementById('deletePartnerBtn').classList.remove('hidden');
        document.getElementById('delete_partner_id').value = id;
    }
    
    showModal('deleteModal', 'deleteModalOverlay');
}

function closeDeleteModal() {
    hideModal('deleteModal', 'deleteModalOverlay');
}

function openSettingsModal() {
    showModal('settingsModal', 'settingsModalOverlay');
}

function closeSettingsModal() {
    hideModal('settingsModal', 'settingsModalOverlay');
}

function showModal(modalId, overlayId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(overlayId);
    modal.classList.remove('hidden');
    setTimeout(() => { modal.classList.add('show'); overlay.classList.add('show'); }, 10);
    document.body.style.overflow = 'hidden';
}

function hideModal(modalId, overlayId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(overlayId);
    modal.classList.remove('show'); overlay.classList.remove('show');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
}

// Form Functions
function resetHeroForm() {
    document.getElementById('edit_hero_id').value = '';
    document.getElementById('hero_title').value = '';
    document.getElementById('hero_subtitle').value = '';
    document.getElementById('hero_description').value = '';
    document.getElementById('primary_button_text').value = '';
    document.getElementById('primary_button_link').value = '';
    document.getElementById('secondary_button_text').value = '';
    document.getElementById('secondary_button_link').value = '';
    document.getElementById('current_images').value = '[]';
    document.getElementById('currentImagesPreview').innerHTML = '';
    document.getElementById('hero_is_active').checked = true;
}

function resetStatForm() {
    document.getElementById('edit_stat_id').value = '';
    document.getElementById('stat_label').value = '';
    document.getElementById('stat_value').value = '';
    document.getElementById('stat_prefix').value = '';
    document.getElementById('stat_suffix').value = '';
    document.getElementById('stat_display_order').value = '0';
    document.getElementById('stat_is_active').checked = true;
}

function resetTestimonialForm() {
    document.getElementById('edit_testimonial_id').value = '';
    document.getElementById('author_name').value = '';
    document.getElementById('author_position').value = '';
    document.getElementById('company').value = '';
    document.getElementById('testimonial_content').value = '';
    document.getElementById('testimonial_display_order').value = '0';
    document.getElementById('current_avatar').value = '';
    document.getElementById('currentAvatarPreview').innerHTML = '';
    
    // Reset star rating
    const stars = document.querySelectorAll('#starRating input');
    stars.forEach(star => star.checked = false);
    document.getElementById('star5').checked = true;
    document.getElementById('testimonial_is_active').checked = true;
}

function resetPartnerForm() {
    document.getElementById('edit_partner_id').value = '';
    document.getElementById('partner_name').value = '';
    document.getElementById('website_url').value = '';
    document.getElementById('partner_display_order').value = '0';
    document.getElementById('current_logo').value = '';
    document.getElementById('currentLogoPreview').innerHTML = '';
    document.getElementById('partner_is_active').checked = true;
}

function editHero(hero) {
    document.getElementById('heroModalTitle').textContent = 'Edit Hero Section';
    document.getElementById('addHeroBtn').classList.add('hidden');
    document.getElementById('updateHeroBtn').classList.remove('hidden');
    document.getElementById('replaceImagesOption').classList.remove('hidden');
    
    document.getElementById('edit_hero_id').value = hero.id;
    document.getElementById('hero_title').value = hero.title || '';
    document.getElementById('hero_subtitle').value = hero.subtitle || '';
    document.getElementById('hero_description').value = hero.description || '';
    document.getElementById('primary_button_text').value = hero.primary_button_text || '';
    document.getElementById('primary_button_link').value = hero.primary_button_link || '';
    document.getElementById('secondary_button_text').value = hero.secondary_button_text || '';
    document.getElementById('secondary_button_link').value = hero.secondary_button_link || '';
    document.getElementById('hero_is_active').checked = hero.is_active == 1;
    
    // Handle images
    const images = hero.background_images ? JSON.parse(hero.background_images) : [];
    document.getElementById('current_images').value = JSON.stringify(images);
    updateImagePreview(images);
    
    showModal('heroModal', 'heroModalOverlay');
}

function editStat(stat) {
    document.getElementById('statModalTitle').textContent = 'Edit Stat';
    document.getElementById('addStatBtn').classList.add('hidden');
    document.getElementById('updateStatBtn').classList.remove('hidden');
    
    document.getElementById('edit_stat_id').value = stat.id;
    document.getElementById('stat_label').value = stat.label || '';
    document.getElementById('stat_value').value = stat.value || '';
    document.getElementById('stat_prefix').value = stat.prefix || '';
    document.getElementById('stat_suffix').value = stat.suffix || '';
    document.getElementById('stat_display_order').value = stat.display_order || 0;
    document.getElementById('stat_is_active').checked = stat.is_active == 1;
    
    showModal('statModal', 'statModalOverlay');
}

function editTestimonial(testimonial) {
    document.getElementById('testimonialModalTitle').textContent = 'Edit Testimonial';
    document.getElementById('addTestimonialBtn').classList.add('hidden');
    document.getElementById('updateTestimonialBtn').classList.remove('hidden');
    
    document.getElementById('edit_testimonial_id').value = testimonial.id;
    document.getElementById('author_name').value = testimonial.author_name || '';
    document.getElementById('author_position').value = testimonial.author_position || '';
    document.getElementById('company').value = testimonial.company || '';
    document.getElementById('testimonial_content').value = testimonial.content || '';
    document.getElementById('testimonial_display_order').value = testimonial.display_order || 0;
    document.getElementById('testimonial_is_active').checked = testimonial.is_active == 1;
    
    // Set star rating
    if (testimonial.rating) {
        const star = document.getElementById(`star${testimonial.rating}`);
        if (star) star.checked = true;
    } else {
        document.getElementById('star5').checked = true;
    }
    
    // Handle avatar preview
    if (testimonial.avatar_path) {
        document.getElementById('current_avatar').value = testimonial.avatar_path;
        document.getElementById('currentAvatarPreview').innerHTML = `
            <div class="image-preview">
                <img src="../uploads/${testimonial.avatar_path}" alt="Current avatar">
            </div>
        `;
    } else {
        document.getElementById('current_avatar').value = '';
        document.getElementById('currentAvatarPreview').innerHTML = '';
    }
    
    showModal('testimonialModal', 'testimonialModalOverlay');
}

function editPartner(partner) {
    document.getElementById('partnerModalTitle').textContent = 'Edit Partner';
    document.getElementById('addPartnerBtn').classList.add('hidden');
    document.getElementById('updatePartnerBtn').classList.remove('hidden');
    
    document.getElementById('edit_partner_id').value = partner.id;
    document.getElementById('partner_name').value = partner.name || '';
    document.getElementById('website_url').value = partner.website_url || '';
    document.getElementById('partner_display_order').value = partner.display_order || 0;
    document.getElementById('partner_is_active').checked = partner.is_active == 1;
    
    // Handle logo preview
    if (partner.logo_path) {
        document.getElementById('current_logo').value = partner.logo_path;
        document.getElementById('currentLogoPreview').innerHTML = `
            <div class="image-preview">
                <img src="../uploads/${partner.logo_path}" alt="Current logo">
            </div>
        `;
    } else {
        document.getElementById('current_logo').value = '';
        document.getElementById('currentLogoPreview').innerHTML = '';
    }
    
    showModal('partnerModal', 'partnerModalOverlay');
}

function updateImagePreview(images) {
    const container = document.getElementById('currentImagesPreview');
    container.innerHTML = '';
    
    images.forEach((image, index) => {
        const preview = document.createElement('div');
        preview.className = 'image-preview';
        preview.innerHTML = `
            <img src="../uploads/${image}" alt="Hero background ${index + 1}">
            <button type="button" class="remove-image" onclick="removeImage(${index})">×</button>
        `;
        container.appendChild(preview);
    });
}

function removeImage(index) {
    const currentImages = JSON.parse(document.getElementById('current_images').value);
    currentImages.splice(index, 1);
    document.getElementById('current_images').value = JSON.stringify(currentImages);
    updateImagePreview(currentImages);
}

// Drag and Drop for Stats
let draggedStat;
document.querySelectorAll('#statsList .drag-handle').forEach(el => {
    el.draggable = true;
    el.addEventListener('dragstart', () => { 
        draggedStat = el; 
        el.classList.add('dragging'); 
    });
    el.addEventListener('dragend', () => { 
        el.classList.remove('dragging'); 
        saveStatOrder(); 
    });
    el.addEventListener('dragover', e => e.preventDefault());
    el.addEventListener('drop', e => {
        e.preventDefault();
        if (draggedStat !== el) {
            const all = Array.from(document.querySelectorAll('#statsList .drag-handle'));
            const from = all.indexOf(draggedStat), to = all.indexOf(el);
            if (from < to) el.after(draggedStat); else el.before(draggedStat);
        }
    });
});

function saveStatOrder() {
    const order = Array.from(document.querySelectorAll('#statsList .drag-handle')).map(el => el.dataset.id);
    fetch('', { 
        method: 'POST', 
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
        body: 'update_stat_order=1&order=' + JSON.stringify(order) 
    });
}

// Sidebar and other functions
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Toast auto-hide
    ['toast-success', 'toast-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) setTimeout(() => { el.classList.add('toast-fade-out'); setTimeout(() => el.remove(), 500); }, 4000);
    });
});
</script>
</body>
</html>