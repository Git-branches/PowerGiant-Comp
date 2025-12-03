<?php 
$page_title = "Contact Us";
include 'header.php'; 

require_once '../config/database.php';
$database = new Database();
$db = $database->connect();

// Fetch all contact content
$stmt = $db->query("SELECT * FROM contact_content ORDER BY display_order, id");
$contact_content = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prevent duplicates — keep only the FIRST occurrence of each section_name
$contact = [];
foreach ($contact_content as $item) {
    if (!isset($contact[$item['section_name']])) {
        $contact[$item['section_name']] = $item;
    }
}

/* ============ FINAL BULLETPROOF CONTACT INFO ============ */
$contact_info = [];

$contact_mapping = [
    'company_address' => ['key'=>'address', 'icon'=>'location', 'title'=>'Main Office'],
    'phone_number'    => ['key'=>'phone',   'icon'=>'phone',   'title'=>'Phone & Emergency'],
    'email_address'   => ['key'=>'email',   'icon'=>'email',   'title'=>'Email Us'],
    'business_hours'  => ['key'=>'hours',   'icon'=>'clock',   'title'=>'Business Hours'],
];

foreach ($contact_mapping as $section_name => $config) {
    if (isset($contact[$section_name])) {
        $item = $contact[$section_name];
        $title = $item['content_title'] ?? $config['title'];
        $raw = $item['content_value'];

        // Auto make phone & email clickable
$value = preg_replace('/(\d{10,})/', '<a href="tel:$1" class="hover:text-secondary font-medium">$1</a>', $raw);
$value = preg_replace('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', 
            '<a href="mailto:$1" class="hover:text-secondary font-medium">$1</a>', $value);

// Address → Google Maps link (FIXED!)
if ($section_name === 'company_address') {
    $clean_address = strip_tags($raw);
    $maps_url      = "https://maps.google.com/?q=" . urlencode($clean_address);
    $value         = "<a href=\"$maps_url\" target=\"_blank\" 
                        class=\"hover:text-secondary transition-colors font-medium\">
                        " . nl2br(htmlspecialchars($clean_address)) . "
                      </a>";
} else {
    $value = nl2br($value);
}

        $contact_info[$config['key']] = [
            'icon'    => $config['icon'],
            'title'   => htmlspecialchars($title),
            'content' => $value
        ];
    } else {
        // Fallbacks here if needed
    }
}
?>

<link rel="stylesheet" href="style.css">

<style>
    @keyframes scale-in {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .animate-scale-in { animation: scale-in 0.3s ease-out; }
    .bg-orange-success { background-color: rgb(255, 107, 0); }
    .hover\:bg-orange-success-dark:hover { background-color: rgb(230, 96, 0); }
    .break-words { word-break: break-word; overflow-wrap: break-word; }
    .min-w-0 { min-width: 0; }
</style>

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        document.querySelectorAll('.theme-toggle-icon').forEach(icon => icon.classList.toggle('hidden'));
        localStorage.setItem('theme', newTheme);
    }
    
    function initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (savedTheme === 'dark') {
            document.querySelectorAll('.sun-icon').forEach(icon => icon.classList.add('hidden'));
            document.querySelectorAll('.moon-icon').forEach(icon => icon.classList.remove('hidden'));
        } else {
            document.querySelectorAll('.sun-icon').forEach(icon => icon.classList.remove('hidden'));
            document.querySelectorAll('.moon-icon').forEach(icon => icon.classList.add('hidden'));
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                const menu = document.getElementById('mobile-menu');
                if (menu) menu.classList.toggle('hidden');
            });
        }

        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (navbar) navbar.classList.toggle('nav-shadow', window.scrollY > 10);
        });

        initTheme();

        const contactForm = document.getElementById('contact-form');
        if (!contactForm) return;

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Sending...';
            submitBtn.disabled = true;

            const formData = new FormData(contactForm);

            fetch('../includes/submit_quote.php', { 
                method: 'POST', 
                body: formData 
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccessPopup(data.message || "Thank you! We'll contact you within 24 hours.");
                    contactForm.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(() => alert('An error occurred. Please try again.'))
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        function showSuccessPopup(message) {
            const popup = document.createElement('div');
            popup.className = 'fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50';
            popup.innerHTML = `
                <div class="bg-white rounded-2xl p-8 mx-4 max-w-md w-full shadow-2xl transform transition-all duration-300 scale-95 animate-scale-in">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-[rgb(255,107,0)] rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="font-poppins font-bold text-2xl text-gray-900 mb-2">Success!</h3>
                        <p class="text-gray-600 mb-6">${message}</p>
                        <button onclick="closePopup()" class="bg-[rgb(255,107,0)] text-white font-poppins font-semibold px-6 py-3 rounded-lg hover:bg-[rgb(230,96,0)] transition-colors w-full">Continue</button>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);
            setTimeout(() => popup.querySelector('.animate-scale-in').classList.add('scale-100'), 10);
        }

        window.closePopup = () => {
            const popup = document.querySelector('.fixed.inset-0.flex.items-center.justify-center.z-50');
            if (popup) popup.remove();
        };
    });
</script>

<main class="min-h-screen">
    <section id="contact" class="section-padding bg-gradient-to-br from-primary to-primary-800">
        <div class="container-custom">
            <div class="text-center mb-16">
                <h2 class="font-poppins font-bold text-3xl sm:text-4xl lg:text-5xl text-white mb-6">
                    <?php echo htmlspecialchars($contact['contact_title']['content_value'] ?? 'Get Your Custom Oil Solution'); ?>
                </h2>
                <p class="text-lg text-accent-100 max-w-3xl mx-auto">
                    <?php echo htmlspecialchars($contact['contact_description']['content_value'] ?? 'Power Giant RMT delivers tailored energy and transport solutions designed to meet your business needs with efficiency and reliability. Our experts are ready to provide personalized consultations and competitive quotes within 24 hours — ensuring seamless supply, safe delivery, and sustainable service that drive your operations forward.'); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-2xl p-8 shadow-card">
                    <h3 class="font-poppins font-semibold text-2xl text-secondary-50 mb-6">
                        <?php echo htmlspecialchars($contact['form_title']['content_value'] ?? 'Request a Quote'); ?>
                    </h3>

                    <form id="contact-form" class="space-y-6" method="POST" enctype="multipart/form-data">
                        <!-- [Your full form here - unchanged] -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="company-name" class="block text-text-secondary font-medium mb-2">Company Name *</label>
                                <input type="text" id="company-name" name="company-name" required class="form-input" placeholder="Your Company Name">
                            </div>
                            <div>
                                <label for="industry" class="block text-text-secondary font-medium mb-2">Industry *</label>
                                <select id="industry" name="industry" required class="form-input">
                                    <option value="">Select Industry</option>
                                    <option value="manufacturing">Manufacturing</option>
                                    <option value="transportation">Transportation & Logistics</option>
                                    <option value="aviation">Aviation</option>
                                    <option value="marine">Marine & Shipping</option>
                                    <option value="construction">Construction</option>
                                    <option value="mining">Mining</option>
                                    <option value="agriculture">Agriculture</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contact-name" class="block text-text-secondary font-medium mb-2">Contact Person *</label>
                                <input type="text" id="contact-name" name="contact-name" required class="form-input" placeholder="John Dela Cruz">
                            </div>
                            <div>
                                <label for="position" class="block text-text-secondary font-medium mb-2">Position</label>
                                <input type="text" id="position" name="position" class="form-input" placeholder="Operations Manager">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-text-secondary font-medium mb-2">Email Address *</label>
                                <input type="email" id="email" name="email" required class="form-input" placeholder="powergiantrmt@gmail.com">
                            </div>
                            <div>
                                <label for="phone" class="block text-text-secondary font-medium mb-2">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required class="form-input" placeholder="+63 912 345 6789">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="service" class="block text-text-secondary font-medium mb-2">Select Product / Service</label>
                                <select id="service" name="service" class="form-input" required>
                                    <option value="">Select Product / Service</option>
                                    <option value="Palm Oil">Palm Oil</option>
                                    <option value="Coconut Oil">Coconut Oil</option>
                                    <option value="Hazardous Waste">Hazardous Waste</option>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Transport Logistics">Transport Logistics</option>
                                </select>
                            </div>
                            <div>
                                <label for="delivery-location" class="block text-text-secondary font-medium mb-2">Primary Delivery Location *</label>
                                <input type="text" id="delivery-location" name="delivery-location" class="form-input" placeholder="Metro Manila, Philippines" required>
                            </div>
                        </div>

                        <div>
                            <label for="attachment" class="block text-text-secondary font-medium mb-2">Upload Image (Optional)</label>
                            <input type="file" id="attachment" name="attachment" class="form-input" accept="image/*">
                            <p class="text-sm text-text-secondary mt-1">All image types accepted: JPG, PNG, GIF, BMP, WEBP, SVG, etc.</p>
                        </div>

                        <div>
                            <label for="message" class="block text-text-secondary font-medium mb-2">Additional Requirements</label>
                            <textarea id="message" name="message" rows="4" class="form-input" placeholder="Please describe any specific requirements..."></textarea>
                        </div>

                        <button type="submit" class="w-full btn-primary">
                            <?php echo htmlspecialchars($contact['submit_button_text']['content_value'] ?? 'Get Quote in 24 Hours'); ?>
                        </button>
                    </form>
                </div>

                <!-- RIGHT COLUMN: Contact Info + Map -->
                <div class="space-y-8">
                    <div class="container-custom px-0">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Contact Information -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8">
                                <h3 class="font-poppins font-semibold text-2xl text-white mb-6">
                                    <?php echo htmlspecialchars($contact['contact_info_title']['content_value'] ?? 'Contact Information'); ?>
                                </h3>
                                
                                <div class="space-y-6">
                                    <?php foreach (['address', 'phone', 'email', 'hours'] as $key): 
                                        $info = $contact_info[$key];
                                    ?>
                                    <div class="flex items-start space-x-4 w-full">
                                        <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center flex-shrink-0">
                                            <?php if ($info['icon'] === 'location'): ?>
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            <?php elseif ($info['icon'] === 'phone'): ?>
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                            <?php elseif ($info['icon'] === 'email'): ?>
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            <?php elseif ($info['icon'] === 'clock'): ?>
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-poppins font-semibold text-white mb-2"><?php echo $info['title']; ?></h4>
                                            <p class="text-accent-100 break-words text-sm leading-relaxed">
                                                <?php echo $info['content']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Google Maps -->
                            <section id="sitemap">
                                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8">
                                    <h3 class="font-poppins font-semibold text-2xl text-white mb-6">
                                        <?php echo htmlspecialchars($contact['location_title']['content_value'] ?? 'Our Location'); ?>
                                    </h3>
                                    
                                    <div class="rounded-xl overflow-hidden h-96 mb-4 relative">
                                        <iframe 
                                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.783036039971!2d125.0975887!3d6.1640393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f799614fa7db2d%3A0x57a365ee6af39376!2sMiravilla%20Homes!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
                                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="rounded-lg"></iframe>
                                        
                                        <div class="absolute bottom-4 left-4 right-4 flex justify-center">
                                            <a href="https://maps.google.com/?q=Miravilla+Homes+Polomolok+South+Cotabato" target="_blank" 
                                               class="bg-white/90 backdrop-blur-sm text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-white transition-all duration-300 shadow-lg flex items-center space-x-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                                </svg>
                                                <span>Open in Google Maps</span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="bg-secondary/20 rounded-lg p-4">
                                        <h4 class="font-poppins font-semibold text-white mb-2">
                                            <?php echo htmlspecialchars($contact['location_guide_title']['content_value'] ?? 'How to Reach Us'); ?>
                                        </h4>
                                        <p class="text-accent-100 text-sm">
                                            <?php echo htmlspecialchars($contact['location_guide']['content_value'] ?? 'Located in Glamang Prk-3, Miravilla Homes, Polomolok, South Cotabato. Easily accessible via national highway.'); ?>
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script id="dhws-dataInjector" src="../public/dhws-data-injector.js"></script>
<script src="script.js"></script>
<?php include 'footer.php'; ?>