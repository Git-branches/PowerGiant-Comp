<?php 
$page_title = "Our Services";
include 'header.php'; 

// Connect to database and get services content
require_once '../config/database.php';
$database = new Database();
$db = $database->connect();

$stmt = $db->query("SELECT * FROM services_content WHERE is_active = 1 ORDER BY display_order");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define feature lists for each service
$service_features = [
    'Palm Oil Distribution' => [
        'RSPO Certified Sustainable',
        'Bulk & Packaged Options', 
        'ISO 22000 Food Safety'
    ],
    'Coconut Oil Products' => [
        'USDA Organic Certified',
        'Cold-Pressed & Refined',
        'Export Quality Standards'
    ],
    'Transportation & Logistics' => [
        'GPS Real-Time Tracking',
        '100+ Vehicle Fleet',
        '24/7 Dispatch Center'
    ],
    'Hazardous Waste Management' => [
        'DENR Accredited',
        'Emergency Response 24/7', 
        'ISO 14001 Certified'
    ],
    'Furniture Logistics' => [
        'White-Glove Service',
        'Assembly & Installation',
        'Full Insurance Coverage'
    ],
    'Industrial Lubricants' => [
        'API & OEM Approved',
        'Synthetic & Mineral Oils',
        'Technical Support'
    ]
];

// Define button colors for each service
$service_colors = [
    'Palm Oil Distribution' => ['from-amber-500 to-orange-600', 'text-orange-600 hover:text-amber-700'],
    'Coconut Oil Products' => ['from-green-500 to-emerald-600', 'text-emerald-600 hover:text-green-700'],
    'Transportation & Logistics' => ['from-blue-500 to-primary', 'text-blue-600 hover:text-primary'],
    'Hazardous Waste Management' => ['from-red-500 to-orange-600', 'text-red-600 hover:text-orange-700'],
    'Furniture Logistics' => ['from-purple-500 to-indigo-600', 'text-purple-600 hover:text-indigo-700'],
    'Industrial Lubricants' => ['from-gray-500 to-gray-700', 'text-gray-600 hover:text-gray-800']
];
?>

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

<main class="min-h-screen">
    <!-- Hero Banner -->
    <section class="relative h-96 bg-gradient-to-br from-primary via-primary-800 to-secondary-900 overflow-hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
        
        <!-- Floating 3D Elements -->
        <div class="absolute top-20 left-10 w-48 h-48 bg-secondary/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-32 right-20 w-40 h-40 bg-primary/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-accent/10 rounded-full blur-3xl animate-pulse-delayed"></div>
        
        <br><br><div class="container-custom relative z-10 text-center text-white">
            <h1 class="font-poppins font-bold text-4xl sm:text-5xl lg:text-6xl mb-6 max-w-5xl mx-auto">
                Premium Oil & Logistics Solutions
            </h1>
            <p class="text-xl max-w-3xl mx-auto text-accent-100 mb-8">
                From palm oil distribution to specialized furniture logistics — Power Giant RMT delivers excellence, safety, and sustainability across all operations.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#services" class="btn-primary text-lg px-8 py-3">
                    Explore Services
                </a>
                <a href="contact.php" class="btn-secondary text-lg px-8 py-3">
                    Get Quote
                </a>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section id="services" class="section-padding bg-surface">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl secondary-50 mb-6">
                    Our Specialized Services
                </h2>
                <p class="text-lg text-text-secondary max-w-3xl mx-auto">
                    Comprehensive solutions for oil distribution, transportation, and specialized logistics with 20+ years of industry expertise.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($services as $service): 
                    $service_name = $service['service_name'];
                    $gradient_class = $service_colors[$service_name][0] ?? 'from-primary to-secondary';
                    $text_color = $service_colors[$service_name][1] ?? 'text-primary hover:text-secondary';
                    $features = $service_features[$service_name] ?? [];
                ?>
                <div class="card-service group cursor-pointer relative overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="p-8 relative z-10">
                        <div class="w-20 h-20 bg-gradient-to-br <?php echo $gradient_class; ?> rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4Z M15,9L12,12L9,9H11V7H13V9H15Z"/>
                            </svg>
                        </div>
                        <h3 class="font-poppins font-semibold text-xl text-gray-800 mb-4"><?php echo htmlspecialchars($service_name); ?></h3>
                        <p class="text-text-secondary mb-6 text-sm leading-relaxed">
                            <?php echo htmlspecialchars($service['service_description']); ?>
                        </p>
                        
                        <!-- Features List -->
                        <?php if (!empty($features)): ?>
                        <ul class="space-y-3 mb-6">
                            <?php foreach ($features as $feature): ?>
                            <li class="flex items-center text-sm text-text-secondary">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <?php echo htmlspecialchars($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        
                        <!-- Action Button -->
                        <a href="contact.php" class="<?php echo $text_color; ?> font-semibold transition-colors flex items-center">
                            <?php 
                                $button_text = match($service_name) {
                                    'Palm Oil Distribution' => 'Request Quote',
                                    'Coconut Oil Products' => 'Order Now',
                                    'Transportation & Logistics' => 'Book Transport',
                                    'Hazardous Waste Management' => 'Schedule Pickup',
                                    'Furniture Logistics' => 'Get Quote',
                                    'Industrial Lubricants' => 'Order Lubricants',
                                    default => 'Learn More'
                                };
                                echo $button_text;
                            ?>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section-padding bg-gradient-to-br from-primary to-secondary text-white">
        <div class="container-custom">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl md:text-5xl font-poppins font-bold mb-2">20+</div>
                    <div class="text-accent-100">Years Experience</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-poppins font-bold mb-2">500+</div>
                    <div class="text-accent-100">Clients Served</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-poppins font-bold mb-2">100+</div>
                    <div class="text-accent-100">Vehicle Fleet</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-poppins font-bold mb-2">24/7</div>
                    <div class="text-accent-100">Service Support</div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<!-- Progress Bar Animation -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const progressBars = document.querySelectorAll('[style*="width"]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.width = entry.target.style.width || '0%';
                    setTimeout(() => {
                        entry.target.style.transition = 'width 2s ease-out';
                    }, 100);
                }
            });
        }, { threshold: 0.5 });

        progressBars.forEach(bar => observer.observe(bar.parentElement));
    });
</script>