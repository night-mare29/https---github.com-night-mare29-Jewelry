document.addEventListener('DOMContentLoaded', function() {
    // Form submit với loading animation
    const form = document.querySelector('.category-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Disable submit button
            const submitBtn = this.querySelector('.submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.7';
            }

            // Show loading animation
            const loading = document.createElement('div');
            loading.className = 'loading';
            loading.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loading);
        });
    }

    // Animation cho table rows
    const tableRows = document.querySelectorAll('.categories-table tbody tr');
    tableRows.forEach((row, index) => {
        // Add fade in animation với delay
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 100);

        // Hover effect
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // Xử lý xóa category với animation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc muốn xóa loại sản phẩm này?')) {
                const row = this.closest('tr');
                
                // Animation fade out cho row
                row.style.transition = 'all 0.5s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                
                // Show loading
                const loading = document.createElement('div');
                loading.className = 'loading';
                loading.innerHTML = '<div class="loading-spinner"></div>';
                document.body.appendChild(loading);
                
                // Redirect sau animation
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            }
        });
    });

    // Alert animation
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Fade in
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        
        requestAnimationFrame(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '1';
            alert.style.transform = 'translateY(0)';
        });
        
        // Fade out sau 3s
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 3000);
    });

    // Counter animation cho số lượng sản phẩm
    const productCounts = document.querySelectorAll('.product-count');
    productCounts.forEach(count => {
        const targetNumber = parseInt(count.textContent);
        let currentNumber = 0;
        const duration = 1000;
        const increment = Math.ceil(targetNumber / (duration / 16));
        
        function updateNumber() {
            if (currentNumber < targetNumber) {
                currentNumber = Math.min(currentNumber + increment, targetNumber);
                count.textContent = currentNumber;
                requestAnimationFrame(updateNumber);
            }
        }
        
        requestAnimationFrame(updateNumber);
    });

    // Empty state check
    const table = document.querySelector('.categories-table');
    if (table && table.querySelectorAll('tbody tr').length === 0) {
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';
        emptyState.innerHTML = `
            <i class="fas fa-folder-open"></i>
            <p>Chưa có loại sản phẩm nào. Hãy thêm loại sản phẩm đầu tiên!</p>
        `;
        table.parentNode.insertBefore(emptyState, table);
        table.style.display = 'none';
    }
});