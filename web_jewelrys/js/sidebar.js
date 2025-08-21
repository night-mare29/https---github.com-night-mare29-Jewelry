document.addEventListener('DOMContentLoaded', function() {
    // Highlight active menu item
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('.menu a');
    
    menuItems.forEach(item => {
        // Remove active class from all items first
        item.classList.remove('active');
        
        // Add active class to current page
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
            
            // Also highlight parent section
            const section = item.closest('.menu-section');
            if (section) {
                section.classList.add('active');
            }
        }
    });

    // Hover effect for menu items
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        item.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // Animate sections on load
    const sections = document.querySelectorAll('.menu-section');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateX(-10px)';
        
        setTimeout(() => {
            section.style.transition = 'all 0.3s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateX(0)';
        }, index * 100);
    });

    // Smooth scroll for menu
    const menu = document.querySelector('.menu');
    let isScrolling = false;

    menu.addEventListener('scroll', function() {
        if (!isScrolling) {
            window.requestAnimationFrame(() => {
                // Add shadow when scrolling
                if (menu.scrollTop > 0) {
                    menu.style.boxShadow = 'inset 0 5px 5px -5px rgba(0,0,0,0.2)';
                } else {
                    menu.style.boxShadow = 'none';
                }
                isScrolling = false;
            });
            isScrolling = true;
        }
    });

    // Logout button effect
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        });

        logoutBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }
});