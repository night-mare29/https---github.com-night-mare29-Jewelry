document.addEventListener('DOMContentLoaded', function() {
    // Preview ảnh khi upload
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const preview = this.closest('.form-group').querySelector('.image-preview');
                
                reader.onload = function(e) {
                    // Tạo hoặc cập nhật preview
                    let img;
                    if (preview.querySelector('img')) {
                        img = preview.querySelector('img');
                    } else {
                        img = document.createElement('img');
                        preview.appendChild(img);
                    }
                    
                    // Animation cho preview
                    img.style.opacity = '0';
                    img.src = e.target.result;
                    setTimeout(() => {
                        img.style.transition = 'opacity 0.3s ease';
                        img.style.opacity = '1';
                    }, 100);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Hiệu ứng cho form submit
    const form = document.querySelector('.promotion-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Thêm loading spinner
            const loading = document.createElement('div');
            loading.className = 'loading';
            loading.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loading);

            // Disable submit button
            const submitBtn = this.querySelector('.submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.7';
            }
        });
    }

    // Hiệu ứng cho table rows
    const tableRows = document.querySelectorAll('.promotions-table tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transition = 'transform 0.3s ease';
            this.style.transform = 'scale(1.01)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Xử lý xóa với animation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc muốn xóa ưu đãi này?')) {
                // Animation fade out cho row
                const row = this.closest('tr');
                row.style.transition = 'all 0.5s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                
                // Loading animation
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

    // Zoom ảnh trong table
    const tableImages = document.querySelectorAll('.promotions-table img');
    tableImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
            this.style.transform = 'scale(1.2)';
            this.style.zIndex = '1';
        });

        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = 'auto';
        });
    });

    // Drop zone effect cho input file
    const fileInputWrappers = document.querySelectorAll('.file-input-wrapper');
    fileInputWrappers.forEach(wrapper => {
        const input = wrapper.querySelector('input[type="file"]');
        
        input.addEventListener('dragenter', function(e) {
            this.style.borderColor = '#3498db';
            this.style.background = 'rgba(52,152,219,0.05)';
        });

        input.addEventListener('dragleave', function(e) {
            this.style.borderColor = '#ddd';
            this.style.background = 'transparent';
        });

        input.addEventListener('drop', function(e) {
            this.style.borderColor = '#2ecc71';
            setTimeout(() => {
                this.style.borderColor = '#ddd';
                this.style.background = 'transparent';
            }, 1000);
        });
    });
});