<?php 
$page_title = "Our Projects";
include 'header.php'; 

// Connect to database and get projects content
require_once '../config/database.php';
$database = new Database();
$db = $database->connect();

$stmt = $db->query("SELECT * FROM projects_content WHERE is_active = 1 ORDER BY display_order");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// UPDATED: Helper function for project images
function getProjectImage($imagePath, $fallbackImage = '../css/images/project-waste-plant.jpg') {
    if (empty($imagePath)) {
        return $fallbackImage;
    }
    
    // The editor stores paths like "projects/filename.jpg"
    $fullPath = "../uploads/" . $imagePath;
    
    if (file_exists($fullPath)) {
        return $fullPath;
    }
    
    return $fallbackImage;
}

// Fallback projects data if database is empty
if (empty($projects)) {
    $projects = [
        [
            'project_title' => 'Batangas Hazardous Waste Treatment Plant',
            'project_description' => '50,000 MT/year capacity | DENR TSDF Certified | Zero Spill Record',
            'client_name' => 'San Miguel Corp',
            'project_date' => '2024-01-15',
            'image_path' => '../css/images/project-waste-plant.jpg',
            'status' => 'Completed'
        ],
        [
            'project_title' => 'Cebu Bulk Fuel Terminal Expansion',
            'project_description' => '100,000 KL additional capacity | DOE Approved | Q4 2025 Target',
            'client_name' => 'Petron Corp',
            'project_date' => '2025-12-31',
            'image_path' => '../css/images/project-fuel-depot.jpg',
            'budget' => '₱850M',
            'status' => 'Ongoing'
        ],
        [
            'project_title' => 'Nationwide GPS Fleet Tracking System',
            'project_description' => '200+ tankers | Real-time ETA | 99.9% Uptime',
            'client_name' => 'Internal',
            'project_date' => '2023-06-20',
            'image_path' => '../css/images/project-logistics.jpg',
            'status' => 'Completed'
        ]
    ];
}
?>
<link rel="icon"  href="POWER-GIANT.png">
<script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>
<script src="script.js"></script>
<link rel="stylesheet" href="style.css">
<script>
    // Theme toggle functionality
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        
        // Toggle icons
        document.querySelectorAll('.theme-toggle-icon').forEach(icon => {
            icon.classList.toggle('hidden');
        });
        
        // Save preference to localStorage
        localStorage.setItem('theme', newTheme);
    }
    
    // Initialize theme from localStorage
    function initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        // Set correct icon visibility
        if (savedTheme === 'dark') {
            document.querySelectorAll('.sun-icon').forEach(icon => icon.classList.add('hidden'));
            document.querySelectorAll('.moon-icon').forEach(icon => icon.classList.remove('hidden'));
        } else {
            document.querySelectorAll('.sun-icon').forEach(icon => icon.classList.remove('hidden'));
            document.querySelectorAll('.moon-icon').forEach(icon => icon.classList.add('hidden'));
        }
    }
    
    // Mobile menu functionality
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    });
    
    // Mobile dropdown functionality
    function toggleMobileDropdown(id) {
        const dropdown = document.getElementById(id);
        const arrow = document.getElementById(id.replace('dropdown', 'arrow'));
        
        dropdown.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }
    
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 10) {
            navbar.classList.add('nav-shadow');
        } else {
            navbar.classList.remove('nav-shadow');
        }
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initTheme();
    });
</script>

<main class="min-h-screen bg-surface">
    <!-- Hero Banner -->
    <section class="relative py-32 bg-gradient-to-br from-primary via-primary-800 to-secondary-900 overflow-hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        
        <!-- Floating 3D Elements -->
        <div class="absolute top-20 left-10 w-48 h-48 bg-secondary/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-32 right-20 w-40 h-40 bg-primary/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-accent/10 rounded-full blur-3xl animate-pulse-delayed"></div>

        <div class="container-custom relative z-10 text-center text-white">
            <h1 class="font-poppins font-bold text-4xl sm:text-5xl lg:text-6xl mb-6 max-w-5xl mx-auto">
                Projects That Power Progress
            </h1>
            <p class="text-xl max-w-3xl mx-auto text-accent-100 mb-8">
                From hazardous waste treatment plants to nationwide fuel logistics networks — we deliver excellence on time, on budget, and beyond compliance.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#portfolio" class="btn-primary text-lg px-8 py-3">
                    View Portfolio
                </a>
                <a href="contact.php" class="btn-secondary text-lg px-8 py-3">
                    Start Your Project
                </a>
            </div>
        </div>
    </section>

    <!-- Project Stats -->
    <section class="section-padding bg-surface">
        <div class="container-custom">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="group">
                    <div class="text-4xl md:text-5xl font-poppins font-bold secondary-50 mb-2 group-hover:scale-110 transition-transform duration-300 inline-block">
                        150+
                    </div>
                    <p class="text-text-secondary font-medium">Projects Completed</p>
                </div>
                <div class="group">
                    <div class="text-4xl md:text-5xl font-poppins font-bold secondary-50 mb-2 group-hover:scale-110 transition-transform duration-300 inline-block">
                        12
                    </div>
                    <p class="text-text-secondary font-medium">Active Projects</p>
                </div>
                <div class="group">
                    <div class="text-4xl md:text-5xl font-poppins font-bold secondary-50 mb-2 group-hover:scale-110 transition-transform duration-300 inline-block">
                        98%
                    </div>
                    <p class="text-text-secondary font-medium">On-Time Delivery</p>
                </div>
                <div class="group">
                    <div class="text-4xl md:text-5xl font-poppins font-bold secondary-50 mb-2 group-hover:scale-110 transition-transform duration-300 inline-block">
                        0
                    </div>
                    <p class="text-text-secondary font-medium">Safety Incidents</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Project Categories -->
    <section class="section-padding bg-gradient-to-br from-primary/5 to-secondary/5">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl secondary-50 mb-6">
                    Project Categories
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Hazardous Waste -->
                <div class="project-cat group cursor-pointer relative overflow-hidden rounded-3xl bg-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-secondary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="p-8 relative z-10">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4Z"/>
                                <path d="M12,8L16,12L12,16L8,12L12,8Z"/>
                            </svg>
                        </div>
                        <h3 class="font-poppins font-bold text-xl secondary-50 text-center mb-4">Hazardous Waste Management</h3>
                        <p class="text-text-secondary text-center text-sm mb-6">
                            Treatment plants, transport networks, and emergency response systems.
                        </p>
                        <div class="text-center">
                            <span class="inline-block px-4 py-2 bg-green-100 text-green-700 rounded-full text-xs font-bold">42 Projects</span>
                        </div>
                    </div>
                </div>

                <!-- Fuel Infrastructure -->
                <div class="project-cat group cursor-pointer relative overflow-hidden rounded-3xl bg-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-secondary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="p-8 relative z-10">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z"/>
                                <path d="M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7Z"/>
                            </svg>
                        </div>
                        <h3 class="font-poppins font-bold text-xl secondary-50 text-center mb-4">Fuel Infrastructure</h3>
                        <p class="text-text-secondary text-center text-sm mb-6">
                            Depots, pipelines, marine terminals, and retail stations.
                        </p>
                        <div class="text-center">
                            <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">58 Projects</span>
                        </div>
                    </div>
                </div>

                <!-- Logistics & Transport -->
                <div class="project-cat group cursor-pointer relative overflow-hidden rounded-3xl bg-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-secondary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="p-8 relative z-10">
                        <div class="w-20 h-20 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V5H19V19Z"/>
                                <path d="M7,7H17V9H7V7Z"/>
                                <path d="M7,11H17V13H7V11Z"/>
                                <path d="M7,15H12V17H7V15Z"/>
                            </svg>
                        </div>
                        <h3 class="font-poppins font-bold text-xl secondary-50 text-center mb-4">Logistics & Transport</h3>
                        <p class="text-text-secondary text-center text-sm mb-6">
                            Fleet expansion, GPS tracking systems, and delivery networks.
                        </p>
                        <div class="text-center">
                            <span class="inline-block px-4 py-2 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">50 Projects</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Projects Portfolio -->
    <section id="portfolio" class="section-padding bg-surface">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl secondary-50 mb-6">
                    Featured Projects
                </h2>
                <p class="text-lg text-text-secondary max-w-3xl mx-auto">
                    Real-world impact through engineering excellence and sustainable solutions.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($projects as $project): 
                    // UPDATED: Use the helper function to get proper image path
                    $projectImage = getProjectImage($project['image_path'] ?? '');
                ?>
                <div class="project-card group relative overflow-hidden rounded-3xl shadow-xl bg-white border border-accent-100 hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4">
                    <div class="relative h-64 overflow-hidden">
                        <img src="<?php echo $projectImage; ?>" 
                             alt="<?php echo htmlspecialchars($project['project_title']); ?>" 
                             class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                             onerror="this.src='https://images.pexels.com/photos/1267338/pexels-photo-1267338.jpeg?auto=compress&cs=tinysrgb&w=800';">
                        <div class="absolute top-4 right-4 <?php echo (isset($project['status']) && $project['status'] === 'Ongoing') ? 'bg-primary' : 'bg-success'; ?> text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                            <?php echo isset($project['status']) ? $project['status'] : ((!empty($project['project_date']) && strtotime($project['project_date']) > strtotime('-6 months')) ? 'Ongoing' : 'Completed'); ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-poppins font-bold text-xl secondary-50 mb-2"><?php echo htmlspecialchars($project['project_title']); ?></h3>
                        <p class="text-text-secondary text-sm mb-4">
                            <?php echo htmlspecialchars($project['project_description']); ?>
                        </p>
                        <div class="flex justify-between text-xs text-text-secondary mb-4">
                            <?php if (!empty($project['client_name'])): ?>
                            <span><strong>Client:</strong> <?php echo htmlspecialchars($project['client_name']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($project['project_date'])): ?>
                            <span><strong>Year:</strong> <?php echo date('Y', strtotime($project['project_date'])); ?></span>
                            <?php elseif (isset($project['budget'])): ?>
                            <span><strong>Budget:</strong> <?php echo htmlspecialchars($project['budget']); ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="#" class="text-secondary font-semibold hover:text-primary transition-colors flex items-center text-sm">
                            <?php echo (isset($project['status']) && $project['status'] === 'Ongoing') ? 'Track Progress' : 'View Case Study'; ?> 
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-12 text-center">
                <a href="contact.php" class="inline-block bg-gradient-to-r from-primary to-secondary text-white font-poppins font-semibold px-8 py-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                    Discuss Your Project
                </a>
            </div>
        </div>
    </section>

    <!-- Project Timeline (Interactive) -->
    <section class="section-padding bg-gradient-to-br from-primary/5 to-secondary/5">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl secondary-50 mb-6">
                    Project Timeline
                </h2>
                <p class="text-lg text-text-secondary max-w-3xl mx-auto">
                    From concept to completion — transparency at every step.
                </p>
            </div>

            <div class="relative max-w-5xl mx-auto">
                <div class="absolute left-1/2 transform -translate-x-1/2 w-1 bg-accent-200 h-full"></div>
                
                <!-- Milestone 1 -->
                <div class="flex justify-center items-center mb-12 relative">
                    <div class="w-1/2 pr-12 text-right">
                        <div class="bg-white p-6 rounded-2xl shadow-lg border border-accent-100">
                            <h4 class="font-poppins font-semibold text-lg secondary-50">Planning & Permits</h4>
                            <p class="text-sm text-text-secondary">DOE, DENR, LGU Approvals</p>
                        </div>
                    </div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-12 h-12 bg-primary rounded-full flex items-center justify-center shadow-xl">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2Z"/>
                        </svg>
                    </div>
                    <div class="w-1/2 pl-12">
                        <div class="text-sm text-text-secondary font-medium">Month 1-3</div>
                    </div>
                </div>

                <!-- Milestone 2 -->
                <div class="flex justify-center items-center mb-12 relative">
                    <div class="w-1/2 pr-12">
                        <div class="text-sm text-text-secondary font-medium text-right">Month 4-8</div>
                    </div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-12 h-12 bg-secondary rounded-full flex items-center justify-center shadow-xl">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2Z"/>
                        </svg>
                    </div>
                    <div class="w-1/2 pl-12 text-left">
                        <div class="bg-white p-6 rounded-2xl shadow-lg border border-accent-100">
                            <h4 class="font-poppins font-semibold text-lg secondary-50">Construction & Installation</h4>
                            <p class="text-sm text-text-secondary">Civil works, tank fabrication, piping</p>
                        </div>
                    </div>
                </div>

                <!-- Milestone 3 -->
                <div class="flex justify-center items-center relative">
                    <div class="w-1/2 pr-12 text-right">
                        <div class="bg-white p-6 rounded-2xl shadow-lg border border-accent-100">
                            <h4 class="font-poppins font-semibold text-lg secondary-50">Testing & Commissioning</h4>
                            <p class="text-sm text-text-secondary">Leak tests, calibration, safety drills</p>
                        </div>
                    </div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-12 h-12 bg-success rounded-full flex items-center justify-center shadow-xl">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="w-1/2 pl-12">
                        <div class="text-sm text-text-secondary font-medium">Month 9-12</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<!-- Custom Styles -->
<style>
    .project-card img {
        transition: transform 1s ease;
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    .animate-pulse-delayed {
        animation: pulse 3s ease-in-out infinite 1s;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
</style>