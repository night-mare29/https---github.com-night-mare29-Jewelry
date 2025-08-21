class Notification {
    constructor() {
        this.init();
    }

    init() {
        // Tạo container cho notification
        this.container = document.createElement('div');
        this.container.className = 'notification-container';
        document.body.appendChild(this.container);
    }

    show(options = {}) {
        const {
            type = 'success',
            title = 'Thành công',
            message = '',
            duration = 3000
        } = options;

        // Tạo notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;

        // Tạo checkmark nếu là success
        let iconHtml = '';
        if (type === 'success') {
            iconHtml = `
                <div class="checkmark-circle">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            `;
        }

        // Nội dung notification
        notification.innerHTML = `
            ${iconHtml}
            <div class="notification-content">
                <h4 class="notification-title">${title}</h4>
                <p class="notification-message">${message}</p>
            </div>
            <button class="notification-close">&times;</button>
        `;

        // Thêm vào container
        this.container.appendChild(notification);

        // Show animation
        requestAnimationFrame(() => {
            this.container.classList.add('show');
        });

        // Xử lý nút close
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.hide(notification);
        });

        // Tự động ẩn sau duration
        setTimeout(() => {
            this.hide(notification);
        }, duration);
    }

    hide(notification) {
        // Hide animation
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        
        // Xóa notification sau khi animation kết thúc
        setTimeout(() => {
            notification.remove();
            // Ẩn container nếu không còn notification nào
            if (this.container.children.length === 0) {
                this.container.classList.remove('show');
            }
        }, 500);
    }
}

// Tạo instance global
window.notification = new Notification();

// Hàm helper để show notification
function showNotification(options) {
    window.notification.show(options);
}

// Thêm style vào head
document.head.insertAdjacentHTML('beforeend', `
    <link rel="stylesheet" href="css/notification.css">
`);