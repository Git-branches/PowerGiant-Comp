<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../config/auth.php';

// Redirect if already logged in (with role check)
if (isLoggedIn()) {
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'editor') {
        header('Location: index_editor.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

// Get flash error message from session
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            $database = new Database();
            $db = $database->connect();
            
            $sql = "SELECT * FROM admin_user WHERE username = :username LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Update last login
                $update_sql = "UPDATE admin_user SET last_login = NOW() WHERE id = :id";
                $update_stmt = $db->prepare($update_sql);
                $update_stmt->execute([':id' => $admin['id']]);
                
                // Set session with ROLE ✅
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role']; // ← CRITICAL FIX!
                
                // Set success flag for loader
                $_SESSION['login_success'] = true;
                
                // Redirect based on role ✅
                if ($admin['role'] === 'admin') {
                    header('Location: dashboard.php');
                } elseif ($admin['role'] === 'editor') {
                    header('Location: index_editor.php');
                } else {
                    header('Location: dashboard.php'); // Fallback
                }
                exit;
            } else {
                $_SESSION['login_error'] = 'Invalid username or password';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['login_error'] = 'Login failed. Please try again.';
            error_log("Login Error: " . $e->getMessage());
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $_SESSION['login_error'] = 'Please fill in all fields';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Power Giant RMT</title>
    <link rel="icon" type="" href="POWER-GIANT.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1a365d 50%, #0f172a 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated background particles */
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* Oil Truck Tanker Loader Styles */
        #loader-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        #loader-overlay.active {
            display: flex;
        }

        .loader-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #0f172a 0%, #1a365d 50%, #0f172a 100%);
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

        /* Road - FIXED POSITION */
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

        /* Oil Truck Container - FIXED POSITION */
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

        /* Truck Cabin */
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

        /* Oil Tanker */
        .oil-tanker {
            position: absolute;
            left: 80px;
            bottom: 5px;
            width: 180px;
            height: 70px;
            background: linear-gradient(180deg, #71717a 0%, #52525b 100%);
            border-radius: 35px;
            overflow: hidden;
            box-shadow: 
                inset 0 -5px 10px rgba(0, 0, 0, 0.3),
                0 4px 10px rgba(0, 0, 0, 0.3);
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

        /* Wheels - FIXED POSITION */
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

        /* Exhaust Smoke */
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
            0% {
                transform: translateY(0) scale(1);
                opacity: 0.6;
            }
            100% {
                transform: translateY(-50px) scale(2);
                opacity: 0;
            }
        }

        /* Loading Text */
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

        /* Progress Bar */
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
            animation: progressMove 2s ease-in-out infinite;
        }

        @keyframes progressMove {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* Password toggle button fix */
        #togglePassword {
            z-index: 10;
            background: none;
            border: none;
            cursor: pointer;
        }

        #togglePassword i {
            display: block;
            font-style: normal;
        }

        /* Hide browser autofill icons */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        /* Glassmorphism card */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .gradient-border {
            position: relative;
            border-radius: 24px;
            padding: 2px;
            background: linear-gradient(135deg, 
                rgba(13, 148, 136, 0.6) 0%, 
                rgba(6, 182, 212, 0.6) 25%, 
                rgba(59, 130, 246, 0.6) 50%, 
                rgba(147, 51, 234, 0.6) 75%, 
                rgba(13, 148, 136, 0.6) 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-input:focus {
            border-color: rgba(6, 182, 212, 0.6);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }

        .gradient-btn {
            background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .gradient-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -8px rgba(6, 182, 212, 0.6);
        }

        .gradient-btn:active {
            transform: translateY(0);
        }

        .gradient-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .gradient-btn:hover::before {
            left: 100%;
        }

        .error-glass {
            background: rgba(220, 38, 38, 0.1);
            backdrop-filter: blur(10px);
            border-left: 4px solid rgba(220, 38, 38, 0.8);
        }

        .logo-container {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .glow {
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.3);
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(6, 182, 212, 0.5);
            border-radius: 3px;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .road { width: 300px; }
            .oil-truck { 
                width: 200px; 
                transform: scale(0.8); 
                margin-bottom: 0;
            }
            .gradient-border {
                margin: 1rem;
                padding: 1px;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Animated particles background -->
    <div id="particles-js"></div>

    <!-- Oil Truck Tanker Loader Overlay -->
    <div id="loader-overlay">
        <div class="loader-bg"></div>
        <div class="loader-container">
            <!-- Oil Truck -->
            <div class="oil-truck">
                <!-- Truck Cabin -->
                <div class="truck-cabin">
                    <div class="truck-window"></div>
                </div>

                <!-- Oil Tanker -->
                <div class="oil-tanker">
                    <div class="oil-liquid">
                        <div class="oil-wave"></div>
                    </div>
                    <div class="tanker-stripe"></div>
                    <div class="tanker-cap"></div>
                </div>

                <!-- Wheels -->
                <div class="wheel wheel-1"></div>
                <div class="wheel wheel-2"></div>
                <div class="wheel wheel-3"></div>

                <!-- Exhaust Smoke -->
                <div class="exhaust">
                    <div class="smoke"></div>
                    <div class="smoke"></div>
                    <div class="smoke"></div>
                </div>
            </div>

            <!-- Road -->
            <div class="road">
                <div class="road-line"></div>
            </div>

            <!-- Loading Text -->
            <div class="loader-text">LOADING DASHBOARD</div>
            <div class="loader-subtext">Fueling up your admin portal...</div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- Login Card -->
    <div class="gradient-border w-full max-w-sm sm:max-w-md">
        <div class="glass-card rounded-[22px] p-8 sm:p-10 text-white">
            <div class="text-center mb-10">
                <div class="logo-container mb-4">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg glow">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight">
                    PG <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-cyan-400">RMT</span> Portal
                </h1>
                <p class="text-black-500 font-bold mt-2 text-md">Secure Administrative Access</p>
            </div>
            
            <?php if ($error): ?>
            <div class="error-glass p-4 rounded-lg mb-6 flex items-start space-x-3" id="error-message">
                <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                <div>
                    <p class="text-red-300 font-medium text-sm">Authentication Error</p>
                    <p class="text-red-200 text-sm mt-1"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="login-form" class="space-y-6">
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-semibold text-gray-300">
                        <i class="fas fa-user mr-2 text-cyan-400"></i>Username
                    </label>
                    <div class="relative">
                        <input type="text" id="username" name="username" required 
                                class="glass-input w-full px-4 py-3 pl-11 rounded-xl placeholder-gray-400 text-white focus:outline-none"
                                placeholder="Enter your username" autocomplete="username">
                        <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-300">
                        <i class="fas fa-lock mr-2 text-cyan-400"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                                class="glass-input w-full px-4 py-3 pl-11 pr-11 rounded-xl placeholder-gray-400 text-white focus:outline-none"
                                placeholder="••••••••" autocomplete="current-password">
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <button type="button" id="togglePassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-cyan-400 transition-colors focus:outline-none">
                            <i class="fas fa-eye" id="eyeIcon" style="pointer-events: none;"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" id="login-button"
                        class="gradient-btn w-full text-white font-semibold py-3.5 px-4 rounded-xl flex items-center justify-center space-x-2">
                    <span id="button-text">Sign In to Dashboard</span>
                    <i class="fas fa-arrow-right" id="button-icon"></i>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-700 text-center">
                <p class="text-xs text-green-500 font-bold">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Secure access for authorized personnel only
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Develop By:
                    <span class="text-cyan-400 font-semibold">Rhon Jon Romero</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
    // Particles Background
    particlesJS('particles-js', {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: "#06b6d4" },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: true },
            size: { value: 3, random: true },
            line_linked: {
                enable: true,
                distance: 150,
                color: "#0d9488",
                opacity: 0.2,
                width: 1
            },
            move: {
                enable: true,
                speed: 2,
                direction: "none",
                random: true,
                straight: false,
                out_mode: "out",
                bounce: false
            }
        },
        interactivity: {
            detect_on: "canvas",
            events: {
                onhover: { enable: true, mode: "repulse" },
                onclick: { enable: true, mode: "push" },
                resize: true
            }
        },
        retina_detect: true
    });

    // Password Toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            eyeIcon.className = 'fas fa-eye';
        }
    });

    // Form Submit Handler - WITH LOADER DURATION CONTROL
    document.getElementById('login-form').addEventListener('submit', function(e) {
        const button = document.getElementById('login-button');
        const buttonText = document.getElementById('button-text');
        const buttonIcon = document.getElementById('button-icon');
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const loaderOverlay = document.getElementById('loader-overlay');

        if (!username || !password) {
            return;
        }

        // Prevent immediate form submission to show loader longer
        e.preventDefault();

        // Show loading state
        button.disabled = true;
        buttonText.textContent = 'Authenticating...';
        buttonIcon.className = 'fas fa-circle-notch fa-spin';
        button.style.opacity = '0.8';
        
        // Show the loader overlay
        loaderOverlay.classList.add('active');
        
        // Set minimum loader display time (adjust this value - in milliseconds)
        const minimumLoaderTime = 5000; // 3 seconds - CHANGE THIS VALUE TO ADJUST LOADER DURATION
        
        setTimeout(function() {
            // Now submit the form after the minimum loader time
            document.getElementById('login-form').submit();
        }, minimumLoaderTime);
    });

    // Auto-hide error message after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const errorBox = document.getElementById('error-message');
        
        if (errorBox) {
            setTimeout(function() {
                errorBox.style.transition = 'all 0.5s ease';
                errorBox.style.opacity = '0';
                errorBox.style.transform = 'translateY(-10px)';
                
                setTimeout(function() {
                    errorBox.remove();
                }, 500);
            }, 3000);
        }
        
        // Auto-focus username field
        document.getElementById('username').focus();
        
        // Animate card elements
        const elements = document.querySelectorAll('.glass-card > *');
        elements.forEach(function(el, i) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            setTimeout(function() {
                el.style.transition = 'all 0.5s ease';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, i * 100);
        });
    });
</script>
</body>
</html>