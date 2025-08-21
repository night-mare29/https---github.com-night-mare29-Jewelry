document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form thêm vào giỏ hàng
    const cartForms = document.querySelectorAll('.form-cart');
    cartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Chỉ xử lý nút thêm vào giỏ hàng
            if (!e.submitter || e.submitter.name !== 'add_to_cart') return;
            
            e.preventDefault();
            const formData = new FormData(this);

            fetch('cart_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hiển thị thông báo thành công với SweetAlert2
                    Swal.fire({
                        title: 'Thành công!',
                        text: `Đã thêm ${data.product_name} vào giỏ hàng`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Cập nhật số lượng trong giỏ hàng trên header nếu có
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    }
                } else {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Đã có lỗi xảy ra',
                    icon: 'error'
                });
            });
        });
    });

    // Tạo modal login
    const modalHTML = `
        <div id="loginModal" class="login-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>🔒 Yêu cầu đăng nhập</h4>
                </div>
                <div class="modal-body">
                    <p>Vui lòng đăng nhập để tiếp tục mua hàng</p>
                    <div class="modal-buttons">
                        <button class="btn-cancel">Hủy</button>
                        <button class="btn-login">Đăng nhập</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Thêm styles cho modal
    const modalStyle = document.createElement('style');
    modalStyle.textContent = `
        .login-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }
        
        .login-modal .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            min-width: 320px;
            animation: slideIn 0.3s ease;
        }
        
        .login-modal .modal-header {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .login-modal .modal-header h4 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.5rem;
        }
        
        .login-modal .modal-body p {
            margin: 0 0 20px;
            color: #666;
            text-align: center;
        }
        
        .login-modal .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .login-modal button {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .login-modal .btn-cancel {
            background: #e9ecef;
            color: #444;
        }
        
        .login-modal .btn-login {
            background: #4e73df;
            color: white;
        }
        
        .login-modal .btn-cancel:hover {
            background: #dde2e6;
        }
        
        .login-modal .btn-login:hover {
            background: #2e59d9;
            transform: translateY(-2px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }
    `;
    document.head.appendChild(modalStyle);
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const modal = document.getElementById('loginModal');
    const buyNowButtons = document.querySelectorAll('button[name="buy_now"]');

    buyNowButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Kiểm tra đăng nhập
            if (!document.querySelector('meta[name="user-logged-in"]')) {
                e.preventDefault();
                modal.style.display = 'block';
            }
        });
    });

    // Xử lý nút trong modal
    const btnCancel = modal.querySelector('.btn-cancel');
    const btnLogin = modal.querySelector('.btn-login');

    btnCancel.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    btnLogin.addEventListener('click', () => {
        window.location.href = 'user/login.php';
    });

    // Click ngoài modal để đóng
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});