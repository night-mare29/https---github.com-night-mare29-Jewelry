document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const paymentSelect = document.getElementById('payment_method');
    const bankInfo = document.getElementById('bank-info');

    // Validation patterns
    const patterns = {
        phone: /^(0|\+84)[0-9]{9}$/,
        name: /^[a-zA-ZÀ-ỹ\s]{2,}$/,
        address: /.{10,}/
    };

    // Show success state
    function showSuccess(input) {
        input.classList.remove('invalid');
        input.classList.add('valid');
        const error = input.parentNode.querySelector('.error-message');
        if (error) error.remove();
    }

    // Show error state
    function showError(input, message) {
        input.classList.remove('valid');
        input.classList.add('invalid', 'shake');
        
        // Remove shake animation after it completes
        setTimeout(() => input.classList.remove('shake'), 500);

        // Remove existing error message if any
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) existingError.remove();

        // Add new error message
        const error = document.createElement('div');
        error.className = 'error-message';
        error.textContent = message;
        input.parentNode.appendChild(error);
    }

    // Real-time validation
    form.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('input', function() {
            validateInput(this);
        });

        input.addEventListener('blur', function() {
            validateInput(this);
        });
    });

    // Validate individual input
    function validateInput(input) {
        const value = input.value.trim();
        
        if (input.name === 'phone') {
            if (!patterns.phone.test(value)) {
                showError(input, 'Số điện thoại không hợp lệ (phải có 10 số và bắt đầu bằng 0 hoặc +84)');
                return false;
            }
        }
        else if (input.name === 'name') {
            if (!patterns.name.test(value)) {
                showError(input, 'Tên phải có ít nhất 2 ký tự và không chứa số hoặc ký tự đặc biệt');
                return false;
            }
        }
        else if (input.name === 'address') {
            if (!patterns.address.test(value)) {
                showError(input, 'Địa chỉ phải có ít nhất 10 ký tự');
                return false;
            }
        }

        showSuccess(input);
        return true;
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        // Kiểm tra giỏ hàng trống
        const cartEmpty = document.querySelector('.products-section p')?.textContent.includes('Không có sản phẩm nào trong giỏ hàng');
        
        if (cartEmpty) {
            e.preventDefault();
            Swal.fire({
                title: 'Giỏ hàng trống!',
                text: 'Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán.',
                icon: 'warning',
                confirmButtonText: 'Đi mua sắm',
                confirmButtonColor: '#1a73e8',
                showCancelButton: true,
                cancelButtonText: 'Đóng',
                width: '400px',
                customClass: {
                    container: 'my-swal'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'product.php';
                }
            });
            return;
        }

        let isValid = true;
        
        // Validate all inputs
        form.querySelectorAll('.form-input').forEach(input => {
            if (!validateInput(input)) {
                isValid = false;
                input.classList.add('shake');
                setTimeout(() => input.classList.remove('shake'), 500);
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Auto-format phone number
    const phoneInput = form.querySelector('input[name="phone"]');
    phoneInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (!value.startsWith('0')) {
                value = '0' + value;
            }
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
        }
        this.value = value;
    });

    // Payment method change handler
    paymentSelect.addEventListener('change', function() {
        if (this.value === 'bank') {
            bankInfo.style.display = 'block';
            bankInfo.style.animation = 'slideIn 0.3s ease';
        } else {
            bankInfo.style.display = 'none';
        }
    });

    // Add floating labels animation
    form.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('focus', () => {
            input.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', () => {
            if (!input.value) {
                input.parentNode.classList.remove('focused');
            }
        });
    });
});