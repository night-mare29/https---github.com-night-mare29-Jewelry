document.addEventListener('DOMContentLoaded', function() {
    // Filter change animation
    const filterSelect = document.querySelector('.filter select');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const loading = document.createElement('div');
            loading.className = 'loading';
            loading.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loading);
        });
    }

    // Animate table rows
    const tableRows = document.querySelectorAll('.orders-table tbody tr');
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

    // Format dates
    const dateColumns = document.querySelectorAll('.date-column');
    dateColumns.forEach(col => {
        const date = new Date(col.textContent);
        col.textContent = new Intl.DateTimeFormat('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    });

    // Format prices
    const priceColumns = document.querySelectorAll('.price-column');
    priceColumns.forEach(col => {
        const price = parseFloat(col.textContent);
        col.textContent = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    });

    // Status badges animation
    const statusBadges = document.querySelectorAll('.status-badge');
    statusBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });

        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Delete confirmation với animation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc muốn xóa đơn hàng này?')) {
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

    // Print button animation
    const printButtons = document.querySelectorAll('.print-btn');
    printButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.add('printing');
            setTimeout(() => {
                this.classList.remove('printing');
            }, 1000);
        });
    });

    // Alert animations
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Empty state check
    const table = document.querySelector('.orders-table');
    if (table && table.querySelectorAll('tbody tr').length === 0) {
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';
        emptyState.innerHTML = `
            <i class="fas fa-box-open"></i>
            <p>Chưa có đơn hàng nào${filterSelect.value ? ' với trạng thái này' : ''}.</p>
        `;
        table.parentNode.insertBefore(emptyState, table);
        table.style.display = 'none';
    }

    // View button tooltip
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(btn => {
        btn.setAttribute('title', 'Xem chi tiết đơn hàng');
        
        // Custom tooltip animation
        btn.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.position = 'absolute';
            tooltip.style.top = e.pageY - 30 + 'px';
            tooltip.style.left = e.pageX + 10 + 'px';
            document.body.appendChild(tooltip);
            
            setTimeout(() => tooltip.style.opacity = '1', 10);
        });

        btn.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.style.opacity = '0';
                setTimeout(() => tooltip.remove(), 200);
            }
        });
    });
});