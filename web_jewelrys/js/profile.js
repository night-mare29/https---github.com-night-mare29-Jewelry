document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const updateForm = document.getElementById('updateForm');
    const btnUpdate = document.querySelector('.btn-update');
    const formInputs = updateForm.querySelectorAll('input');
    let isFormVisible = false;

    // Tạo loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(loadingOverlay);

    // Toggle form với animation
    btnUpdate.addEventListener('click', function() {
        isFormVisible = !isFormVisible;
        updateForm.style.display = isFormVisible ? 'block' : 'none';
        
        if (isFormVisible) {
            updateForm.style.opacity = '0';
            updateForm.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                updateForm.style.opacity = '1';
                updateForm.style.transform = 'translateY(0)';
            }, 10);
        }
        
        // Cập nhật text button
        this.textContent = isFormVisible ? 'Ẩn form cập nhật' : 'Cập nhật thông tin';
    });

    // Form validation
    updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Hiển thị loading
            loadingOverlay.classList.add('active');
            
            // Giả lập loading để thấy animation
            setTimeout(() => {
                this.submit();
            }, 800);
        }
    });

    // Validate từng input khi blur
    formInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateInput(this);
        });

        // Xóa thông báo lỗi khi focus
        input.addEventListener('focus', function() {
            removeError(this);
        });
    });

    // Thêm hiệu ứng hover cho bảng đơn hàng
    const orderRows = document.querySelectorAll('tbody tr');
    orderRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
            this.style.transition = 'all 0.3s ease';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});

// Validate toàn bộ form
function validateForm() {
    let isValid = true;
    const inputs = document.querySelectorAll('#updateForm input');
    
    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Validate từng input
function validateInput(input) {
    removeError(input);
    let isValid = true;

    if (input.value.trim() !== '') {
        switch(input.type) {
            case 'email':
                if (!isValidEmail(input.value)) {
                    showError(input, 'Email không hợp lệ');
                    isValid = false;
                }
                break;
                
            case 'text':
                if (input.id === 'phone' && !isValidPhone(input.value)) {
                    showError(input, 'Số điện thoại không hợp lệ');
                    isValid = false;
                }
                if (input.id === 'username' && input.value.length < 2) {
                    showError(input, 'Tên phải có ít nhất 2 ký tự');
                    isValid = false;
                }
                break;
        }
    }
    
    return isValid;
}

// Hiển thị lỗi
function showError(input, message) {
    removeError(input);
    const error = document.createElement('div');
    error.className = 'error-message';
    error.textContent = message;
    error.style.color = '#e74c3c';
    error.style.fontSize = '14px';
    error.style.marginTop = '-15px';
    error.style.marginBottom = '15px';
    error.style.animation = 'slideDown 0.3s ease-out';
    input.parentNode.insertBefore(error, input.nextSibling);
    input.style.borderColor = '#e74c3c';
}

// Xóa thông báo lỗi
function removeError(input) {
    const error = input.parentNode.querySelector('.error-message');
    if (error) {
        error.remove();
        input.style.borderColor = '#e0e0e0';
    }
}

// Validate email
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Validate số điện thoại
function isValidPhone(phone) {
    return /^[0-9]{10,11}$/.test(phone);
}

// Responsive table
function makeTableResponsive() {
    if (window.innerWidth <= 768) {
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent);
            const dataCells = table.querySelectorAll('tbody td');
            
            dataCells.forEach((cell, index) => {
                cell.setAttribute('data-label', headers[index % headers.length]);
            });
        });
    }
}

// Gọi hàm responsive khi load trang và resize
window.addEventListener('load', makeTableResponsive);
window.addEventListener('resize', makeTableResponsive);