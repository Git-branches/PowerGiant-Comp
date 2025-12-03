<?php 
$page_title = "Home";
include 'header.php'; 

// Connect to database
require_once '../config/database.php';
$database = new Database();
$db = $database->connect();

// Get hero content
$stmt = $db->query("SELECT * FROM homepage_hero WHERE is_active = 1 LIMIT 1");
$hero = $stmt->fetch(PDO::FETCH_ASSOC);

// Get stats
$stmt = $db->query("SELECT * FROM homepage_stats WHERE is_active = 1 ORDER BY display_order");
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get testimonials
$stmt = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order LIMIT 5");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get partners
$stmt = $db->query("SELECT * FROM partners WHERE is_active = 1 ORDER BY display_order");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// UPDATED: Simplified image path helper for hero images
function getHeroImageUrl($imagePath, $fallbackIndex = 1) {
    if (empty($imagePath)) {
        return "../css/images/truck{$fallbackIndex}.jpg";
    }
    
    $fullPath = "../uploads/" . $imagePath;
    
    if (file_exists($fullPath)) {
        return $fullPath . '?v=' . filemtime($fullPath);
    }
    
    return "../css/images/truck{$fallbackIndex}.jpg";
}

// UPDATED: Helper for testimonial avatars
function getTestimonialAvatar($avatarPath, $authorName) {
    if (empty($avatarPath)) {
        return null;
    }
    
    $fullPath = "../uploads/" . $avatarPath;
    
    if (file_exists($fullPath)) {
        return $fullPath;
    }
    
    return null;
}

// UPDATED: Helper for partner logos
function getPartnerLogo($logoPath, $partnerName) {
    if (empty($logoPath)) {
        return null;
    }
    
    $fullPath = "../uploads/" . $logoPath;
    
    if (file_exists($fullPath)) {
        return $fullPath;
    }
    
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Power Giant RMT'); ?></title>
    <link rel="stylesheet" href="style.css">
    <script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>

    <!-- OIL TRUCK LOADER STYLES -->
    <style>
    /* === OIL TRUCK LOADER - FULL COPY FROM login.php === */
    #loader-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0f172a 0%, #1a365d 50%, #0f172a 100%);
        transition: opacity 0.5s ease;
    }
    .loader-bg {
        position: absolute;
        inset: 0;
        background: inherit;
        backdrop-filter: blur(10px);
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .loader-container {
        position: relative;
        z-index: 10;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .road {
        width: 500px;
        height: 12px;
        background: linear-gradient(90deg,
            transparent 0%,
            rgba(148, 163, 184, 0.4) 10%,
            rgba(148, 163, 184, 0.7) 50%,
            rgba(148, 163, 184, 0.4) 90%,
            transparent 100%);
        border-radius: 6px;
        position: relative;
        margin-top: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    .road-line {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg,
            transparent 0%,
            #f8fafc 10%,
            #f8fafc 50%,
            #f8fafc 90%,
            transparent 100%);
        background-size: 40px 100%;
        animation: roadMove 1s linear infinite;
    }
    @keyframes roadMove {
        from { background-position: 0 0; }
        to { background-position: 40px 0; }
    }
    .oil-truck {
        width: 280px;
        height: 120px;
        position: relative;
        margin-bottom: 5px;
        animation: truckBounce 0.5s ease-in-out infinite;
    }
    @keyframes truckBounce {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-2px); }
    }
    .truck-cabin {
        position: absolute;
        left: 20px;
        bottom: 5px;
        width: 60px;
        height: 50px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 8px 8px 4px 4px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        z-index: 2;
    }
    .truck-window {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 25px;
        height: 20px;
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        border-radius: 4px;
        opacity: 0.8;
    }
    .truck-window::before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        height: 50%;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }
    .oil-tanker {
        position: absolute;
        left: 80px;
        bottom: 5px;
        width: 180px;
        height: 70px;
        background: linear-gradient(180deg, #71717a 0%, #52525b 100%);
        border-radius: 35px;
        overflow: hidden;
        box-shadow: inset 0 -5px 10px rgba(0, 0, 0, 0.3), 0 4px 10px rgba(0, 0, 0, 0.3);
        z-index: 1;
    }
    .oil-liquid {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 75%;
        background: linear-gradient(180deg, #06b6d4 0%, #0d9488 100%);
        animation: oilSlosh 1s ease-in-out infinite;
        transform-origin: bottom;
        clip-path: ellipse(100% 100% at 50% 100%);
    }
    @keyframes oilSlosh {
        0%, 100% { clip-path: ellipse(100% 100% at 50% 100%); }
        25% { clip-path: ellipse(95% 105% at 45% 100%); }
        50% { clip-path: ellipse(100% 100% at 50% 100%); }
        75% { clip-path: ellipse(95% 105% at 55% 100%); }
    }
    .oil-wave {
        position: absolute;
        top: -20px;
        left: -50%;
        width: 200%;
        height: 40px;
        background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        animation: waveMove 2s ease-in-out infinite;
    }
    @keyframes waveMove {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(10%) rotate(2deg); }
    }
    .tanker-stripe {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 10px;
        right: 10px;
        height: 2px;
        background: rgba(255, 255, 255, 0.2);
    }
    .tanker-cap {
        position: absolute;
        top: 8px;
        right: 20px;
        width: 12px;
        height: 12px;
        background: #3f3f46;
        border-radius: 50%;
        border: 2px solid #71717a;
    }
    .wheel {
        position: absolute;
        bottom: -8px;
        width: 28px;
        height: 28px;
        background: radial-gradient(circle, #27272a 0%, #18181b 70%, #09090b 100%);
        border-radius: 50%;
        border: 3px solid #52525b;
        animation: wheelRotate 0.5s linear infinite;
        z-index: 3;
    }
    .wheel::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 12px;
        height: 12px;
        background: #71717a;
        border-radius: 50%;
    }
    .oil-truck .wheel::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 2px;
        background: #52525b;
    }
    @keyframes wheelRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .wheel-1 { left: 40px; }
    .wheel-2 { left: 100px; }
    .wheel-3 { left: 230px; }
    .exhaust {
        position: absolute;
        left: 15px;
        bottom: 45px;
        width: 8px;
        height: 8px;
        z-index: 2;
    }
    .smoke {
        position: absolute;
        width: 8px;
        height: 8px;
        background: rgba(148, 163, 184, 0.4);
        border-radius: 50%;
        animation: smokeRise 2s ease-out infinite;
    }
    .smoke:nth-child(1) { animation-delay: 0s; }
    .smoke:nth-child(2) { animation-delay: 0.5s; }
    .smoke:nth-child(3) { animation-delay: 1s; }
    @keyframes smokeRise {
        0% { transform: translateY(0) scale(1); opacity: 0.6; }
        100% { transform: translateY(-50px) scale(2); opacity: 0; }
    }
    .loader-text {
        color: #06b6d4;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 2px;
        animation: textPulse 1.5s ease-in-out infinite;
        margin-top: 25px;
    }
    @keyframes textPulse {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }
    .loader-subtext {
        color: #64748b;
        font-size: 14px;
        margin-top: 10px;
        animation: fadeInOut 2s ease-in-out infinite;
    }
    @keyframes fadeInOut {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.8; }
    }
    .progress-container {
        width: 300px;
        height: 4px;
        background: rgba(148, 163, 184, 0.2);
        border-radius: 2px;
        margin: 20px auto 0;
        overflow: hidden;
    }
    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #0d9488, #06b6d4);
        border-radius: 2px;
        width: 0%;
        transition: width 0.1s ease;
    }
    @media (max-width: 640px) {
        .road { width: 300px; }
        .oil-truck { width: 200px; transform: scale(0.8); margin-bottom: 0; }
    }
    </style>

    <!-- YOUR EXISTING STYLES -->
    <style>
    /* Fixed Dark Mode */
    [data-theme="dark"] {
        --color-primary: #002B5B;
        --color-secondary: #FF6B00;
        --color-text-primary: #ffffff;
        --color-text-secondary: #d1d5db;
        --color-surface: #1f2937;
        --color-accent-100: #374151;
        --color-accent-200: #4b5563;
    }
    [data-theme="dark"] .bg-surface { background-color: var(--color-surface); }
    [data-theme="dark"] .text-text-primary { color: var(--color-text-primary); }
    [data-theme="dark"] .text-text-secondary { color: var(--color-text-secondary); }
    [data-theme="dark"] .bg-white { background-color: #374151; }
    [data-theme="dark"] .text-gray-800 { color: #f9fafb; }
    [data-theme="dark"] .text-gray-700 { color: #e5e7eb; }
    [data-theme="dark"] .text-gray-600 { color: #d1d5db; }
    [data-theme="dark"] .border-gray-200 { border-color: #4b5563; }
    [data-theme="dark"] .bg-gray-50 { background-color: #374151; }

    /* Hero Slider Styles */
    .hero-slider { position: relative; width: 100%; height: 100vh; overflow: hidden; }
    .slider-container { position: relative; width: 100%; height: 100%; }
    .slider-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out; }
    .slider-slide.active { opacity: 1; }
    .slider-slide img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .slider-dots { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10; }
    .slider-dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255, 255, 255, 0.5); cursor: pointer; transition: background 0.3s ease; }
    .slider-dot.active { background: #FF6B00; }

    /* Testimonial Carousel */
    .testimonial-carousel-wrapper { position: relative; width: 100vw; margin-left: calc(-50vw + 50%); overflow: hidden; padding: 40px 0; background: transparent; }
    .testimonial-carousel { display: flex; gap: 30px; animation: scroll-left 40s linear infinite; will-change: transform; width: max-content; }
    .testimonial-carousel:hover { animation-play-state: paused; }
    @keyframes scroll-left { 0% { transform: translateX(0); } 100% { transform: translateX(-33.333%); } }
    .testimonial-item { flex: 0 0 380px; width: 380px; min-width: 380px; }
    .testimonial-card { background: white; border-radius: 16px; padding: 35px 30px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); text-align: center; height: 450px; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .testimonial-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15); }
    [data-theme="dark"] .testimonial-card { background: #374151; color: white; }
    .testimonial-avatar { width: 85px; height: 85px; border-radius: 50%; object-fit: cover; border: 4px solid #FF6B00; flex-shrink: 0; }
    .testimonial-info h4 { font-size: 1.25rem; font-weight: 700; margin-bottom: 5px; color: #1f2937; }
    [data-theme="dark"] .testimonial-info h4 { color: white; }
    .testimonial-info p { color: #6b7280; margin-bottom: 3px; font-size: 0.95rem; }
    [data-theme="dark"] .testimonial-info p { color: #d1d5db; }
    .testimonial-company { color: #FF6B00 !important; font-weight: 600; font-size: 0.9rem; }
    .testimonial-text { font-size: 1rem; line-height: 1.7; color: #4b5563; margin: 20px 0; font-style: italic; flex-grow: 1; display: flex; align-items: center; justify-content: center; padding: 0 10px; }
    [data-theme="dark"] .testimonial-text { color: #e5e7eb; }
    .testimonial-rating { display: flex; justify-content: center; gap: 5px; margin-top: 15px; }
    .testimonial-rating svg { width: 20px; height: 20px; }

    /* Animations */
    .animate-fade-in { animation: fadeIn 1s ease-out; }
    .animate-slide-up { animation: slideUp 1s ease-out 0.3s both; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 768px) {
        .testimonial-carousel-wrapper { width: 100vw; margin-left: calc(-50vw + 50%); }
        .testimonial-carousel { gap: 20px; animation: scroll-left 30s linear infinite; }
        .testimonial-item { flex: 0 0 300px; width: 300px; min-width: 300px; }
        .testimonial-card { padding: 28px 22px; height: 420px; }
        .testimonial-avatar { width: 70px; height: 70px; }
        .testimonial-info h4 { font-size: 1.1rem; }
        .testimonial-text { font-size: 0.95rem; }
        .hero-content h1 { font-size: 2.5rem !important; }
    }
    @media (max-width: 480px) {
        .testimonial-carousel { gap: 15px; animation: scroll-left 25s linear infinite; }
        .testimonial-item { flex: 0 0 280px; width: 280px; min-width: 280px; }
        .testimonial-card { padding: 25px 20px; height: 400px; }
        .testimonial-avatar { width: 65px; height: 65px; }
        .testimonial-text { font-size: 0.9rem; }
    }
    </style>
</head>
<body class="min-h-screen">

    <!-- OIL TRUCK LOADER -->
    <div id="loader-overlay">
        <div class="loader-bg"></div>
        <div class="loader-container">
            <div class="oil-truck">
                <div class="truck-cabin"><div class="truck-window"></div></div>
                <div class="oil-tanker">
                    <div class="oil-liquid"><div class="oil-wave"></div></div>
                    <div class="tanker-stripe"></div>
                    <div class="tanker-cap"></div>
                </div>
                <div class="wheel wheel-1"></div>
                <div class="wheel wheel-2"></div>
                <div class="wheel wheel-3"></div>
                <div class="exhaust">
                    <div class="smoke"></div>
                    <div class="smoke"></div>
                    <div class="smoke"></div>
                </div>
            </div>
            <div class="road"><div class="road-line"></div></div>
            <div class="loader-text">LOADING POWER GIANT RMT</div>
            <div class="loader-subtext">Fueling up your experience...</div>
            <div class="progress-container">
                <div class="progress-bar" id="progressFill"></div>
            </div>
        </div>
    </div>

    <main class="min-h-screen">
        <!-- Hero Section with Working Slider -->
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <div class="hero-slider">
                    <div class="slider-container" id="heroSlider">
                        <?php if (!empty($hero) && !empty($hero['background_images'])): 
                            $images = json_decode($hero['background_images'], true);
                            foreach ($images as $index => $image): 
                                $imageUrl = getHeroImageUrl($image, ($index % 4) + 1);
                        ?>
                            <div class="slider-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo $imageUrl; ?>" 
                                     alt="Power Giant RMT Operations"
                                     loading="lazy"
                                     onerror="this.src='../css/images/truck<?php echo ($index % 4) + 1; ?>.jpg'">
                            </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <div class="slider-slide active"><img src="../css/images/truck4.jpg" alt="Modern industrial oil refinery" loading="lazy"></div>
                            <div class="slider-slide"><img src="../css/images/truck1.jpg" alt="Oil distribution terminal" loading="lazy"></div>
                            <div class="slider-slide"><img src="../css/images/truck2.jpg" alt="Transportation services" loading="lazy"></div>
                            <div class="slider-slide"><img src="../css/images/truck3.jpg" alt="Logistics operations" loading="lazy"></div>
                        <?php endif; ?>
                    </div>
                    <div class="slider-dots" id="heroDots"></div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-br from-primary/90 via-primary/70 to-secondary/20"></div>
            </div>

            <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-4xl mx-auto">
                    <h1 class="font-poppins font-extrabold text-4xl sm:text-5xl lg:text-6xl xl:text-7xl text-white mb-6 animate-fade-in">
                        <?php echo !empty($hero['title']) ? $hero['title'] : 'Delivering the Future of <span class="text-gradient bg-gradient-to-r from-secondary to-accent-100 bg-clip-text text-transparent">Oil Solutions</span>'; ?>
                    </h1>
                    <p class="text-xl sm:text-2xl text-orange-200 mb-8 max-w-3xl mx-auto animate-slide-up">
                        <?php echo !empty($hero['description']) ? htmlspecialchars($hero['description']) : 'Reliable and high-quality oil and energy solutions with nationwide coverage, trusted by industries and partners for their efficiency, safety, and consistent performance.'; ?>
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                        <a href="<?php echo !empty($hero['primary_button_link']) ? $hero['primary_button_link'] : 'contact.php'; ?>" class="bg-primary hover:bg-blue-800 text-white font-semibold py-4 px-8 rounded-lg transition-colors duration-300 text-lg transform hover:scale-105">
                            <?php echo !empty($hero['primary_button_text']) ? htmlspecialchars($hero['primary_button_text']) : 'Get Quote Now'; ?>
                        </a>
                        <a href="<?php echo !empty($hero['secondary_button_link']) ? $hero['secondary_button_link'] : 'services.php'; ?>" class="border-2 border-white text-white hover:bg-white hover:text-primary font-semibold py-4 px-8 rounded-lg transition-colors duration-300 text-lg transform hover:scale-105">
                            <?php echo !empty($hero['secondary_button_text']) ? htmlspecialchars($hero['secondary_button_text']) : 'Explore Services'; ?>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        <?php if (!empty($stats)): ?>
                            <?php foreach ($stats as $stat): ?>
                            <div class="animate-counter">
                                <div class="text-3xl sm:text-4xl font-poppins font-bold text-orange-500">
                                    <?php echo htmlspecialchars($stat['prefix'] ?? ''); ?><?php echo htmlspecialchars($stat['value'] ?? ''); ?><?php echo htmlspecialchars($stat['suffix'] ?? ''); ?>
                                </div>
                                <div class="text-orange-200 font-medium"><?php echo htmlspecialchars($stat['label'] ?? ''); ?></div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="animate-counter"><div class="text-3xl sm:text-4xl font-poppins font-bold text-orange-500">1</div><div class="text-orange-200 font-medium">Oil Stations</div></div>
                            <div class="animate-counter"><div class="text-3xl sm:text-4xl font-poppins font-bold text-orange-500">20+</div><div class="text-orange-200 font-medium">Years Experience</div></div>
                            <div class="animate-counter"><div class="text-3xl sm:text-4xl font-poppins font-bold text-orange-500">5+</div><div class="text-orange-200 font-medium">Corporate Partners</div></div>
                            <div class="animate-counter"><div class="text-3xl sm:text-4xl font-poppins font-bold text-orange-500">100%</div><div class="text-orange-200 font-medium">Safety Compliance</div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="py-20 bg-white dark:bg-gray-900">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl text-gray-900 dark:text-white mb-4">
                        Trusted by Industry Leaders
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        See what our corporate partners say about our reliable oil solutions and exceptional service quality.
                    </p>
                </div>

                <div class="testimonial-carousel-wrapper">
                    <div class="testimonial-carousel" id="testimonialCarousel">
                        <?php 
                        $testimonialsToShow = !empty($testimonials) ? $testimonials : [
                            ['author_name' => 'Roberto Santos', 'author_position' => 'Operations Director', 'company' => 'Manila Steel Corp', 'testimonial_text' => 'Power Giant RMT Oil has been our reliable oil partner for 8 years. Their 24/7 delivery service and consistent quality have never let us down during critical production periods.', 'rating' => 5],
                            ['author_name' => 'Maria Gonzalez', 'author_position' => 'Procurement Manager', 'company' => 'Global Manufacturing Inc', 'testimonial_text' => 'Exceptional service and reliable supply chain management. Power Giant RMT ensures our operations run smoothly with their timely deliveries and quality products.', 'rating' => 5],
                            ['author_name' => 'Juan dela Cruz', 'author_position' => 'Plant Manager', 'company' => 'PhilSteel Industries', 'testimonial_text' => 'Outstanding professionalism and dedication to customer satisfaction. Their team goes above and beyond to meet our operational needs.', 'rating' => 5]
                        ];
                        $allTestimonials = array_merge($testimonialsToShow, $testimonialsToShow, $testimonialsToShow);
                        foreach ($allTestimonials as $testimonial): 
                            $avatarUrl = getTestimonialAvatar($testimonial['avatar_path'] ?? '', $testimonial['author_name']);
                        ?>
                            <div class="testimonial-item">
                                <div class="testimonial-card">
                                    <div class="testimonial-header">
                                        <?php if ($avatarUrl): ?>
                                        <img src="<?php echo $avatarUrl; ?>" alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>" class="testimonial-avatar" loading="lazy">
                                        <?php else: ?>
                                        <div class="testimonial-avatar bg-primary text-white flex items-center justify-center font-bold text-xl">
                                            <?php echo strtoupper(substr($testimonial['author_name'], 0, 1)); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="testimonial-info">
                                            <h4><?php echo htmlspecialchars($testimonial['author_name']); ?></h4>
                                            <p><?php echo htmlspecialchars($testimonial['author_position']); ?></p>
                                            <p class="testimonial-company"><?php echo htmlspecialchars($testimonial['company']); ?></p>
                                        </div>
                                    </div>
                                    <p class="testimonial-text">"<?php echo htmlspecialchars($testimonial['testimonial_text']); ?>"</p>
                                    <div class="testimonial-rating">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                        <svg class="<?= $i < ($testimonial['rating'] ?? 5) ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Partners Section -->
                <div class="mt-16 pt-12 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="font-poppins font-semibold text-xl text-gray-900 dark:text-white text-center mb-8">Trusted by Leading Companies</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 items-center opacity-70">
                        <?php if (!empty($partners)): ?>
                            <?php foreach ($partners as $partner): 
                                $logoUrl = getPartnerLogo($partner['logo_path'] ?? '', $partner['name']);
                            ?>
                            <div class="text-center">
                                <?php if ($logoUrl): ?>
                                <img src="<?php echo $logoUrl; ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>" class="h-10 mx-auto object-contain" loading="lazy">
                                <?php else: ?>
                                <div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto">
                                    <span class="text-gray-600 dark:text-gray-300 font-semibold text-xs"><?php echo htmlspecialchars($partner['name']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center"><div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto"><span class="text-gray-600 dark:text-gray-300 font-semibold text-xs">ACME Corp</span></div></div>
                            <div class="text-center"><div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto"><span class="text-gray-600 dark:text-gray-300 font-semibold text-xs">TechFlow</span></div></div>
                            <div class="text-center"><div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto"><span class="text-gray-600 dark:text-gray-300 font-semibold text-xs">GlobalTech</span></div></div>
                            <div class="text-center"><div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto"><span class="text-gray-600 dark:text-gray-300 font-semibold text-xs">InnovateCo</span></div></div>
                            <div class="text-center"><div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto"><span class="text-gray-600 dark:text-gray-300 font-semibold text-xs">FutureTech</span></div></div>
                            <div class="text-center"><div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto"><span class="text-gray-600 dark:text-gray-300 font-semibold text-xs">NextGen</span></div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <!-- OIL TRUCK LOADER SCRIPT -->
    <script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader-overlay');
        const progressFill = document.getElementById('progressFill');
        
        let progress = 0;
        const interval = setInterval(() => {
            progress += 2;
            progressFill.style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    loader.style.opacity = '0';
                    loader.style.pointerEvents = 'none';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 500);
                }, 300);
            }
        }, 40);
        
        setTimeout(() => {
            if (loader.style.display !== 'none') {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 500);
            }
        }, 5000);
    });
    </script>

    <!-- YOUR EXISTING SCRIPTS -->
    <script src="script.js"></script>
    <script>
    // Dark Mode
    function initDarkMode() {
        const themeToggles = document.querySelectorAll('.theme-toggle, [data-theme-toggle]');
        const html = document.documentElement;
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        let currentTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
        html.setAttribute('data-theme', currentTheme);
        updateThemeIcons(currentTheme);
        
        themeToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcons(newTheme);
            });
        });
        
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) {
                const newTheme = e.matches ? 'dark' : 'light';
                html.setAttribute('data-theme', newTheme);
                updateThemeIcons(newTheme);
            }
        });
    }
    function updateThemeIcons(theme) {
        const sunIcons = document.querySelectorAll('.sun-icon, [data-sun-icon]');
        const moonIcons = document.querySelectorAll('.moon-icon, [data-moon-icon]');
        sunIcons.forEach(icon => icon.style.display = theme === 'dark' ? 'none' : 'block');
        moonIcons.forEach(icon => icon.style.display = theme === 'dark' ? 'block' : 'none');
    }

    function initMobileMenu() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                updateThemeIcons(document.documentElement.getAttribute('data-theme'));
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.hero-slider')) new HeroSlider();
        initDarkMode();
        initMobileMenu();
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    });

    window.addEventListener('storage', function(e) {
        if (e.key === 'theme') {
            document.documentElement.setAttribute('data-theme', e.newValue);
            updateThemeIcons(e.newValue);
        }
    });

    class HeroSlider {
        constructor() {
            this.slides = document.querySelectorAll('.hero-slider .slider-slide');
            this.dotsContainer = document.getElementById('heroDots');
            this.currentSlide = 0;
            this.interval = null;
            this.init();
        }
        init() {
            this.createDots();
            this.startAutoPlay();
            this.setupEventListeners();
        }
        createDots() {
            if (!this.dotsContainer) return;
            this.slides.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.className = `slider-dot ${index === 0 ? 'active' : ''}`;
                dot.addEventListener('click', () => this.goToSlide(index));
                this.dotsContainer.appendChild(dot);
            });
        }
        goToSlide(index) {
            this.slides[this.currentSlide].classList.remove('active');
            if (this.dotsContainer && this.dotsContainer.children[this.currentSlide]) {
                this.dotsContainer.children[this.currentSlide].classList.remove('active');
            }
            this.currentSlide = index;
            this.slides[this.currentSlide].classList.add('active');
            if (this.dotsContainer && this.dotsContainer.children[this.currentSlide]) {
                this.dotsContainer.children[this.currentSlide].classList.add('active');
            }
        }
        nextSlide() {
            const next = (this.currentSlide + 1) % this.slides.length;
            this.goToSlide(next);
        }
        startAutoPlay() {
            this.interval = setInterval(() => this.nextSlide(), 5000);
        }
        stopAutoPlay() {
            if (this.interval) clearInterval(this.interval);
        }
        setupEventListeners() {
            const slider = document.querySelector('.hero-slider');
            if (slider) {
                slider.addEventListener('mouseenter', () => this.stopAutoPlay());
                slider.addEventListener('mouseleave', () => this.startAutoPlay());
            }
        }
    }
    </script>
</body>
</html>