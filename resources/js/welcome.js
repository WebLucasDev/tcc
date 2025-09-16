document.addEventListener('DOMContentLoaded', function() {
    // Initialize landing page
    initSplashScreen();
    initScrollAnimations();
    initNavigation();
    initParticleEffects();
});

// Splash Screen Controller
function initSplashScreen() {
    const splashScreen = document.getElementById('splashScreen');
    const mainContent = document.getElementById('mainContent');

    // Show splash screen for 3 seconds
    setTimeout(() => {
        // Add fade out animation to splash screen
        splashScreen.classList.add('fade-out');

        // Show main content after splash fade out
        setTimeout(() => {
            mainContent.classList.add('visible');
            splashScreen.style.display = 'none';

            // Trigger entrance animations
            triggerEntranceAnimations();

            // Initialize reveal animations after content is visible
            setTimeout(() => {
                initRevealAnimations();
            }, 100);
        }, 800);
    }, 3000);
}

// Initialize reveal animations
function initRevealAnimations() {
    const revealElements = document.querySelectorAll('.section-reveal');

    console.log(`Found ${revealElements.length} elements to animate`);

    // Create intersection observer with better settings
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                console.log('Animating element:', entry.target);

                // Add delay for staggered animations
                const delay = entry.target.dataset.delay || 0;
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, delay);

                // Stop observing once animated
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all elements and add staggered delays
    revealElements.forEach((element, index) => {
        // Reset any existing visible class
        element.classList.remove('visible');

        // Add staggered delay
        const baseDelay = Math.floor(index / 3) * 200; // Group delays
        const itemDelay = (index % 3) * 100; // Individual delays within groups
        element.dataset.delay = baseDelay + itemDelay;

        observer.observe(element);
        console.log(`Observing element ${index} with delay ${element.dataset.delay}ms`);
    });
}

// Scroll-based animations
function initScrollAnimations() {
    let ticking = false;

    // Optimized scroll handler with throttling
    function handleScroll() {
        if (!ticking) {
            requestAnimationFrame(() => {
                const scrolled = window.pageYOffset;
                const windowHeight = window.innerHeight;

                // Parallax effect for hero section
                const heroSection = document.querySelector('.hero-section');
                if (heroSection && scrolled < windowHeight) {
                    const heroOffset = scrolled * 0.5;
                    heroSection.style.transform = `translateY(${heroOffset}px)`;
                }

                // Animate particles based on scroll
                const particles = document.querySelectorAll('.particle');
                particles.forEach((particle, index) => {
                    if (scrolled < windowHeight * 2) {
                        const speed = (index + 1) * 0.05; // Reduced speed for smoother effect
                        const particleOffset = scrolled * speed;
                        particle.style.transform = `translateY(${particleOffset}px) rotate(${particleOffset * 0.5}deg)`;
                    }
                });

                // Update navigation style
                updateNavigationStyle(scrolled);

                ticking = false;
            });
            ticking = true;
        }
    }

    // Add scroll listener
    window.addEventListener('scroll', handleScroll, { passive: true });

    // Initial call
    handleScroll();
}

// Update navigation style based on scroll
function updateNavigationStyle(scrolled) {
    const nav = document.querySelector('nav');
    if (!nav) return;

    if (scrolled > 100) {
        nav.classList.add('scrolled');
        nav.style.background = 'rgba(255, 255, 255, 0.95)';
        nav.style.backdropFilter = 'blur(20px)';
        nav.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
    } else {
        nav.classList.remove('scrolled');
        nav.style.background = 'rgba(255, 255, 255, 0.9)';
        nav.style.backdropFilter = 'blur(12px)';
        nav.style.boxShadow = 'none';
    }
}

// Navigation interactions
function initNavigation() {
    const navLinks = document.querySelectorAll('nav a[href^="#"]');

    // Smooth scrolling for navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                const headerOffset = 80; // Account for fixed navigation
                const elementPosition = targetSection.offsetTop;
                const offsetPosition = elementPosition - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Mobile menu toggle (if needed in future)
    const mobileMenuBtn = document.querySelector('.md\\:hidden button');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            console.log('Mobile menu clicked');
        });
    }
}

// Enhanced particle effects
function initParticleEffects() {
    const particlesContainer = document.querySelector('.floating-particles');

    if (!particlesContainer) return;

    // Create additional dynamic particles
    function createParticle() {
        const particle = document.createElement('div');
        particle.className = 'particle';

        // Random properties
        const size = Math.random() * 6 + 2;
        const left = Math.random() * 100;
        const animationDuration = Math.random() * 20 + 15;
        const delay = Math.random() * 5;

        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${left}%`;
        particle.style.animationDuration = `${animationDuration}s`;
        particle.style.animationDelay = `${delay}s`;

        particlesContainer.appendChild(particle);

        // Remove particle after animation
        setTimeout(() => {
            if (particle.parentNode) {
                particle.parentNode.removeChild(particle);
            }
        }, (animationDuration + delay) * 1000);
    }

    // Create particles periodically
    setInterval(createParticle, 2000);
}

// Entrance animations for hero section
function triggerEntranceAnimations() {
    const heroElements = [
        '.hero-section img',
        '.hero-section h1',
        '.hero-section p',
        '.hero-section .flex'
    ];

    heroElements.forEach((selector, index) => {
        const element = document.querySelector(selector);
        if (element) {
            setTimeout(() => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';

                requestAnimationFrame(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                });
            }, index * 200);
        }
    });
}

// Button hover effects
document.addEventListener('mouseover', (e) => {
    if (e.target.classList.contains('btn-primary')) {
        e.target.style.transform = 'translateY(-2px)';
        e.target.style.boxShadow = '0 10px 20px rgba(225, 62, 22, 0.3)';
    }
});

document.addEventListener('mouseout', (e) => {
    if (e.target.classList.contains('btn-primary')) {
        e.target.style.transform = 'translateY(0)';
        e.target.style.boxShadow = '';
    }
});

// Card hover effects
const cards = document.querySelectorAll('.card-hover');
cards.forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-10px) scale(1.02)';
        card.style.boxShadow = '0 25px 50px rgba(225, 62, 22, 0.15)';
    });

    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0) scale(1)';
        card.style.boxShadow = '';
    });
});

// Scroll indicator click
document.querySelector('.scroll-indicator')?.addEventListener('click', () => {
    document.getElementById('features').scrollIntoView({
        behavior: 'smooth'
    });
});

// Add dynamic gradient animation
function animateGradients() {
    const gradientElements = document.querySelectorAll('.gradient-text');

    gradientElements.forEach(element => {
        let hue = 0;
        setInterval(() => {
            hue = (hue + 1) % 360;
            const color1 = `hsl(${hue}, 70%, 50%)`;
            const color2 = `hsl(${(hue + 60) % 360}, 70%, 60%)`;
            element.style.background = `linear-gradient(135deg, ${color1} 0%, ${color2} 100%)`;
            element.style.webkitBackgroundClip = 'text';
            element.style.webkitTextFillColor = 'transparent';
        }, 100);
    });
}

// Initialize gradient animations after content is visible
setTimeout(() => {
    if (document.getElementById('mainContent').classList.contains('visible')) {
        // animateGradients(); // Uncomment for dynamic gradient effect
    }
}, 4000);

// Performance optimization - throttle scroll events
let ticking = false;

function throttleScroll() {
    if (!ticking) {
        requestAnimationFrame(() => {
            // Scroll-based animations here
            ticking = false;
        });
        ticking = true;
    }
}

window.addEventListener('scroll', throttleScroll);

// Add loading states for images
document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('load', () => {
            img.style.opacity = '1';
            img.style.transform = 'scale(1)';
        });

        img.style.opacity = '0';
        img.style.transform = 'scale(0.9)';
        img.style.transition = 'all 0.3s ease';
    });
});

// Add ripple effect to buttons
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-primary')) {
        const button = e.target;
        const rect = button.getBoundingClientRect();
        const ripple = document.createElement('span');

        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        button.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .btn-primary {
        position: relative;
        overflow: hidden;
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }

    nav.scrolled {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(style);
