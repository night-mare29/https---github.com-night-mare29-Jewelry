document.addEventListener('DOMContentLoaded', function() {
    // Preview ảnh khi upload
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const preview = document.querySelector('.current-image') || document.createElement('div');
                preview.className = 'current-image';
                
                if (!document.querySelector('.current-image')) {
                    this.parentNode.appendChild(preview);
                }
                
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <p>Ảnh xem trước:</p>
                        <img src="${e.target.result}" style="opacity: 0">
                    `;
                    
                    setTimeout(() => {
                        const img = preview.querySelector('img');
                        img.style.transition = 'opacity 0.3s ease';
                        img.style.opacity = '1';
                    }, 100);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Drag and drop support
        const dropZone = fileInput.closest('.file-input-wrapper');
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('highlight');
            }

            function unhighlight(e) {
                dropZone.classList.remove('highlight');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        }
    }

    // Form submission với loading state
    const form = document.querySelector('.product-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('input[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.value = 'Đang xử lý...';
            }

            const loading = document.createElement('div');
            loading.className = 'loading';
            loading.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loading);
        });
    }

    // Animate table rows
    const tableRows = document.querySelectorAll('.product-table tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Format prices
    const priceElements = document.querySelectorAll('.price-column');
    priceElements.forEach(el => {
        const price = parseFloat(el.textContent);
        el.textContent = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    });

    // Xử lý xóa sản phẩm
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                const row = this.closest('tr');
                row.style.transition = 'all 0.5s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                
                const loading = document.createElement('div');
                loading.className = 'loading';
                loading.innerHTML = '<div class="loading-spinner"></div>';
                document.body.appendChild(loading);
                
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            }
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

    // Image zoom effect
    const productImages = document.querySelectorAll('.product-image');
    productImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.zIndex = '100';
        });

        img.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });

    // Animated counters
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value !== '') {
                this.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            }
        });
    });
});