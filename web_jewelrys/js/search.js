document.addEventListener('DOMContentLoaded', function() {
    // Thêm animation delay cho các sản phẩm
    const products = document.querySelectorAll('.product-item');
    products.forEach((product, index) => {
        product.style.animationDelay = `${index * 0.1}s`;
    });

    // Hiệu ứng hover cho ảnh sản phẩm
    products.forEach(product => {
        const img = product.querySelector('img');
        
        product.addEventListener('mouseenter', () => {
            img.style.transform = 'scale(1.05)';
        });

        product.addEventListener('mouseleave', () => {
            img.style.transform = 'scale(1)';
        });
    });

    // Thêm loading animation khi tìm kiếm
    const searchForm = document.querySelector('form[action="search.php"]');
    if (searchForm) {
        searchForm.addEventListener('submit', () => {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = `
                <div class="loading-spinner"></div>
                <p>Đang tìm kiếm...</p>
            `;
            document.body.appendChild(loadingOverlay);
        });
    }

    // Lazy loading cho ảnh sản phẩm
    if ('IntersectionObserver' in window) {
        const imgOptions = {
            root: null,
            threshold: 0.1,
            rootMargin: '0px'
        };

        const imgObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('fade-in');
                    observer.unobserve(img);
                }
            });
        }, imgOptions);

        const productImages = document.querySelectorAll('.product-item img[data-src]');
        productImages.forEach(img => imgObserver.observe(img));
    }

    // Smooth scroll khi click vào nút "Xem chi tiết"
    const viewDetailButtons = document.querySelectorAll('.view-detail');
    viewDetailButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            // Lưu vị trí scroll hiện tại vào session storage
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });
    });

    // Hiệu ứng ripple cho nút "Xem chi tiết"
    viewDetailButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            this.appendChild(ripple);

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size/2;
            const y = e.clientY - rect.top - size/2;

            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Thêm CSS cho loading overlay và ripple effect
const style = document.createElement('style');
style.textContent = `
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e0dcd7;
        border-top-color: #4B3621;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);