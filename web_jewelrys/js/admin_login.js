document.addEventListener('DOMContentLoaded', function() {
    // Lấy form
    const form = document.querySelector('.login-form');
    const inputs = form.querySelectorAll('input');

    // Thêm hiệu ứng cho input khi focus
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'all 0.3s ease';
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
            this.style.transform = 'scale(1)';
        });
    });

    // Kiểm tra độ mạnh của mật khẩu
    const passwordInput = form.querySelector('#password');
    if (passwordInput) {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength';
        strengthIndicator.style.cssText = `
            height: 4px;
            transition: all 0.3s ease;
            margin-top: 5px;
            border-radius: 2px;
        `;
        passwordInput.parentElement.appendChild(strengthIndicator);

        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            updateStrengthIndicator(strengthIndicator, strength);
        });
    }

    // Xử lý form submit với animation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm(form)) {
            // Animation khi submit thành công
            const button = form.querySelector('button[type="submit"]');
            button.innerHTML = 'Đang xử lý...';
            button.style.background = 'linear-gradient(135deg, #1a237e, #283593)';
            
            // Giả lập loading để thấy animation
            setTimeout(() => {
                this.submit();
            }, 1000);
        }
    });
});

// Hàm kiểm tra độ mạnh của mật khẩu
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength += 1;
    if (password.match(/[a-z]+/)) strength += 1;
    if (password.match(/[A-Z]+/)) strength += 1;
    if (password.match(/[0-9]+/)) strength += 1;
    if (password.match(/[!@#$%^&*]+/)) strength += 1;

    return strength;
}

// Cập nhật thanh hiển thị độ mạnh mật khẩu
function updateStrengthIndicator(indicator, strength) {
    const colors = ['#ef5350', '#ff7043', '#ffa726', '#66bb6a', '#42a5f5'];
    const widths = ['20%', '40%', '60%', '80%', '100%'];
    
    indicator.style.width = widths[strength - 1] || '0';
    indicator.style.background = colors[strength - 1] || '#ddd';
}

// Hàm validate form
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            shakeElement(input);
        }

        // Kiểm tra email
        if (input.type === 'email' && !isValidEmail(input.value)) {
            isValid = false;
            shakeElement(input);
        }
    });

    return isValid;
}

// Hiệu ứng rung khi input không hợp lệ
function shakeElement(element) {
    element.style.animation = 'none';
    element.offsetHeight; // Trigger reflow
    element.style.animation = 'shake 0.5s ease-in-out';
    element.style.borderColor = '#d32f2f';

    setTimeout(() => {
        element.style.borderColor = '#e0e0e0';
    }, 2000);
}

// Kiểm tra email hợp lệ
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}