document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const inputs = form.querySelectorAll('input, textarea');

    // Thêm hiệu ứng khi focus vào input
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });

    // Validate form
    function validateForm(e) {
        e.preventDefault();
        let isValid = true;
        const errors = [];

        // Reset error states
        inputs.forEach(input => {
            input.classList.remove('error');
        });

        // Validate name
        const name = form.querySelector('#name');
        if (!name.value.trim()) {
            name.classList.add('error');
            errors.push('Vui lòng nhập họ tên');
            isValid = false;
        }

        // Validate email
        const email = form.querySelector('#email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim() || !emailRegex.test(email.value)) {
            email.classList.add('error');
            errors.push('Email không hợp lệ');
            isValid = false;
        }

        // Validate phone (optional)
        const phone = form.querySelector('#phone');
        if (phone.value.trim()) {
            const phoneRegex = /^[0-9]{10,11}$/;
            if (!phoneRegex.test(phone.value)) {
                phone.classList.add('error');
                errors.push('Số điện thoại không hợp lệ');
                isValid = false;
            }
        }

        // Validate message
        const message = form.querySelector('#message');
        if (!message.value.trim()) {
            message.classList.add('error');
            errors.push('Vui lòng nhập nội dung tin nhắn');
            isValid = false;
        }

        if (!isValid) {
            // Hiển thị thông báo lỗi
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-msg';
            errorDiv.innerHTML = `⚠️ ${errors.join('<br>')}`;
            
            // Xóa thông báo lỗi cũ nếu có
            const oldError = form.querySelector('.error-msg');
            if (oldError) {
                oldError.remove();
            }
            
            form.insertBefore(errorDiv, form.firstChild);
            
            // Scroll to error message
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            // Submit form if valid
            form.submit();
        }
    }

    form.addEventListener('submit', validateForm);

    // Thêm hiệu ứng cho input khi nhập
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                if (this.value.trim()) {
                    this.classList.remove('error');
                }
            }
        });
    });

    // Thêm hiệu ứng cho button
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.addEventListener('mouseover', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    submitBtn.addEventListener('mouseout', function() {
        this.style.transform = 'translateY(0)';
    });
});