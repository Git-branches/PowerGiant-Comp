<?php
require_once '../config/database.php';
require_once '../config/config.php';

// ADD THIS HELPER FUNCTION (SAME AS IN NEWS.PHP)
function getNewsImage($imagePath, $fallbackImage = 'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1200') {
    if (empty($imagePath)) {
        return $fallbackImage;
    }
    
    // Your news_editor.php stores paths like "news/filename.jpg"
    $fullPath = "../uploads/" . $imagePath; // This becomes "../uploads/news/filename.jpg"
    
    if (file_exists($fullPath)) {
        return $fullPath;
    }
    
    return $fallbackImage;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: news.php');
    exit;
}

try {
    $db = (new Database())->connect();
    $stmt = $db->prepare("
        SELECT id, news_title, news_excerpt, news_content, author, publish_date, image_path 
        FROM news_content 
        WHERE id = ? AND is_published = 1
    ");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        http_response_code(404);
        include '../includes/404.php';
        exit;
    }
} catch (Exception $e) {
    error_log('News detail error: ' . $e->getMessage());
    die('<div class="text-center py-16 text-red-600">Error loading article.</div>');
}

// Normalize keys for template - USE THE SAME HELPER FUNCTION
$title     = $article['news_title'];
$excerpt   = $article['news_excerpt'] ?: 'No excerpt available.';
$content   = $article['news_content'];
$author    = $article['author'];
$date      = date('F j, Y', strtotime($article['publish_date']));

// FIXED: Use the same helper function as news.php
$image     = getNewsImage($article['image_path']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Power Giant RMT</title>
    <link rel="icon" type="image/x-icon" href="../css/images/favicon.ico">
    <link rel="stylesheet" href="../css/output.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="alternate" type="application/rss+xml" title="Power Giant RMT News" href="/rss/news.xml">
    <style>
        .prose { max-width: 100%; }
        .prose p { margin-bottom: 1.25rem; line-height: 1.8; }
        .prose h1, .prose h2, .prose h3 { margin: 2rem 0 1rem; font-weight: bold; }
        .prose ul, .prose ol { padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .prose li { margin-bottom: 0.5rem; }
        .prose img { border-radius: 0.75rem; margin: 1.5rem 0; max-width: 100%; height: auto; }
        .print-only { display: none; }
        
        /* ✅ Mobile Responsive Styles */
        @media (max-width: 640px) {
            .prose { font-size: 0.9rem; }
            .prose h1 { font-size: 1.5rem; }
            .prose h2 { font-size: 1.25rem; }
            .prose h3 { font-size: 1.125rem; }
        }
        
        @media print {
            .no-print { display: none; }
            .print-only { display: block; }
            body { background: white; }
            .container-custom { max-width: 100%; padding: 0 1in; }
        }
    </style>
</head>
<body class="bg-surface min-h-screen flex flex-col">

    <?php include 'header.php'; ?>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const newTheme = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            document.querySelectorAll('.theme-toggle-icon').forEach(i => i.classList.toggle('hidden'));
            localStorage.setItem('theme', newTheme);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Theme initialization
            const saved = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
            if (saved === 'dark') {
                document.querySelectorAll('.sun-icon').forEach(i => i.classList.add('hidden'));
                document.querySelectorAll('.moon-icon').forEach(i => i.classList.remove('hidden'));
            }

            // ✅ FIXED: Mobile menu with dropdown support
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuBtn && mobileMenu) {
                // Toggle mobile menu
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    mobileMenu.classList.toggle('hidden');
                });
                
                // ✅ Prevent details/summary from closing menu
                const allDetails = mobileMenu.querySelectorAll('details');
                allDetails.forEach(detail => {
                    const summary = detail.querySelector('summary');
                    
                    if (summary) {
                        summary.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }
                    
                    detail.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
                
                // ✅ Close menu only on page navigation links
                const pageLinks = mobileMenu.querySelectorAll('a[href$=".php"], a[href^="index"]');
                pageLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        setTimeout(() => {
                            mobileMenu.classList.add('hidden');
                        }, 100);
                    });
                });
            }

            // Navbar scroll effect
            window.addEventListener('scroll', () => {
                const navbar = document.getElementById('navbar');
                if (navbar) navbar.classList.toggle('nav-shadow', window.scrollY > 10);
            });
        });
    </script>

    <main class="flex-1">
        <article class="container-custom py-8 md:py-12 lg:py-16 px-4">
            <!-- Breadcrumb -->
            <nav class="text-xs sm:text-sm text-text-secondary mb-4 md:mb-6 no-print">
                <a href="../index.php" class="hover:text-primary">Home</a>
                <span class="mx-2">/</span>
                <a href="news.php" class="hover:text-primary">News</a>
                <span class="mx-2">/</span>
                <span class="text-primary truncate inline-block max-w-[200px] sm:max-w-none"><?= htmlspecialchars($title) ?></span>
            </nav>

            <div class="grid lg:grid-cols-3 gap-6 md:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <header class="mb-6 md:mb-8">
                        <h1 class="font-poppins font-bold text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-secondary-50 mb-3 md:mb-4 leading-tight">
                            <?= htmlspecialchars($title) ?>
                        </h1>
                        <div class="flex flex-wrap items-center gap-2 md:gap-4 text-xs sm:text-sm text-text-secondary">
                            <div class="flex items-center">
                                <i class="fas fa-user text-primary mr-1 md:mr-2"></i>
                                <span><?= htmlspecialchars($author) ?></span>
                            </div>
                            <span class="hidden sm:inline">•</span>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-primary mr-1 md:mr-2"></i>
                                <time><?= $date ?></time>
                            </div>
                            <span class="hidden sm:inline">•</span>
                            <div class="flex items-center no-print">
                                <i class="fas fa-clock text-primary mr-1 md:mr-2"></i>
                                <span>3 min read</span>
                            </div>
                        </div>
                    </header>

                    <!-- Featured Image -->
                    <?php if ($image): ?>
                    <div class="mb-6 md:mb-10 rounded-xl md:rounded-2xl overflow-hidden shadow-lg md:shadow-xl">
                        <img src="<?= $image ?>" 
                             alt="<?= htmlspecialchars($title) ?>" 
                             class="w-full h-auto max-h-64 sm:max-h-80 md:max-h-96 object-cover"
                             onerror="this.src='https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1200';">
                    </div>
                    <?php endif; ?>

                    <!-- Excerpt -->
                    <?php if ($excerpt): ?>
                    <p class="text-base md:text-lg text-text-secondary italic mb-6 md:mb-8 pb-6 md:pb-8 border-b border-accent-100">
                        <?= htmlspecialchars($excerpt) ?>
                    </p>
                    <?php endif; ?>

                    <!-- Full Content -->
                    <div class="prose prose-sm sm:prose-base lg:prose-lg text-text-primary">
                        <?= nl2br(htmlspecialchars($content)) ?>
                    </div>

                    <!-- Tags -->
                    <div class="mt-8 md:mt-12 pt-6 md:pt-8 border-t border-accent-100 no-print">
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2 md:px-3 py-1 bg-secondary/10 text-secondary rounded-full text-xs sm:text-sm font-medium">
                                Sustainability
                            </span>
                            <span class="px-2 md:px-3 py-1 bg-primary/10 text-primary rounded-full text-xs sm:text-sm font-medium">
                                Innovation
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="lg:col-span-1 space-y-6 md:space-y-8">
                    <!-- Share Buttons -->
                    <div class="bg-white p-4 md:p-6 rounded-xl md:rounded-2xl shadow-md no-print">
                        <h3 class="font-poppins font-semibold text-base md:text-lg mb-3 md:mb-4">Share This Article</h3>
                        <div class="flex gap-2 md:gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                               target="_blank" 
                               class="w-9 h-9 md:w-10 md:h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition text-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($title) ?>" 
                               target="_blank" 
                               class="w-9 h-9 md:w-10 md:h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition text-sm">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                               target="_blank" 
                               class="w-9 h-9 md:w-10 md:h-10 bg-blue-700 text-white rounded-full flex items-center justify-center hover:bg-blue-800 transition text-sm">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied!')" 
                                    class="w-9 h-9 md:w-10 md:h-10 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center hover:bg-gray-300 transition text-sm">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Related News -->
                    <div class="bg-white p-4 md:p-6 rounded-xl md:rounded-2xl shadow-md">
                        <h3 class="font-poppins font-semibold text-base md:text-lg mb-3 md:mb-4">Related Articles</h3>
                        <p class="text-xs sm:text-sm text-text-secondary">Coming soon...</p>
                    </div>

                    <!-- Print Button -->
                    <div class="no-print">
                        <button onclick="window.print()" 
                                class="w-full bg-gradient-to-r from-primary to-secondary text-white font-poppins font-medium py-2.5 md:py-3 rounded-lg md:rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 text-sm md:text-base">
                            <i class="fas fa-print"></i>
                            Print Article
                        </button>
                    </div>
                </aside>
            </div>

            <!-- Back to News -->
            <div class="mt-12 md:mt-16 text-center no-print">
                <a href="news.php" class="inline-flex items-center text-primary font-semibold hover:text-secondary transition text-sm md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to All News
                </a>
            </div>
        </article>
    </main>

    <?php include 'footer.php'; ?>

    <script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>
    <script src="script.js"></script>

    <!-- Print Styles -->
    <script>
        window.addEventListener('beforeprint', () => {
            document.body.classList.add('printing');
        });
        window.addEventListener('afterprint', () => {
            document.body.classList.remove('printing');
        });
    </script>
</body>
</html>