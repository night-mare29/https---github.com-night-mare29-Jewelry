// Parallax effect for banner
window.addEventListener('scroll', function() {
    const banner = document.querySelector('.banner img, .main-banner');
    if (banner) {
        const scrolled = window.pageYOffset;
        banner.style.transform = `translateY(${scrolled * 0.3}px)`;
    }
});

// User menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        userMenu.addEventListener('click', function(e) {
            this.classList.toggle('active');
            e.stopPropagation();
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
            }
        });
    }
});

// Intersection Observer for scroll animations
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-up');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Add animation to products
document.querySelectorAll('.product-card').forEach(product => {
    observer.observe(product);
    
    // 3D hover effect
    product.addEventListener('mousemove', handleHover);
    product.addEventListener('mouseleave', resetStyles);
});

// 3D hover effect function
function handleHover(e) {
    const card = this;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    
    const rotateX = (y - centerY) / 10;
    const rotateY = (centerX - x) / 10;
    
    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
    card.style.transition = 'transform 0.1s';
    
    // Add shine effect
    const shine = card.querySelector('.shine') || document.createElement('div');
    if (!card.querySelector('.shine')) {
        shine.classList.add('shine');
        card.appendChild(shine);
    }
    
    shine.style.backgroundImage = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 80%)`;
}

function resetStyles() {
    this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
    this.style.transition = 'transform 0.5s';
}

// Smooth scroll for menu links
document.querySelectorAll('.menu a').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href.startsWith('#')) {
            e.preventDefault();
            document.querySelector(href).scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Add necessary CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .shine {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        background: radial-gradient(circle at center, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 80%);
    }
    
    .product-card {
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
        will-change: transform;
    }
`;
document.head.appendChild(style);

// Page loading animation
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});

// Add loading animation styles
const loadingStyle = document.createElement('style');
loadingStyle.textContent = `
    body {
        opacity: 0;
        transition: opacity 0.5s;
    }
    
    body.loaded {
        opacity: 1;
    }
`;
document.head.appendChild(loadingStyle);