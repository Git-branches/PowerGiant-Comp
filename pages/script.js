// ===== IMAGE SLIDER FUNCTIONALITY =====
class ImageSlider {
    constructor() {
        this.currentSlide = 0;
        this.totalSlides = 5;
        this.sliderContainer = document.getElementById('heroSlider');
        this.dots = document.querySelectorAll('.slider-dot');
        this.autoSlideInterval = null;
        
        this.init();
    }

    init() {
        this.startAutoSlide();
        this.setupKeyboardNavigation();
        
        // Pause auto-slide on hover
        this.sliderContainer.addEventListener('mouseenter', () => this.pauseAutoSlide());
        this.sliderContainer.addEventListener('mouseleave', () => this.startAutoSlide());
    }

    goToSlide(slideIndex) {
        this.currentSlide = slideIndex;
        const translateX = -(slideIndex * 20); // 20% per slide (100% / 5 slides)
        this.sliderContainer.style.transform = `translateX(${translateX}%)`;
        
        // Update dots
        this.dots.forEach((dot, index) => {
            if (index === slideIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.goToSlide(this.currentSlide);
    }

    previousSlide() {
        this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
        this.goToSlide(this.currentSlide);
    }

    startAutoSlide() {
        this.autoSlideInterval = setInterval(() => {
            this.nextSlide();
        }, 5000); // Change slide every 5 seconds
    }

    pauseAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
            this.autoSlideInterval = null;
        }
    }

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.previousSlide();
                this.pauseAutoSlide();
                setTimeout(() => this.startAutoSlide(), 5000);
            } else if (e.key === 'ArrowRight') {
                this.nextSlide();
                this.pauseAutoSlide();
                setTimeout(() => this.startAutoSlide(), 5000);
            }
        });
    }
}

// Initialize slider
const heroSlider = new ImageSlider();

// Global functions for slider controls
function currentSlide(slideIndex) {
    heroSlider.goToSlide(slideIndex - 1);
    heroSlider.pauseAutoSlide();
    setTimeout(() => heroSlider.startAutoSlide(), 5000);
}

function nextSlide() {
    heroSlider.nextSlide();
    heroSlider.pauseAutoSlide();
    setTimeout(() => heroSlider.startAutoSlide(), 5000);
}

function previousSlide() {
    heroSlider.previousSlide();
    heroSlider.pauseAutoSlide();
    setTimeout(() => heroSlider.startAutoSlide(), 5000);
}

// ===== GALLERY FUNCTIONALITY =====
class ImageGallery {
    constructor() {
        this.galleryItems = document.querySelectorAll('.gallery-item');
        this.init();
    }

    init() {
        this.setupGalleryInteractions();
        this.setupLazyLoading();
    }

    setupGalleryInteractions() {
        this.galleryItems.forEach((item, index) => {
            item.addEventListener('click', () => this.openLightbox(index));
            
            // Add staggered animation on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, entryIndex) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 150);
                    }
                });
            }, { threshold: 0.1 });
            
            observer.observe(item);
            
            // Initial state for animation
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        });
    }

    setupLazyLoading() {
        const images = document.querySelectorAll('.gallery-item img');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        images.forEach(img => {
            if (img.dataset.src) {
                imageObserver.observe(img);
            }
        });
    }

    openLightbox(index) {
        // Create lightbox modal
        const lightbox = document.createElement('div');
        lightbox.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4';
        lightbox.style.backdropFilter = 'blur(10px)';
        
        const img = this.galleryItems[index].querySelector('img');
        const title = this.galleryItems[index].querySelector('.gallery-title')?.textContent || '';
        const description = this.galleryItems[index].querySelector('.gallery-description')?.textContent || '';
        
        lightbox.innerHTML = `
            <div class="max-w-4xl w-full relative">
                <button class="absolute -top-12 right-0 text-white hover:text-secondary text-4xl font-light z-10">&times;</button>
                <img src="${img.src}" alt="${img.alt}" class="w-full h-auto rounded-lg shadow-2xl">
                <div class="bg-white dark:bg-gray-800 rounded-b-lg p-6 mt-4">
                    <h3 class="text-xl font-bold text-primary mb-2">${title}</h3>
                    <p class="text-text-secondary">${description}</p>
                </div>
            </div>
        `;
        
        // Close lightbox functionality
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target.textContent === '×') {
                document.body.removeChild(lightbox);
                document.body.style.overflow = 'auto';
            }
        });
        
        // ESC key to close
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                if (document.body.contains(lightbox)) {
                    document.body.removeChild(lightbox);
                    document.body.style.overflow = 'auto';
                }
                document.removeEventListener('keydown', escHandler);
            }
        });
        
        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';
    }
}

// Initialize gallery
const imageGallery = new ImageGallery();

// ===== THEME MANAGEMENT SYSTEM =====
class ThemeManager {
    constructor() {
        this.currentTheme = this.getStoredTheme() || this.getSystemTheme();
        this.init();
    }

    init() {
        this.applyTheme(this.currentTheme);
        this.updateToggleState();
        this.setupSystemThemeListener();
    }

    getStoredTheme() {
        return localStorage.getItem('astra-oil-theme');
    }

    getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    setStoredTheme(theme) {
        localStorage.setItem('astra-oil-theme', theme);
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        this.currentTheme = theme;
        this.setStoredTheme(theme);
        
        // Add smooth transition effect
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        
        // Update meta theme color for mobile browsers
        this.updateMetaThemeColor(theme);
    }

    updateMetaThemeColor(theme) {
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', theme === 'dark' ? '#0f0f23' : '#ffffff');
        } else {
            const meta = document.createElement('meta');
            meta.name = 'theme-color';
            meta.content = theme === 'dark' ? '#0f0f23' : '#ffffff';
            document.getElementsByTagName('head')[0].appendChild(meta);
        }
    }

    updateToggleState() {
        const toggles = document.querySelectorAll('.theme-toggle');
        toggles.forEach(toggle => {
            if (this.currentTheme === 'dark') {
                toggle.classList.add('dark');
            } else {
                toggle.classList.remove('dark');
            }
        });
    }

    toggle() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        this.updateToggleState();
        
        // Add haptic feedback for mobile devices
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
        
        // Announce theme change for accessibility
        this.announceThemeChange(newTheme);
    }

    announceThemeChange(theme) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = `Switched to ${theme} mode`;
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    setupSystemThemeListener() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addListener((e) => {
            if (!this.getStoredTheme()) {
                this.applyTheme(e.matches ? 'dark' : 'light');
                this.updateToggleState();
            }
        });
    }
}


// Initialize theme manager
const themeManager = new ThemeManager();

// Global toggle function
function toggleTheme() {
    themeManager.toggle();
}

// ===== EXISTING FUNCTIONALITY =====

// Mobile Navigation Toggle
/*
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

mobileMenuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});*/

// Smooth Scrolling for Navigation Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            // Close mobile menu if open
            //mobileMenu.classList.add('hidden');
        }
    });
});

// Enhanced Navbar Background on Scroll with Theme Support
window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    const currentTheme = document.documentElement.getAttribute('data-theme');
    
    if (window.scrollY > 50) {
        navbar.classList.add('shadow-lg');
        if (currentTheme === 'dark') {
            navbar.style.backgroundColor = 'rgba(15, 15, 35, 0.98)';
        } else {
            navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.98)';
        }
    } else {
        navbar.classList.remove('shadow-lg');
        if (currentTheme === 'dark') {
            navbar.style.backgroundColor = 'rgba(15, 15, 35, 0.95)';
        } else {
            navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        }
    }
});

// Animated Counters with Enhanced Performance
function animateCounters() {
    const counters = document.querySelectorAll('[data-count]');
    
    counters.forEach(counter => {
        if (counter.dataset.animated) return; // Prevent re-animation
        
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
                counter.dataset.animated = 'true';
            }
        };
        
        updateCounter();
    });
}

// Enhanced Intersection Observer for Animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
            
            // Trigger counter animation for statistics
            if (entry.target.querySelector('[data-count]')) {
                animateCounters();
            }
        }
    });
}, observerOptions);

// Observe sections for animations
document.querySelectorAll('section').forEach(section => {
    observer.observe(section);
});

// Enhanced Form Handling with Theme Support
const contactForm = document.getElementById('contact-form');
const successMessage = document.getElementById('success-message');

if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        const requiredFields = contactForm.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                field.style.borderColor = '#ef4444';
            } else {
                field.classList.remove('border-red-500');
                field.style.borderColor = '';
            }
        });
        
        if (isValid) {
            // Add loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';
            submitBtn.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                contactForm.style.display = 'none';
                successMessage.classList.remove('hidden');
            }, 1500);
        }
    });
}

// Enhanced Parallax Effect with Performance Optimization
let ticking = false;

function updateParallax() {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    
    parallaxElements.forEach(element => {
        const speed = 0.5;
        const yPos = -(scrolled * speed);
        element.style.transform = `translateY(${yPos}px)`;
    });
    
    ticking = false;
}

window.addEventListener('scroll', () => {
    if (!ticking) {
        requestAnimationFrame(updateParallax);
        ticking = true;
    }
});

// Enhanced 3D Card Hover Effects with Theme Support
document.querySelectorAll('.card-service').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-12px) rotateX(8deg) scale(1.02)';
        const currentTheme = document.documentElement.getAttribute('data-theme');
        if (currentTheme === 'dark') {
            this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.4)';
        } else {
            this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.2)';
        }
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) rotateX(0) scale(1)';
        const currentTheme = document.documentElement.getAttribute('data-theme');
        if (currentTheme === 'dark') {
            this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.3)';
        } else {
            this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.1)';
        }
    });
});

// Enhanced Page Load Initialization
// Replace the existing form submission code in your landing.html
// Update the existing form submission code in your landing.html
// Update the form submission in your index.php
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const successMessage = document.getElementById('success-message');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '📤 Submitting...';
            submitBtn.disabled = true;

            // Validate file size
            const fileInput = document.getElementById('attachment');
            if (fileInput.files[0] && fileInput.files[0].size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }

            // Collect form data
            const formData = new FormData(contactForm);

            // Submit via AJAX
            fetch('../includes/submit_quote.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    successMessage.classList.remove('hidden');
                    contactForm.reset();
                    
                    // Scroll to success message
                    successMessage.scrollIntoView({ behavior: 'smooth' });
                    
                    // Hide success message after 10 seconds
                    setTimeout(() => {
                        successMessage.classList.add('hidden');
                    }, 10000);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Sorry, there was an error submitting your request. Please try again.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// Theme-aware component updates
function updateThemeAwareComponents() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    
    // Update any theme-specific components here
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        if (currentTheme === 'dark') {
            img.style.filter = 'brightness(0.9) contrast(1.1)';
        } else {
            img.style.filter = 'brightness(1) contrast(1)';
        }
    });
}

// Listen for theme changes to update components
document.addEventListener('DOMContentLoaded', function() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                updateThemeAwareComponents();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme']
    });
});

// Keyboard Navigation for Theme Toggle
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Shift + T to toggle theme
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
        e.preventDefault();
        toggleTheme();
    }
});

// Enhanced Performance Monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(function() {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData.loadEventEnd - perfData.loadEventStart > 3000) {
                console.warn('Page load time exceeded 3 seconds');
            }
        }, 0);
    });
}

// Service Worker Registration for Better Performance (Optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('SW registered: ', registration);
        }).catch(function(registrationError) {
            console.log('SW registration failed: ', registrationError);
        });
    });
}




// PALITAN ANG SCROLL SPY CODE - Line ~384 pababa:

document.addEventListener("DOMContentLoaded", () => {
  const sections = document.querySelectorAll("section[id]");
  // UPDATE: Include both old and new navigation classes
  const navLinks = document.querySelectorAll('.nav-link.text-text-secondary, .mobile-nav-link.text-text-secondary, .text-text-secondary');
  const mobileMenu = document.getElementById("mobile-menu");

  // === Scroll Spy (auto highlight while scrolling)
  function activateNavLink() {
    let scrollPos = window.scrollY + window.innerHeight / 3;
    let foundActive = false;
    
    sections.forEach(section => {
      const id = section.getAttribute("id");
      const offsetTop = section.offsetTop;
      const offsetHeight = section.offsetHeight;
      
      // Find links that match this section
      const links = document.querySelectorAll(`.nav-link.text-text-secondary[href="#${id}"], .mobile-nav-link.text-text-secondary[href="#${id}"], .text-text-secondary[href="#${id}"]`);
      
      if (scrollPos >= offsetTop && scrollPos < offsetTop + offsetHeight && !foundActive) {
        // Remove all active links first
        navLinks.forEach(l => l.classList.remove("active"));
        
        // Add active to matching links
        links.forEach(link => link.classList.add("active"));
        foundActive = true;
      }
    });
    
    // If no section found, check for home page
    if (!foundActive && window.location.hash === '') {
      const homeLinks = document.querySelectorAll('[href="index.php"], [href="./"], [href="#home"]');
      navLinks.forEach(l => l.classList.remove("active"));
      homeLinks.forEach(link => link.classList.add("active"));
    }
  }

  

  // === Event Listeners for scroll spy
  window.addEventListener("scroll", activateNavLink);
  window.addEventListener("load", activateNavLink);

  // === Click highlight + close mobile nav
  navLinks.forEach(link => {
    link.addEventListener("click", () => {
      // Remove all active links
      navLinks.forEach(l => l.classList.remove("active"));
      link.classList.add("active");

      // Auto-close mobile nav
      if (mobileMenu && !mobileMenu.classList.contains("hidden")) {
        mobileMenu.classList.add("hidden");
      }
    });

    // === Touch feedback (simulate hover on tap)
    link.addEventListener("touchstart", () => link.classList.add("touched"));
    link.addEventListener("touchend", () => {
      setTimeout(() => link.classList.remove("touched"), 300);
    });
  });
});


        document.addEventListener('DOMContentLoaded', function() {
            const track = document.querySelector('.testimonial-track');
            const slides = document.querySelectorAll('.testimonial-slide');
            const cards = document.querySelectorAll('.testimonial-card');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            const dotsContainer = document.querySelector('.slider-dots');
            
            let currentIndex = 0;
            let slidesPerView = getSlidesPerView();
            let autoSlideInterval;
            
            // Create dots
            slides.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.classList.add('slider-dot');
                if (index === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(index));
                dotsContainer.appendChild(dot);
            });
            
            const dots = document.querySelectorAll('.slider-dot');
            
            function getSlidesPerView() {
                if (window.innerWidth >= 1024) return 3;
                if (window.innerWidth >= 768) return 2;
                return 1;
            }
            
            function updateSlider() {
                const slideWidth = 100 / slidesPerView;
                track.style.transform = `translateX(-${currentIndex * slideWidth}%)`;
                
                // Update active cards and dots
                cards.forEach((card, index) => {
                    const isActive = index >= currentIndex && index < currentIndex + slidesPerView;
                    card.classList.toggle('active', isActive);
                });
                
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }
            
            function goToSlide(index) {
                currentIndex = index;
                updateSlider();
                resetAutoSlide();
            }
            
            function nextSlide() {
                if (currentIndex < slides.length - slidesPerView) {
                    currentIndex++;
                } else {
                    // Loop back to the beginning for a continuous effect
                    currentIndex = 0;
                }
                updateSlider();
                resetAutoSlide();
            }
            
            function prevSlide() {
                if (currentIndex > 0) {
                    currentIndex--;
                } else {
                    // Loop to the end for a continuous effect
                    currentIndex = slides.length - slidesPerView;
                }
                updateSlider();
                resetAutoSlide();
            }
            
            function startAutoSlide() {
                 autoSlideInterval = setInterval(nextSlide, 3000); // 3 seconds na lang
            }
            
            function resetAutoSlide() {
                clearInterval(autoSlideInterval);
                startAutoSlide();
            }
            
            // Event listeners
            prevBtn.addEventListener('click', prevSlide);
            nextBtn.addEventListener('click', nextSlide);
            
            // Handle window resize
            window.addEventListener('resize', () => {
                slidesPerView = getSlidesPerView();
                // Reset to first slide on resize to avoid empty space
                if (currentIndex > slides.length - slidesPerView) {
                    currentIndex = Math.max(0, slides.length - slidesPerView);
                }
                updateSlider();
            });
            
            // Start auto-slide
            startAutoSlide();
            
            // Pause auto-slide on hover
            track.addEventListener('mouseenter', () => {
                clearInterval(autoSlideInterval);
            });
            
            track.addEventListener('mouseleave', () => {
                startAutoSlide();
            });
        });


        