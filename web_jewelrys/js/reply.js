document.addEventListener('DOMContentLoaded', function() {
    // Animate table rows
    const tableRows = document.querySelectorAll('.contact-table tbody tr');
    tableRows.forEach((row, index) => {
        // Add fade in animation với delay
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Form submission với loading state
    const replyForms = document.querySelectorAll('.reply-form');
    replyForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Check if textarea is empty
            const textarea = this.querySelector('textarea');
            if (!textarea.value.trim()) {
                e.preventDefault();
                textarea.classList.add('error');
                textarea.placeholder = 'Vui lòng nhập nội dung phản hồi';
                return;
            }

            // Show loading spinner
            const loading = document.createElement('div');
            loading.className = 'loading';
            loading.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loading);

            // Disable submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
            }
        });
    });

    // Auto-resize textarea
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Initial height
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    });

    // Show character count
    textareas.forEach(textarea => {
        const wrapper = textarea.parentElement;
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.fontSize = '0.8rem';
        counter.style.color = '#666';
        counter.style.textAlign = 'right';
        counter.style.marginTop = '0.25rem';
        
        wrapper.appendChild(counter);

        function updateCounter() {
            const remaining = 1000 - textarea.value.length;
            counter.textContent = `${remaining} ký tự còn lại`;
            
            if (remaining < 100) {
                counter.style.color = '#e74c3c';
            } else {
                counter.style.color = '#666';
            }
        }

        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });

    // Format timestamps
    const timestamps = document.querySelectorAll('.timestamp');
    timestamps.forEach(el => {
        const date = new Date(el.textContent);
        el.textContent = new Intl.DateTimeFormat('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    });

    // Message preview expansion
    const messages = document.querySelectorAll('.message-content');
    messages.forEach(msg => {
        if (msg.scrollHeight > msg.clientHeight) {
            const expandBtn = document.createElement('button');
            expandBtn.className = 'expand-btn';
            expandBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Xem thêm';
            expandBtn.style.background = 'none';
            expandBtn.style.border = 'none';
            expandBtn.style.color = '#3498db';
            expandBtn.style.cursor = 'pointer';
            expandBtn.style.padding = '0.5rem 0';
            
            msg.after(expandBtn);

            expandBtn.addEventListener('click', function() {
                if (msg.style.maxHeight) {
                    msg.style.maxHeight = null;
                    this.innerHTML = '<i class="fas fa-chevron-down"></i> Xem thêm';
                } else {
                    msg.style.maxHeight = msg.scrollHeight + 'px';
                    this.innerHTML = '<i class="fas fa-chevron-up"></i> Thu gọn';
                }
            });
        }
    });

    // Empty state check
    const table = document.querySelector('.contact-table');
    if (table && table.querySelectorAll('tbody tr').length === 0) {
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';
        emptyState.innerHTML = `
            <i class="fas fa-inbox"></i>
            <p>Chưa có góp ý nào từ khách hàng.</p>
        `;
        table.parentNode.insertBefore(emptyState, table);
        table.style.display = 'none';
    }

    // Alert auto-hide
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
});