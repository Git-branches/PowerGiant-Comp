<?php 
$page_title = "News & Updates";
include 'header.php'; 

// Connect to database
require_once '../config/database.php';
$database = new Database();
$db = $database->connect();

// UPDATED: Helper function for news images - MATCHES YOUR NEWS_EDITOR.PHP STRUCTURE
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

// Fetch published news
$stmt = $db->query("
    SELECT id, news_title, news_excerpt, news_content, author, publish_date, image_path 
    FROM news_content 
    WHERE is_published = 1 
    ORDER BY display_order ASC, publish_date DESC, id DESC
");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Normalize data: Map DB columns → Template keys
$normalized_news = [];
foreach ($news as $item) {
    // UPDATED: Use helper function for image paths
    $normalized_news[] = [
        'id'           => $item['id'],
        'title'        => $item['news_title'] ?? 'Untitled',
        'excerpt'      => $item['news_excerpt'] ?? 'No excerpt available.',
        'content'      => $item['news_content'] ?? '',
        'author'       => $item['author'] ?? 'PG RMT Team',
        'publish_date' => $item['publish_date'],
        'image_path'   => getNewsImage($item['image_path']),
        'category'     => 'GENERAL',
        'read_time'    => '2 min read'
    ];
}

// Fallback if DB is empty
if (empty($normalized_news)) {
    $normalized_news = [
        [
            'title' => 'Power Giant RMT Opens ₱1.2B Green Fuel Depot in Visayas',
            'excerpt' => 'First carbon-neutral fuel terminal in Southeast Asia',
            'content' => 'The new facility in Cebu features solar-powered operations, waste heat recovery, and real-time emissions monitoring — setting a new standard for sustainable energy infrastructure.',
            'author' => 'PG RMT Team',
            'publish_date' => '2025-10-28',
            'category' => 'BREAKING',
            'image_path' => '../css/images/news-featured.jpg',
            'read_time' => '2 min read'
        ],
    ];
}

// Assign to sections
$featured_news = $normalized_news[0] ?? null;
$recent_news   = array_slice($normalized_news, 1, 3);
$grid_news     = array_slice($normalized_news, 4);

// Stats
$news_stats = [
    ['count' => count($normalized_news), 'label' => 'News Articles'],
    ['count' => '12', 'label' => 'Awards Won'],
    ['count' => '2.5M', 'label' => 'Tons CO₂ Reduced'],
    ['count' => '50+', 'label' => 'Media Features']
];

$media_mentions = [
    '../css/images/hazardous.png',
    '../css/images/about.png', 
    '../css/images/cert.png',
    '../css/images/Process.png'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Power Giant RMT</title>
    <link rel="icon" type="image/x-icon" href="../css/images/favicon.ico">
    <link rel="stylesheet" href="../css/output.css">
    <link rel="stylesheet" href="style.css">
    <link rel="alternate" type="application/rss+xml" title="Power Giant RMT News" href="/rss/news.xml">
</head>
<body>
    <script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>
    <script src="script.js"></script>

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

<main class="min-h-screen bg-surface">
    <!-- Hero Banner -->
    <section class="hero-banner relative py-20 md:py-32 bg-gradient-to-br from-primary via-primary-800 to-secondary-900 overflow-hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        
        <div class="absolute top-20 left-10 w-48 h-48 bg-secondary/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-32 right-20 w-40 h-40 bg-primary/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-accent/10 rounded-full blur-3xl animate-pulse-delayed"></div>

        <div class="container-custom relative z-10 text-center text-white px-4">
            <h1 class="font-poppins font-bold text-3xl sm:text-4xl md:text-5xl lg:text-6xl mb-4 md:mb-6 max-w-5xl mx-auto">
                Latest News & Updates
            </h1>
            <p class="text-base sm:text-lg md:text-xl max-w-3xl mx-auto text-accent-100 mb-6 md:mb-8">
                Stay informed with Power Giant RMT — industry milestones, sustainability wins, and community impact.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center px-4">
                <a href="#latest" class="btn-primary text-base md:text-lg px-6 md:px-8 py-3">Read Latest</a>
                <a href="#subscribe" class="btn-secondary text-base md:text-lg px-6 md:px-8 py-3">Subscribe to Updates</a>
            </div>
        </div>
    </section>

    <!-- News Stats -->
    <section class="section-padding bg-surface">
        <div class="container-custom px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 text-center">
                <?php foreach ($news_stats as $stat): ?>
                <div class="group">
                    <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-poppins font-bold secondary-50 mb-2 group-hover:scale-110 transition-transform duration-300 inline-block">
                        <?= htmlspecialchars($stat['count']) ?>
                    </div>
                    <p class="text-xs sm:text-sm md:text-base text-text-secondary font-medium"><?= htmlspecialchars($stat['label']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Latest News -->
    <section id="latest" class="section-padding bg-gradient-to-br from-primary/5 to-secondary/5">
        <div class="container-custom px-4">
            <div class="flex flex-col lg:flex-row gap-6 md:gap-8 items-start">
                <!-- Featured -->
                <?php if ($featured_news): ?>
                <div class="w-full lg:w-2/3">
                    <div class="featured-news group relative overflow-hidden rounded-2xl md:rounded-3xl shadow-2xl bg-white border border-accent-100 hover:shadow-3xl transition-all duration-700">
                        <div class="relative h-64 sm:h-72 md:h-80 lg:h-96 overflow-hidden">
                            <img src="<?= htmlspecialchars($featured_news['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($featured_news['title']) ?>" 
                                 class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                                 onerror="this.src='https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1200';">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 md:bottom-6 left-4 md:left-6 right-4 md:right-6 text-white">
                                <span class="inline-block px-2 md:px-3 py-1 bg-secondary text-white rounded-full text-xs font-bold mb-2 md:mb-3">
                                    <?= htmlspecialchars($featured_news['category']) ?>
                                </span>
                                <h2 class="font-poppins font-bold text-xl sm:text-2xl md:text-3xl mb-2 line-clamp-2">
                                    <?= htmlspecialchars($featured_news['title']) ?>
                                </h2>
                                <p class="text-xs sm:text-sm opacity-90 mb-2 md:mb-3 line-clamp-2"><?= htmlspecialchars($featured_news['excerpt']) ?></p>
                                <div class="flex items-center text-xs opacity-80">
                                    <span><?= date('M j, Y', strtotime($featured_news['publish_date'])) ?></span>
                                    <span class="mx-2">•</span>
                                    <span><?= htmlspecialchars($featured_news['read_time']) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 md:p-6 lg:p-8">
                            <p class="text-sm md:text-base text-text-secondary mb-4 md:mb-6 line-clamp-3"><?= htmlspecialchars($featured_news['content']) ?></p>
                            <a href="news-detail.php?id=<?= $featured_news['id'] ?>" class="text-secondary font-semibold hover:text-primary transition-colors flex items-center text-sm md:text-base">
                                Read Full Story 
                                <svg class="w-4 h-4 md:w-5 md:h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Sidebar -->
                <div class="w-full lg:w-1/3 space-y-4 md:space-y-6">
                    <h3 class="font-poppins font-bold text-lg md:text-xl secondary-50 mb-4">Recent Updates</h3>
                    <?php foreach ($recent_news as $recent): ?>
                    <div class="news-item group flex gap-3 md:gap-4 p-3 md:p-4 bg-white rounded-xl md:rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 border border-accent-50">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-lg md:rounded-xl overflow-hidden flex-shrink-0">
                            <img src="<?= htmlspecialchars($recent['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($recent['title']) ?>" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://images.pexels.com/photos/3183183/pexels-photo-3183183.jpeg?auto=compress&cs=tinysrgb&w=400';">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-poppins font-semibold text-xs sm:text-sm secondary-50 mb-1 group-hover:text-primary transition-colors line-clamp-2">
                                <?= htmlspecialchars($recent['title']) ?>
                            </h4>
                            <p class="text-xs text-text-secondary mb-2">
                                <?= date('M j, Y', strtotime($recent['publish_date'])) ?> • <?= htmlspecialchars($recent['read_time']) ?>
                            </p>
                            <a href="news-detail.php?id=<?= $recent['id'] ?>" class="text-xs text-secondary font-medium hover:underline">Read</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- News Grid -->
    <section class="section-padding bg-surface">
        <div class="container-custom px-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 md:mb-12 gap-4">
                <h2 class="font-poppins font-bold text-2xl md:text-3xl secondary-50">All News</h2>
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <button class="px-3 md:px-4 py-2 bg-white border border-accent-200 rounded-lg text-xs md:text-sm font-medium hover:bg-accent-50 transition-colors">All</button>
                    <button class="px-3 md:px-4 py-2 bg-white border border-accent-200 rounded-lg text-xs md:text-sm font-medium hover:bg-accent-50 transition-colors">Sustainability</button>
                    <button class="px-3 md:px-4 py-2 bg-white border border-accent-200 rounded-lg text-xs md:text-sm font-medium hover:bg-accent-50 transition-colors">Awards</button>
                    <button class="px-3 md:px-4 py-2 bg-white border border-accent-200 rounded-lg text-xs md:text-sm font-medium hover:bg-accent-50 transition-colors">Projects</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                <?php if (!empty($grid_news)): ?>
                    <?php foreach ($grid_news as $item): ?>
                    <div class="news-card group relative overflow-hidden rounded-xl md:rounded-2xl bg-white shadow-lg hover:shadow-xl transition-all duration-500 border border-accent-100">
                        <div class="h-40 sm:h-44 md:h-48 overflow-hidden">
                            <img src="<?= htmlspecialchars($item['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                 onerror="this.src='https://images.pexels.com/photos/414837/pexels-photo-414837.jpeg?auto=compress&cs=tinysrgb&w=800';">
                        </div>
                        <div class="p-4 md:p-6">
                            <?php 
                            $cat = $item['category'];
                            $cat_class = match($cat) {
                                'SUSTAINABILITY' => 'bg-success/10 text-success',
                                'PARTNERSHIP' => 'bg-primary/10 text-primary',
                                'DIVERSITY' => 'bg-purple-100 text-purple-700',
                                'AWARD' => 'bg-amber-100 text-amber-700',
                                'SAFETY' => 'bg-blue-100 text-blue-700',
                                'BREAKING' => 'bg-red-100 text-red-700',
                                default => 'bg-secondary/10 text-secondary'
                            };
                            ?>
                            <span class="inline-block px-2 md:px-3 py-1 <?= $cat_class ?> rounded-full text-xs font-bold mb-2 md:mb-3">
                                <?= htmlspecialchars($cat) ?>
                            </span>
                            <h3 class="font-poppins font-semibold text-base md:text-lg secondary-50 mb-2 group-hover:text-primary transition-colors line-clamp-2">
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>
                            <p class="text-text-secondary text-xs md:text-sm mb-3 md:mb-4 line-clamp-2"><?= htmlspecialchars($item['excerpt']) ?></p>
                            <div class="flex justify-between text-xs text-text-secondary">
                                <span><?= date('M j, Y', strtotime($item['publish_date'])) ?></span>
                                <a href="news-detail.php?id=<?= $item['id'] ?>" class="text-secondary font-medium hover:underline">Read More</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="col-span-3 text-center text-text-secondary py-8">No more articles.</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($grid_news)): ?>
            <div class="mt-8 md:mt-12 text-center">
                <button class="bg-gradient-to-r from-primary to-secondary text-white font-poppins font-semibold px-6 md:px-8 py-3 rounded-full shadow-lg hover:shadow-xl transition-all text-sm md:text-base">
                    Load More News
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Newsletter -->
    <section id="subscribe" class="section-padding bg-gradient-to-br from-primary to-secondary">
        <div class="container-custom text-center text-white px-4">
            <h2 class="font-poppins font-bold text-2xl sm:text-3xl md:text-4xl mb-4 md:mb-6">Stay Updated</h2>
            <p class="text-base md:text-lg mb-6 md:mb-8 max-w-2xl mx-auto opacity-90">
                Get monthly updates on sustainability, projects, and industry insights.
            </p>
            <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-3 md:gap-4">
                <input type="email" placeholder="Enter your email" class="flex-1 px-4 md:px-6 py-3 md:py-4 rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white/30 text-sm md:text-base">
                <button type="submit" class="btn-light px-6 md:px-8 py-3 md:py-4 rounded-full font-semibold hover:scale-105 transition-transform text-sm md:text-base">
                    Subscribe
                </button>
            </form>
            <p class="text-xs mt-3 md:mt-4 opacity-70">We respect your privacy. Unsubscribe anytime.</p>
        </div>
    </section>

    <!-- Media Mentions -->
    <section class="section-padding bg-surface">
        <div class="container-custom px-4">
            <h2 class="font-poppins font-bold text-2xl md:text-3xl secondary-50 text-center mb-8 md:mb-12">As Featured In</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 items-center opacity-60 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                <?php foreach ($media_mentions as $media): ?>
                <img src="<?= htmlspecialchars($media) ?>" alt="Media" class="h-8 md:h-12 mx-auto object-contain" onerror="this.style.display='none'">
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<style>
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-pulse-delayed { animation: pulse 3s ease-in-out infinite 1s; }
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    
    /* ✅ Utility for text truncation */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* ✅ Additional mobile optimizations */
    @media (max-width: 640px) {
        .container-custom {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
</body>
</html>