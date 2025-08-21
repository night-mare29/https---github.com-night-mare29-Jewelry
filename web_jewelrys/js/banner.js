document.addEventListener('DOMContentLoaded', function() {
    // File preview
    const fileInput = document.querySelector('input[type="file"]');
    const previewContainer = document.querySelector('.file-preview');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (!previewContainer) return;
                    
                    // Clear previous preview
                    previewContainer.innerHTML = '';
                    
                    // Create new preview
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.opacity = '0';
                    previewContainer.appendChild(img);
                    
                    // Fade in animation
                    setTimeout(() => {
                        img.style.transition = 'opacity 0.3s ease';
                        img.style.opacity = '1';
                    }, 100);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Table row hover effect
    const tableRows = document.querySelectorAll('table tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transition = 'background-color 0.3s ease';
            this.style.backgroundColor = '#f8f9fa';
        });

        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Submit form with loading state
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('input[type="submit"]');
            if (submitBtn) {
                submitBtn.value = 'Đang xử lý...';
                submitBtn.disabled = true;
            }

            // Show loading spinner
            const loading = document.createElement('div');
            loading.className = 'loading';
            loading.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loading);
        });
    }

    // Confirm delete with custom dialog
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc muốn xoá banner này?')) {
                // Show loading spinner
                const loading = document.createElement('div');
                loading.className = 'loading';
                loading.innerHTML = '<div class="loading-spinner"></div>';
                document.body.appendChild(loading);
                
                // Redirect after animation
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            }
        });
    });

    // Alert animation
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Fade out animation
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            
            // Remove after animation
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 3000);
    });

    // Image zoom effect
    const tableImages = document.querySelectorAll('table img');
    tableImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transition = 'transform 0.3s ease';
            this.style.transform = 'scale(1.1)';
            this.style.zIndex = '1';
        });

        img.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.zIndex = '';
        });
    });
});