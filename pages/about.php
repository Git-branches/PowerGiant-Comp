<?php 
$page_title = "About Us";
include 'header.php'; 

// Connect to database
require_once '../config/database.php';
$database = new Database();
$db = $database->connect();

try {
    $stmt = $db->query("SELECT * FROM about_content ORDER BY display_order, id");
    $about_content = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("About DB Error: " . $e->getMessage());
    $about_content = [];
}

// Build content map
$content = [];
foreach ($about_content as $item) {
    $content[$item['section_name']] = $item;
}

// FINAL FIX - Handles base path correctly
function getImageUrl($path, $fallback) {
    if (empty($path)) {
        return getBasePath() . ltrim($fallback, '/');
    }
    
    $cleanPath = ltrim($path, '/');
    $serverPath = __DIR__ . '/../' . $cleanPath;
    
    if (file_exists($serverPath)) {
        return getBasePath() . $cleanPath . '?v=' . filemtime($serverPath);
    }
    
    return getBasePath() . ltrim($fallback, '/');
}

// Helper function to get base path
function getBasePath() {
    // Get the base directory (e.g., /pgrmtv2/)
    $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /pgrmtv2/pages/about.php
    $basePath = dirname(dirname($scriptName)); // e.g., /pgrmtv2
    return $basePath . '/';
}
?>

<script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>
<script src="script.js"></script>
<link rel="stylesheet" href="style.css">

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.classList.add('theme-transition');
        html.setAttribute('data-theme', newTheme);
        updateThemeIcons(newTheme);
        localStorage.setItem('theme', newTheme);
        
        setTimeout(() => html.classList.remove('theme-transition'), 300);
    }
    
    function updateThemeIcons(theme) {
        document.querySelectorAll('.sun-icon').forEach(icon => 
            icon.classList.toggle('hidden', theme !== 'light')
        );
        document.querySelectorAll('.moon-icon').forEach(icon => 
            icon.classList.toggle('hidden', theme !== 'dark')
        );
    }
    
    function initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcons(savedTheme);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu) {
                    mobileMenu.classList.toggle('hidden');
                    mobileMenu.classList.toggle('animate-slide-down');
                }
            });
        }
        
        initTheme();
        
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                navbar.classList.toggle('nav-shadow', window.scrollY > 10);
            }
        });
    });
    
    function toggleMobileDropdown(id) {
        const dropdown = document.getElementById(id);
        const arrow = document.getElementById(id.replace('dropdown', 'arrow'));
        if (dropdown && arrow) {
            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }
    }
</script>

<main id="about" class="min-h-screen">
    <section class="section-padding bg-surface relative overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 transform-gpu">
                <img src="https://images.unsplash.com/photo-1726731782158-fcf6822b6ca4" 
                     alt="Oil refinery" 
                     class="w-full h-full object-cover opacity-15 parallax-slow scale-110"
                     onerror="this.src='https://images.pexels.com/photos/162568/oil-rig-sea-oil-drilling-162568.jpeg?auto=compress&cs=tinysrgb&w=2940';">
            </div>
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-3xl rotate-12 animate-float opacity-60"></div>
                <div class="absolute top-40 right-20 w-24 h-24 bg-gradient-to-br from-secondary/10 to-primary/10 rounded-2xl -rotate-12 animate-float-delayed opacity-40"></div>
                <div class="absolute bottom-32 left-1/4 w-20 h-20 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-xl rotate-45 animate-pulse opacity-50"></div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-white/95 via-white/90 to-surface/85 dark:from-gray-900/95 dark:via-gray-900/90 dark:to-surface-dark/85"></div>
        </div>

        <div class="container-custom relative z-10">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl text-secondary-50 dark:text-white mb-6">
                    <?= htmlspecialchars($content['main_title']['content_value'] ?? 'About Oil Product Industries with Excellence') ?>
                </h2>
                <p class="text-lg text-secondary dark:text-gray-300 max-w-3xl mx-auto">
                    <?= htmlspecialchars($content['main_description']['content_value'] ?? 'POWER GIANT RMT IS A DYNAMIC AND INNOVATIVE ENTERPRISE...') ?>
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Main Image -->
                <div class="relative group">
                    <div class="relative overflow-hidden rounded-3xl shadow-2xl transform perspective-1000 group-hover:scale-105 transition-all duration-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-secondary/20 z-10"></div>
                        <img src="<?= getImageUrl($content['main_image']['image_path'] ?? '', 'css/images/about.png') ?>" 
                             alt="Power Giant RMT"
                             class="w-full h-96 object-cover transform scale-110 group-hover:scale-125 transition-transform duration-1000"
                             onerror="this.onerror=null; this.src='/css/images/about.png';">
                        
                        <div class="absolute top-6 left-6 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-xl p-4 shadow-xl rotate-3 hover:rotate-0 transition-transform">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M11,6V12.414L15.293,16.707L16.707,15.293L13,11.586V6H11Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-lg font-poppins font-bold text-secondary-50 dark:text-white">24/7</div>
                                    <div class="text-xs text-text-secondary dark:text-gray-400">Operations</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-6 right-6 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-xl p-4 shadow-xl -rotate-3 hover:rotate-0 transition-transform">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-success to-secondary rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-lg font-poppins font-bold text-secondary-50 dark:text-white">ISO</div>
                                    <div class="text-xs text-text-secondary dark:text-gray-400">Certified</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -top-8 -right-8 bg-gradient-to-br from-white to-accent-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 shadow-2xl rotate-6 hover:rotate-3 transition-all z-30">
                        <div class="text-center">
                            <div class="text-3xl font-poppins font-bold text-primary dark:text-primary-light" data-count="20">0</div>
                            <div class="text-sm text-secondary dark:text-gray-300 font-medium">Years<br>Excellence</div>
                        </div>
                    </div>
                    <div class="absolute -bottom-8 -left-8 bg-gradient-to-br from-white to-primary-50 dark:from-gray-800 dark:to-primary-900/20 rounded-2xl p-6 shadow-2xl -rotate-6 hover:-rotate-3 transition-all z-30">
                        <div class="text-center">
                            <div class="text-3xl font-poppins font-bold text-secondary dark:text-secondary-light" data-count="50">0</div>
                            <div class="text-sm text-secondary dark:text-gray-300 font-medium">Oil<br>Stations</div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="space-y-8">
                    <h3 class="font-poppins font-semibold text-3xl text-secondary-50 dark:text-white mb-6">
                        <?= htmlspecialchars($content['about_title']['content_value'] ?? 'ABOUT COMPANY') ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="p-6 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg border border-accent-100 dark:border-gray-700">
                            <p class="text-secondary dark:text-gray-300 leading-relaxed">
                                <?= nl2br(htmlspecialchars($content['about_content_1']['content_value'] ?? 'POWER GIANT RMT IS A DYNAMIC...')) ?>
                            </p>
                        </div>
                        <div class="p-6 bg-gradient-to-br from-primary/5 to-secondary/5 dark:from-primary/10 dark:to-secondary/10 rounded-2xl shadow-lg border border-accent-100 dark:border-gray-700">
                            <p class="text-secondary dark:text-gray-300 leading-relaxed">
                                <?= nl2br(htmlspecialchars($content['about_content_2']['content_value'] ?? 'OUR SERVICES DIVISION...')) ?>
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-md hover:scale-105 transition-all group">
                            <div class="w-12 h-12 bg-gradient-to-br from-success to-secondary rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-secondary dark:text-gray-300 font-medium">ISO 9001:2015 Quality Management Certified</span>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-md hover:scale-105 transition-all group">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-secondary dark:text-gray-300 font-medium">24/7 Emergency Oil Delivery Service</span>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-md hover:scale-105 transition-all group">
                            <div class="w-12 h-12 bg-gradient-to-br from-secondary to-success rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                                </svg>
                            </div>
                            <span class="text-secondary dark:text-gray-300 font-medium">Advanced Oil Quality Testing Laboratory</span>
                        </div>
                    </div>

                    <div class="mt-12 p-6 bg-gradient-to-br from-white/70 to-accent-50/70 dark:from-gray-800/70 dark:to-gray-700/70 backdrop-blur-sm rounded-2xl border border-accent-200 dark:border-gray-600 shadow-xl">
                        <h4 class="font-poppins font-semibold text-lg text-secondary-50 dark:text-white mb-4 text-center">Trusted by 5+ Corporate Partners</h4>
                        <div class="grid grid-cols-4 gap-4">
                            <?php foreach (['MFG', 'AVN', 'MAR', 'LOG'] as $i => $code): ?>
                            <div class="text-center transform hover:scale-110 transition-transform">
                                <div class="w-16 h-16 bg-gradient-to-br from-<?= ['primary','secondary','success','primary'][$i] ?> to-<?= ['secondary','success','primary','secondary'][$i] ?> rounded-xl flex items-center justify-center mx-auto mb-2 shadow-lg">
                                    <span class="text-white font-bold text-xs"><?= $code ?></span>
                                </div>
                                <div class="text-xs text-text-secondary dark:text-gray-400">
                                    <?= ['Manufacturing','Aviation','Marine','Logistics'][$i] ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery -->
            <div class="mt-20">
                <div class="text-center mb-12">
                    <h3 class="font-poppins font-bold text-2xl sm:text-3xl text-secondary-50 dark:text-white mb-4">
                        <?= htmlspecialchars($content['quality_policy_title']['content_value'] ?? 'QUALITY POLICY') ?>
                    </h3>
                    <p class="text-secondary dark:text-gray-300 max-w-2xl mx-auto">
                        <?= htmlspecialchars($content['quality_policy_description']['content_value'] ?? 'Power Giant RMT is committed...') ?>
                    </p>
                </div>

                <div class="image-gallery grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php 
                    $cards = [
                        'mission' => ['title' => 'mission_title', 'desc' => 'mission_description', 'img' => 'mission_image', 'fallback' => 'css/images/Mission.png'],
                        'vision' => ['title' => 'vision_title', 'desc' => 'vision_description', 'img' => 'vision_image', 'fallback' => 'css/images/Vission.png'],
                        'quality' => ['title' => 'quality_policy_card_title', 'desc' => 'quality_policy_card_description', 'img' => 'quality_image', 'fallback' => 'css/images/Quality.png'],
                        'delivery' => ['title' => 'delivery_title', 'desc' => 'delivery_description', 'img' => 'delivery_image', 'fallback' => 'css/images/Delivery.png'],
                        'process' => ['title' => 'process_title', 'desc' => 'process_description', 'img' => 'process_image', 'fallback' => 'css/images/Process.png'],
                        'certificate' => ['title' => 'certificate_title', 'desc' => 'certificate_description', 'img' => 'certificate_image', 'fallback' => 'css/images/cert.png'],
                    ];
                    foreach ($cards as $key => $c): 
                        $title = $content[$c['title']]['content_value'] ?? ucfirst($key);
                        $desc = $content[$c['desc']]['content_value'] ?? "Description for $key";
                        $img_path = $content[$c['img']]['image_path'] ?? '';
                    ?>
                    <div class="gallery-item relative group overflow-hidden rounded-2xl shadow-xl">
                        <img src="<?= getImageUrl($img_path, $c['fallback']) ?>" 
                             alt="<?= htmlspecialchars($title) ?>"
                             class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110"
                             onerror="this.onerror=null; this.src='/<?= ltrim($c['fallback'], '/') ?>';">
                        <div class="gallery-overlay absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 text-white">
                            <div class="gallery-title font-poppins font-bold text-xl mb-2"><?= htmlspecialchars($title) ?></div>
                            <div class="gallery-description text-sm"><?= htmlspecialchars($desc) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('[data-count]');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-count');
            let count = 0;
            const inc = target / 100;
            const update = () => {
                if (count < target) {
                    count += inc;
                    counter.textContent = Math.ceil(count);
                    requestAnimationFrame(update);
                } else counter.textContent = target;
            };
            const observer = new IntersectionObserver(entries => {
                if (entries[0].isIntersecting) {
                    update();
                    observer.unobserve(counter);
                }
            }, { threshold: 0.5 });
            observer.observe(counter.parentElement);
        });
    });
</script>

<style>
    .theme-transition * { transition: background-color .3s ease, color .3s ease, border-color .3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
    .animate-fade-in { animation: fadeIn .3s ease-in-out; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-down { animation: slideDown .3s ease-out; }
</style>