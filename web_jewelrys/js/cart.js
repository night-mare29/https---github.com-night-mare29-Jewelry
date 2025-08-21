document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form thêm vào giỏ hàng
    const cartForms = document.querySelectorAll('.add-to-cart-form');
    cartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
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
});