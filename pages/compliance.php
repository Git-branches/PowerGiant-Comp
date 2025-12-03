<?php 
$page_title = "Compliance & Permits";
include 'header.php'; 

require_once '../config/database.php';
$database = new Database();
$db = $database->connect();

$stmt = $db->query("SELECT * FROM compliance_content WHERE is_active = 1 ORDER BY display_order");
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fallback if DB empty
if (empty($certificates)) {
    $certificates = [
        [
            'certificate_name' => 'DENR Hazardous Waste Transporter',
            'certificate_description' => 'License No: HWT-2025-001 | Valid until Dec 2027',
            'issuing_authority' => 'DENR',
            'expiry_date' => '2027-12-31',
            'image_path' => 'uploads/compliance/denr-hwt.jpg'
        ],
        [
            'certificate_name' => 'DOE Oil Industry Participant', 
            'certificate_description' => 'Reg No: OIP-2025-POWERGIANT | Valid until Dec 2026',
            'issuing_authority' => 'DOE',
            'expiry_date' => '2026-12-31',
            'image_path' => 'uploads/compliance/doe-oip.jpg'
        ],
        [
            'certificate_name' => 'ISO 9001:2015',
            'certificate_description' => 'Quality Management System | Cert No: QMS-2025-Alpha',
            'issuing_authority' => 'International Standards Organization',
            'expiry_date' => '2025-12-31',
            'image_path' => 'uploads/compliance/iso-9001.jpg'
        ],
        [
            'certificate_name' => 'ISO 14001:2015',
            'certificate_description' => 'Environmental Management System',
            'issuing_authority' => 'International Standards Organization',
            'expiry_date' => '2025-12-31',
            'image_path' => 'uploads/compliance/iso-14001.jpg'
        ],
        [
            'certificate_name' => 'DOLE OSHS Compliance',
            'certificate_description' => 'Occupational Safety & Health Standards',
            'issuing_authority' => 'DOLE',
            'expiry_date' => '2025-12-31',
            'image_path' => 'uploads/compliance/dole-oshs.jpg'
        ],
        [
            'certificate_name' => 'Clean Air Act Compliance',
            'certificate_description' => 'Emission Standards Met | Annual Report 2025',
            'issuing_authority' => 'DENR',
            'expiry_date' => '2025-12-31',
            'image_path' => 'uploads/compliance/clean-air.jpg'
        ]
    ];
}

// Helper: Safe image path
function getImageUrl($path, $fallback) {
    if (empty($path)) return $fallback;
    $full = '../' . ltrim($path, '/');
    return file_exists($full) ? $full . '?v=' . filemtime($full) : $fallback;
}

$compliance_framework = [
    ['name' => 'DENR Accredited', 'description' => 'Hazardous Waste Transporter (HWT) & TSDF License', 'badges' => ['HWT-2025-001', 'TSDF-2025-Alpha'], 'icon_color' => 'from-green-500 to-emerald-600', 'badge_color' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
    ['name' => 'DOE Registered', 'description' => 'Oil Industry Participant (OIP)', 'badges' => ['OIP-2025-POWERGIANT'], 'icon_color' => 'from-blue-500 to-cyan-600', 'badge_color' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
    ['name' => 'DOLE Compliant', 'description' => 'OSHS Certified', 'badges' => ['OSHS-2025-001'], 'icon_color' => 'from-purple-500 to-indigo-600', 'badge_color' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
    ['name' => 'ISO Certified', 'description' => 'ISO 9001, 14001, 45001', 'badges' => ['9001', '14001', '45001'], 'icon_color' => 'from-amber-500 to-orange-600', 'badge_color' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300']
];

$compliance_process = [
    ['step' => '1', 'title' => 'Real-Time Monitoring', 'description' => 'IoT sensors on all tankers and facilities.', 'color' => 'from-primary to-secondary'],
    ['step' => '2', 'title' => 'Monthly Audits', 'description' => 'Internal and third-party audits.', 'color' => 'from-green-500 to-emerald-600'],
    ['step' => '3', 'title' => 'Annual Renewal', 'description' => 'All permits renewed 90 days early.', 'color' => 'from-secondary to-amber-600']
];
?>

<script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>
<script src="script.js"></script>
<link rel="stylesheet" href="style.css">

<!-- EXACT SAME SCRIPT FROM ABOUT.PHP - NO ERRORS! -->
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

<style>
    /* Dark Mode Styles - Same as about.php */
    .theme-transition * { 
        transition: background-color .3s ease, color .3s ease, border-color .3s ease; 
    }
    
    @keyframes fadeIn { 
        from { opacity: 0; transform: scale(0.8); } 
        to { opacity: 1; transform: scale(1); } 
    }
    
    .animate-fade-in { 
        animation: fadeIn .3s ease-in-out; 
    }
    
    @keyframes slideDown { 
        from { opacity: 0; transform: translateY(-10px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    .animate-slide-down { 
        animation: slideDown .3s ease-out; 
    }
    
    @keyframes float { 
        0%, 100% { transform: translateY(0); } 
        50% { transform: translateY(-20px); } 
    }
    
    .animate-float { 
        animation: float 6s ease-in-out infinite; 
    }
    
    .animate-pulse-delayed { 
        animation: pulse 3s ease-in-out infinite 1s; 
    }
</style>

<main class="min-h-screen bg-surface dark:bg-gray-900">
    <!-- Hero -->
    <section class="relative py-32 bg-gradient-to-br from-primary via-primary-800 to-secondary-900 overflow-hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        <div class="absolute top-20 left-10 w-48 h-48 bg-secondary/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-32 right-20 w-40 h-40 bg-primary/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-accent/10 rounded-full blur-3xl animate-pulse-delayed"></div>

        <div class="container-custom relative z-10 text-center text-white">
            <h1 class="font-poppins font-bold text-4xl sm:text-5xl lg:text-6xl mb-6 max-w-5xl mx-auto">
                100% Compliant. Always.
            </h1>
            <p class="text-xl max-w-3xl mx-auto text-accent-100 mb-8">
                Fully accredited by DENR, DOE, DOLE, and international standards.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#certificates" class="bg-secondary hover:bg-secondary/90 text-white font-semibold px-8 py-3 rounded-lg transition-all text-lg">View Certificates</a>
                <a href="contact.php" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-semibold px-8 py-3 rounded-lg border-2 border-white/30 hover:border-white/50 transition-all text-lg">Request Audit Report</a>
            </div>
        </div>
    </section>

    <!-- Framework -->
    <section class="section-padding bg-surface dark:bg-gray-900">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl text-secondary-50 dark:text-white mb-6">
                    Regulatory Compliance Framework
                </h2>
                <p class="text-lg text-secondary dark:text-gray-300 max-w-3xl mx-auto">
                    We exceed government and industry standards.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($compliance_framework as $item): ?>
                <div class="compliance-card group cursor-pointer relative overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5 dark:from-primary/10 dark:to-secondary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="p-8 relative z-10 text-center">
                        <div class="w-20 h-20 bg-gradient-to-br <?= $item['icon_color'] ?> rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4Z"/>
                                <path d="M12,8L16,12L12,16L8,12L12,8Z"/>
                            </svg>
                        </div>
                        <h3 class="font-poppins font-bold text-xl text-secondary-50 dark:text-white mb-3"><?= $item['name'] ?></h3>
                        <p class="text-secondary dark:text-gray-400 text-sm mb-4"><?= $item['description'] ?></p>
                        <div class="flex justify-center space-x-2 flex-wrap gap-1">
                            <?php foreach ($item['badges'] as $badge): ?>
                            <span class="px-3 py-1 <?= $item['badge_color'] ?> rounded-full text-xs font-bold"><?= $badge ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Certificates -->
    <section id="certificates" class="section-padding bg-gradient-to-br from-primary/5 to-secondary/5 dark:from-gray-800/50 dark:to-gray-900/50">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl text-secondary-50 dark:text-white mb-6">
                    Official Permits & Certifications
                </h2>
                <p class="text-lg text-secondary dark:text-gray-300 max-w-3xl mx-auto">
                    All documents verified and up-to-date.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($certificates as $c): ?>
                <div class="certificate-item group relative overflow-hidden rounded-2xl shadow-xl bg-white dark:bg-gray-800 border border-accent-200 dark:border-gray-700">
                    <img src="<?= getImageUrl($c['image_path'], 'https://images.pexels.com/photos/1632790/pexels-photo-1632790.jpeg?auto=compress&cs=tinysrgb&w=800') ?>" 
                         alt="<?= htmlspecialchars($c['certificate_name']) ?>" 
                         class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110"
                         onerror="this.src='https://images.pexels.com/photos/1632790/pexels-photo-1632790.jpeg?auto=compress&cs=tinysrgb&w=800';">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 text-white">
                        <h3 class="font-poppins font-bold text-xl mb-2"><?= htmlspecialchars($c['certificate_name']) ?></h3>
                        <p class="text-sm mb-3"><?= htmlspecialchars($c['certificate_description']) ?></p>
                        <p class="text-xs mb-2">Issued by: <?= htmlspecialchars($c['issuing_authority']) ?></p>
                        <p class="text-xs mb-3">Valid until: <?= date('M Y', strtotime($c['expiry_date'])) ?></p>
                        <a href="<?= !empty($c['certificate_file']) ? '../' . $c['certificate_file'] : '#' ?>" target="_blank" class="text-accent-100 font-medium text-sm flex items-center hover:text-white transition-colors">
                            View PDF 
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-12 text-center">
                <a href="#" class="inline-flex items-center text-secondary dark:text-gray-300 font-semibold hover:text-primary dark:hover:text-primary-300 transition-colors">
                    Download Full Compliance Package (PDF)
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10l4 4m0 0l-4 4m4-4H8"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Process -->
    <section class="section-padding bg-surface dark:bg-gray-900">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl text-secondary-50 dark:text-white mb-6">
                    Our Compliance Assurance Process
                </h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <?php foreach ($compliance_process as $p): ?>
                <div class="text-center group">
                    <div class="w-28 h-28 bg-gradient-to-br <?= $p['color'] ?> rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <div class="text-white font-poppins font-bold text-3xl"><?= $p['step'] ?></div>
                    </div>
                    <h3 class="font-poppins font-semibold text-xl text-secondary-50 dark:text-white mb-3"><?= $p['title'] ?></h3>
                    <p class="text-secondary dark:text-gray-300"><?= $p['description'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>