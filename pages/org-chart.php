<?php 
$page_title = "org-chart";
include 'header.php'; 
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


<main id="org" class="min-h-screen bg-surface">
    <!-- Hero Section -->
    <section class="relative py-24 bg-gradient-to-br from-primary via-primary-700 to-secondary-900 overflow-hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        
        <!-- Floating 3D Orbs -->
        <div class="absolute top-20 left-10 w-40 h-40 bg-secondary/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-32 right-20 w-32 h-32 bg-primary/30 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-accent/20 rounded-full blur-3xl animate-pulse-delayed"></div>

        <div class="container-custom relative z-10 text-center text-white">
            <h1 class="font-poppins font-bold text-4xl sm:text-5xl lg:text-6xl mb-6">
                Our Leadership Structure
            </h1>
            <p class="text-xl max-w-4xl mx-auto text-accent-100">
                A dedicated team of industry experts driving innovation, safety, and sustainability in oil products, logistics, and environmental services.
            </p>
        </div>
    </section>

    <!-- Organizational Chart -->
    <section class="section-padding bg-surface">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl secondary-50 mb-6">
                    Organizational Hierarchy
                </h2>
                <p class="text-lg text-text-secondary max-w-3xl mx-auto">
                    Structured for efficiency, accountability, and excellence across all operations.
                </p>
            </div>

            <div class="org-chart mx-auto max-w-7xl">
                <!-- CEO -->
                <div class="org-node ceo group">
                    <div class="org-card bg-gradient-to-br from-primary to-secondary text-white shadow-2xl">
                        <div class="org-photo">
                            <img src="../css/images/ceo.jpg" alt="CEO" class="w-full h-full object-cover rounded-full" 
                                 onerror="this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';">
                        </div>
                        <div class="org-info">
                            <h3 class="font-poppins font-bold text-lg">Engr. Ramon M. Torres</h3>
                            <p class="text-sm opacity-90">Chief Executive Officer</p>
                            <p class="text-xs mt-1 opacity-80">30+ Years in Oil & Energy</p>
                        </div>
                    </div>
                    <div class="org-connector from-ceo"></div>
                </div>

                <!-- Executive Level -->
                <div class="org-level flex justify-center gap-8 mt-20 flex-wrap">
                    <!-- COO -->
                    <div class="org-node coo group flex-1 max-w-xs">
                        <div class="org-connector to-coo"></div>
                        <div class="org-card bg-white shadow-xl border border-accent-200 group-hover:shadow-2xl transition-all duration-500">
                            <div class="org-photo">
                                <img src="../css/images/coo.jpg" alt="COO" class="w-full h-full object-cover rounded-full"
                                     onerror="this.src='https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';">
                            </div>
                            <div class="org-info text-center">
                                <h3 class="font-poppins font-semibold text-lg secondary-50">Maria Santos</h3>
                                <p class="text-sm text-text-secondary">Chief Operating Officer</p>
                                <p class="text-xs text-text-secondary mt-1">Operations & Logistics</p>
                            </div>
                        </div>
                    </div>

                    <!-- CFO -->
                    <div class="org-node cfo group flex-1 max-w-xs">
                        <div class="org-connector to-cfo"></div>
                        <div class="org-card bg-white shadow-xl border border-accent-200 group-hover:shadow-2xl transition-all duration-500">
                            <div class="org-photo">
                                <img src="../css/images/cfo.jpg" alt="CFO" class="w-full h-full object-cover rounded-full"
                                     onerror="this.src='https://images.unsplash.com/photo-1556157382-97eda2d8aa89?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';">
                            </div>
                            <div class="org-info text-center">
                                <h3 class="font-poppins font-semibold text-lg secondary-50">Jose Lim</h3>
                                <p class="text-sm text-text-secondary">Chief Financial Officer</p>
                                <p class="text-xs text-text-secondary mt-1">Finance & Investments</p>
                            </div>
                        </div>
                    </div>

                    <!-- CTO -->
                    <div class="org-node cto group flex-1 max-w-xs">
                        <div class="org-connector to-cto"></div>
                        <div class="org-card bg-white shadow-xl border border-accent-200 group-hover:shadow-2xl transition-all duration-500">
                            <div class="org-photo">
                                <img src="../css/images/cto.jpg" alt="CTO" class="w-full h-full object-cover rounded-full"
                                     onerror="this.src='https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';">
                            </div>
                            <div class="org-info text-center">
                                <h3 class="font-poppins font-semibold text-lg secondary-50">Dr. Ana Reyes</h3>
                                <p class="text-sm text-text-secondary">Chief Technology Officer</p>
                                <p class="text-xs text-text-secondary mt-1">R&D and Innovation</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Heads -->
                <div class="org-level grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-20">
                    <!-- Operations -->
                    <div class="org-node dept group">
                        <div class="org-connector to-ops"></div>
                        <div class="org-card bg-gradient-to-br from-primary/10 to-secondary/10 border border-primary/20 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="org-icon">
                                <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V5H19V19Z"/>
                                    <path d="M7,7H17V9H7V7Z"/>
                                    <path d="M7,11H17V13H7V11Z"/>
                                    <path d="M7,15H12V17H7V15Z"/>
                                </svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-sm secondary-50">Operations</h4>
                            <p class="text-xs text-text-secondary mt-1">Fleet & Logistics</p>
                        </div>
                    </div>

                    <!-- Safety & Compliance -->
                    <div class="org-node dept group">
                        <div class="org-connector to-safety"></div>
                        <div class="org-card bg-gradient-to-br from-success/10 to-emerald-100 border border-success/20 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="org-icon">
                                <svg class="w-8 h-8 text-success" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,2L1,21H23M12,6L19,20H5L12,6Z"/>
                                    <path d="M12,10V14M12,16V18"/>
                                </svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-sm secondary-50">Safety</h4>
                            <p class="text-xs text-text-secondary mt-1">Compliance & Risk</p>
                        </div>
                    </div>

                    <!-- Environment -->
                    <div class="org-node dept group">
                        <div class="org-connector to-env"></div>
                        <div class="org-card bg-gradient-to-br from-green-100 to-emerald-100 border border-green-300 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="org-icon">
                                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z"/>
                                    <path d="M12,8L16,12L12,16L8,12L12,8Z"/>
                                </svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-sm secondary-50">Environment</h4>
                            <p class="text-xs text-text-secondary mt-1">Sustainability</p>
                        </div>
                    </div>

                    <!-- Sales & Marketing -->
                    <div class="org-node dept group">
                        <div class="org-connector to-sales"></div>
                        <div class="org-card bg-gradient-to-br from-orange-100 to-amber-100 border border-orange-300 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="org-icon">
                                <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4Z"/>
                                    <path d="M12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17A5,5 0 0,0 17,12A5,5 0 0,0 12,7Z"/>
                                </svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-sm secondary-50">Sales</h4>
                            <p class="text-xs text-text-secondary mt-1">Marketing & Growth</p>
                        </div>
                    </div>

                    <!-- HR & Admin -->
                    <div class="org-node dept group">
                        <div class="org-connector to-hr"></div>
                        <div class="org-card bg-gradient-to-br from-purple-100 to-indigo-100 border border-purple-300 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="org-icon">
                                <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,16.06 20,18V20H4V18C4,16.06 7.58,14 12,14Z"/>
                                </svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-sm secondary-50">HR</h4>
                            <p class="text-xs text-text-secondary mt-1">People & Culture</p>
                        </div>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="mt-16 text-center">
                    <div class="inline-block bg-gradient-to-r from-primary to-secondary p-1 rounded-full">
                        <div class="bg-white rounded-full px-8 py-4">
                            <p class="font-poppins font-semibold text-lg secondary-50">
                                150+ Dedicated Team Members Nationwide
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Values -->
    <section class="section-padding bg-gradient-to-br from-primary/5 to-secondary/5">
        <div class="container-custom">
            <div class="text-center mb-12">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl secondary-50 mb-6">
                    Our Leadership Principles
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="w-24 h-24 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-xl">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4Z"/>
                            <path d="M12,8L16,12L12,16L8,12L12,8Z"/>
                        </svg>
                    </div>
                    <h3 class="font-poppins font-semibold text-xl secondary-50 mb-3">Integrity First</h3>
                    <p class="text-text-secondary">We uphold transparency, ethics, and accountability in every decision.</p>
                </div>

                <div class="text-center group">
                    <div class="w-24 h-24 bg-gradient-to-br from-success to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-xl">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z"/>
                            <path d="M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8Z"/>
                        </svg>
                    </div>
                    <h3 class="font-poppins font-semibold text-xl secondary-50 mb-3">Safety Above All</h3>
                    <p class="text-text-secondary">Zero-incident culture through training, technology, and vigilance.</p>
                </div>

                <div class="text-center group">
                    <div class="w-24 h-24 bg-gradient-to-br from-secondary to-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-xl">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2L1,21H23M12,6L19,20H5L12,6Z"/>
                            <path d="M12,10V14M12,16V18"/>
                        </svg>
                    </div>
                    <h3 class="font-poppins font-semibold text-xl secondary-50 mb-3">Innovation Driven</h3>
                    <p class="text-text-secondary">Pioneering green tech and smart logistics for a sustainable future.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<!-- Custom CSS for Org Chart -->
<style>
    .org-chart {
        position: relative;
    }

    .org-node {
        position: relative;
        text-align: center;
    }

    .org-card {
        border-radius: 1.5rem;
        overflow: hidden;
        transition: all 0.4s ease;
        position: relative;
    }

    .org-photo {
        width: 80px;
        height: 80px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .org-info {
        padding: 0 1rem 1rem;
    }

    .org-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Connectors */
    .org-connector {
        position: absolute;
        width: 2px;
        background: linear-gradient(to bottom, transparent, #ccc);
        left: 50%;
        transform: translateX(-50%);
        z-index: 1;
    }

    .from-ceo {
        height: 60px;
        top: 100%;
    }

    .to-coo, .to-cfo, .to-cto {
        height: 60px;
        bottom: 100%;
    }

    .to-ops, .to-safety, .to-env, .to-sales, .to-hr {
        height: 50px;
        bottom: 100%;
    }

    @media (max-width: 768px) {
        .org-level {
            flex-direction: column;
            align-items: center;
        }
        .org-node {
            margin-bottom: 3rem;
        }
        .org-connector {
            display: none;
        }
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