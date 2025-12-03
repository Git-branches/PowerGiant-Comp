<!DOCTYPE html>
<html lang="en" data-theme="light"> <!-- Added data-theme for dark mode -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Giant RMT<?php echo isset($page_title) ? ' | ' . $page_title : ' - Delivering the Future of Oil Solutions'; ?></title>
    <meta name="description" content="Premium fuel and energy solutions with 20+ years of proven reliability, nationwide distribution network, and commitment to sustainability.">
    <meta name="keywords" content="fuel distribution, industrial lubricants, marine fuel, aviation fuel, energy solutions, Philippines">
    
    <!-- Open Graph Meta Tags -->
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="POWER-GIANT.png"> <!-- Fixed type attribute -->
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- TAILWIND CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- TAILWIND CONFIG -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#002B5B',
                        secondary: '#FF6B00',
                        accent: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                        }
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Power Giant RMT",
        "url": "https://powergiantrmt.com",
        "logo": "https://powergiantrmt.com/POWER-GIANT.png",
        "description": "Premium fuel and energy solutions with 20+ years of proven reliability",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Glamang Prk-3, Miravilla Homes",
            "addressLocality": "Polomolok",
            "addressRegion": "South Cotabato",
            "addressCountry": "PH"
        },
        "telephone": "+63-962-895-5759",
        "email": "powergiantrmt@gmail.com"
    }
    </script>

    <!-- Rocket Scripts (if needed) -->
    <script type="module" src="https://static.rocket.new/rocket-web.js?_cfg=https%3A%2F%2Fpowergiantrmt.builtwithrocket.new&_be=https%3A%2F%2Fapplication.rocket.new&_v=0.1.9"></script>
    <script type="module" src="https://static.rocket.new/rocket-shot.js?v=0.0.1"></script>
</head>
<body class="font-poppins text-text-primary bg-surface min-h-screen">

    <!-- Navigation -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-accent-200 transition-all duration-300">
        <div class="container-custom">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <a href="index.php">
                    <div class="flex items-center space-x-3">
                        <img src="POWER-GIANT.png" 
                             alt="Power Giant RMT Logo" 
                             class="w-12 h-12 rounded-lg object-cover shadow-lg"
                             onerror="this.src='https://via.placeholder.com/48x48/002B5B/FF6B00?text=PG'; this.onerror=null;">
                        <span class="font-poppins font-bold text-xl secondary-50">POWER GIANT RMT</span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-text-secondary hover:text-primary transition-colors duration-200 font-medium">Home</a>

                    <!-- Dropdown: Company -->
                    <div class="relative group">
                        <button class="text-text-secondary hover:text-primary transition-colors duration-200 font-medium flex items-center space-x-1">
                            <span>Company</span>
                            <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 border border-accent-200">
                            <a href="about.php" class="block px-5 py-3 text-text-secondary hover:text-primary hover:bg-accent-50 transition-colors">About Us</a>
                           <!----<a href="org-chart.php" class="block px-5 py-3 text-text-secondary hover:text-primary hover:bg-accent-50 transition-colors">Organizational Chart</a> -->
                            <a href="compliance.php" class="block px-5 py-3 text-text-secondary hover:text-primary hover:bg-accent-50 transition-colors">Compliance & Permits</a>
                        </div>
                    </div>

                    <a href="services.php" class="text-text-secondary hover:text-primary transition-colors duration-200 font-medium">Services</a>
                    <a href="projects.php" class="text-text-secondary hover:text-primary transition-colors duration-200 font-medium">Projects</a>
                    <a href="news.php" class="text-text-secondary hover:text-primary transition-colors duration-200 font-medium">News & Updates</a>
                    <a href="contact.php" class="text-text-secondary hover:text-primary transition-colors duration-200 font-medium">Contact Us</a>

                    <!-- Theme Toggle -->
                    <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                        <svg class="theme-toggle-icon sun-icon w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                        <svg class="theme-toggle-icon moon-icon w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </div>

                    <a href="tel:09628955759" class="bg-secondary text-white px-5 py-2 rounded-lg font-semibold hover:bg-primary transition-colors">Call Now</a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <div class="theme-toggle" onclick="toggleTheme()">
                        <svg class="theme-toggle-icon sun-icon w-5 h-5" fill="currentColor" viewBox="0 0 20 20">...</svg>
                        <svg class="theme-toggle-icon moon-icon w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">...</svg>
                    </div>
                    <button id="mobile-menu-btn" class="p-2 rounded-lg hover:bg-accent-100">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden bg-white dark:bg-gray-900 border-t border-accent-200">
                <div class="px-4 py-4 space-y-3">
                    <a href="index.php" class="block text-text-secondary hover:text-primary font-medium">Home</a>

                    <details class="border-b pb-2">
                        <summary class="font-semibold cursor-pointer text-text-secondary hover:text-primary">Company</summary>
                        <div class="pl-4 mt-2 space-y-2">
                            <a href="about.php" class="block text-sm">About Us</a>
                            <!--<a href="org-chart.php" class="block text-sm">Org Chart</a>-->
                            <a href="compliance.php" class="block text-sm">Compliance</a>
                        </div>
                    </details>

                    <a href="services.php" class="block text-text-secondary hover:text-primary font-medium">Services</a>
                    <a href="projects.php" class="block text-text-secondary hover:text-primary font-medium">Projects</a>
                    <a href="news.php" class="block text-text-secondary hover:text-primary font-medium">News & Updates</a>
                    <a href="contact.php" class="block text-text-secondary hover:text-primary font-medium">Contact Us</a>
                    <a href="tel:09628955759" class="bg-secondary text-white px-4 py-2 rounded-lg text-center block mt-4">Call Now</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Starts Below Navbar -->
    <main class="pt-20">
<script>
    
</script>
<script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>
<script src="script.js"></script>
<link rel="stylesheet" href="style.css">