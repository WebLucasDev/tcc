// Splash Screen Animation
document.addEventListener('DOMContentLoaded', function() {
    const splashScreen = document.getElementById('splash-screen');
    const splashLogo = document.getElementById('splash-logo');
    const splashTitle = document.getElementById('splash-title');
    const splashSubtitle = document.getElementById('splash-subtitle');
    const splashLoader = document.getElementById('splash-loader');

    // Animate splash elements
    setTimeout(() => {
        splashLogo.style.opacity = '1';
        splashLogo.style.transform = 'scale(1.1)';
    }, 300);

    setTimeout(() => {
        splashTitle.style.opacity = '1';
        splashTitle.style.transform = 'translateY(0)';
    }, 800);

    setTimeout(() => {
        splashSubtitle.style.opacity = '1';
        splashSubtitle.style.transform = 'translateY(0)';
    }, 1200);

    setTimeout(() => {
        splashLoader.style.opacity = '1';
    }, 1500);

    // Hide splash screen
    setTimeout(() => {
        splashScreen.style.opacity = '0';
        splashScreen.style.transform = 'scale(1.1)';
        setTimeout(() => {
            splashScreen.style.display = 'none';
            initializeMainAnimations();
        }, 500);
    }, 3000);
});

// Canvas Mouse Effects
function initCanvasEffects() {
    const canvas = document.getElementById('canvas-effects');
    const ctx = canvas.getContext('2d');
    let particles = [];
    let mouse = { x: 0, y: 0 };

    // Resize canvas
    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Particle class
    class Particle {
        constructor(x, y) {
            this.x = x;
            this.y = y;
            this.vx = (Math.random() - 0.5) * 2;
            this.vy = (Math.random() - 0.5) * 2;
            this.life = 1.0;
            this.decay = Math.random() * 0.02 + 0.01;
            this.size = Math.random() * 3 + 1;
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;
            this.life -= this.decay;
            this.size *= 0.98;
        }

        draw() {
            ctx.save();
            ctx.globalAlpha = this.life;
            ctx.fillStyle = '#E13E16';
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        isDead() {
            return this.life <= 0 || this.size <= 0.1;
        }
    }

    // Mouse move handler
    document.addEventListener('mousemove', function(e) {
        mouse.x = e.clientX;
        mouse.y = e.clientY;

        // Add particles on mouse move
        if (particles.length < 100) {
            particles.push(new Particle(mouse.x, mouse.y));
        }
    });

    // Animation loop
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Update and draw particles
        particles = particles.filter(particle => {
            particle.update();
            particle.draw();
            return !particle.isDead();
        });

        requestAnimationFrame(animate);
    }

    animate();
}

// Parallax Effects
function initParallax() {
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax-bg');

        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translate3d(0, ${yPos}px, 0)`;
        });
    });
}

// Smooth Scrolling
function initSmoothScroll() {
    document.querySelectorAll('.smooth-scroll').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80; // Account for fixed navbar
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Intersection Observer for Fade In Animations
function initFadeInAnimations() {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all elements with fade-in class
    document.querySelectorAll('.fade-in').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        observer.observe(element);
    });
}

// Mobile Menu Toggle
function initMobileMenu() {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = menuToggle.querySelector('i');

    menuToggle.addEventListener('click', function() {
        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
            menuIcon.classList.remove('fa-bars');
            menuIcon.classList.add('fa-times');
        } else {
            mobileMenu.classList.add('hidden');
            menuIcon.classList.remove('fa-times');
            menuIcon.classList.add('fa-bars');
        }
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('#mobile-menu a').forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
            menuIcon.classList.remove('fa-times');
            menuIcon.classList.add('fa-bars');
        });
    });
}

// Navbar Scroll Effect
function initNavbarScrollEffect() {
    const navbar = document.getElementById('navbar');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-white/95');
            navbar.classList.remove('bg-white/90');
        } else {
            navbar.classList.remove('bg-white/95');
            navbar.classList.add('bg-white/90');
        }
    });
}

// Hero Section Animations
function initHeroAnimations() {
    const heroTitle = document.getElementById('hero-title');
    const heroSubtitle = document.getElementById('hero-subtitle');

    setTimeout(() => {
        heroTitle.style.opacity = '1';
        heroTitle.style.transform = 'translateY(0)';
    }, 200);

    setTimeout(() => {
        heroSubtitle.style.opacity = '1';
        heroSubtitle.style.transform = 'translateY(0)';
    }, 600);
}

// Typing Effect for Hero Title (alternative animation)
function initTypingEffect() {
    const heroTitle = document.getElementById('hero-title');
    const text = 'Metre Ponto';
    let index = 0;

    function typeChar() {
        if (index < text.length) {
            heroTitle.textContent = text.slice(0, index + 1);
            index++;
            setTimeout(typeChar, 100);
        }
    }

    // Uncomment to use typing effect instead of fade in
    // setTimeout(typeChar, 500);
}

// Counter Animation for Statistics
function initCounterAnimation() {
    const counters = document.querySelectorAll('[data-count]');

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.count);
                const duration = 2000; // 2 seconds
                const steps = 60;
                const increment = target / steps;
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current);
                }, duration / steps);

                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
}

// Active Section Navigation
function initActiveNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('section[id]');

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sectionId = entry.target.getAttribute('id');

                // Remove active class from all nav links
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });

                // Add active class to current section's nav link
                const activeLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        });
    }, {
        rootMargin: '-20% 0px -70% 0px', // Trigger when section is 20% from top
        threshold: 0.1
    });

    // Observe all sections
    sections.forEach(section => {
        observer.observe(section);
    });
}

// Scroll Indicator Click Handler
function initScrollIndicator() {
    const scrollIndicator = document.querySelector('.animate-bounce');
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function() {
            const sobreSection = document.getElementById('sobre');
            if (sobreSection) {
                sobreSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });

        // Add hover effect
        scrollIndicator.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(-50%) scale(1.1)';
        });

        scrollIndicator.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(-50%) scale(1)';
        });
    }
}
// Add Hover Effects to Cards
function initCardHoverEffects() {
    const cards = document.querySelectorAll('.hover\\:shadow-lg');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}// Floating Animation for Elements
function initFloatingAnimation() {
    const floatingElements = document.querySelectorAll('.animate-float');

    floatingElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.5}s`;
        element.style.animation = 'float 6s ease-in-out infinite';
    });
}

// Add CSS for floating animation
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .hero-title, .hero-subtitle {
        transition: opacity 0.8s ease, transform 0.8s ease;
    }
`;
document.head.appendChild(style);

// Loading States for Buttons
function initButtonLoadingStates() {
    const buttons = document.querySelectorAll('button');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Carregando...';
            this.disabled = true;

            // Simulate loading (remove in production)
            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        });
    });
}

// Initialize all animations and effects
function initializeMainAnimations() {
    initCanvasEffects();
    initParallax();
    initSmoothScroll();
    initFadeInAnimations();
    initMobileMenu();
    initNavbarScrollEffect();
    initHeroAnimations();
    initCounterAnimation();
    initCardHoverEffects();
    initFloatingAnimation();
    initActiveNavigation();
    initScrollIndicator();

    // Optional: Uncomment for button loading states
    // initButtonLoadingStates();
}// Performance optimization: Use passive event listeners
const addPassiveEventListener = (element, event, handler) => {
    element.addEventListener(event, handler, { passive: true });
};

// Optimize scroll events
let ticking = false;
function updateOnScroll() {
    // Parallax and navbar effects are handled here
    ticking = false;
}

window.addEventListener('scroll', () => {
    if (!ticking) {
        requestAnimationFrame(updateOnScroll);
        ticking = true;
    }
}, { passive: true });

// Preload critical resources
function preloadResources() {
    const criticalImages = [
        '/imgs/favicon.ico'
        // Add other critical images here
    ];

    criticalImages.forEach(src => {
        const img = new Image();
        img.src = src;
    });
}

// Initialize preloading
preloadResources();
