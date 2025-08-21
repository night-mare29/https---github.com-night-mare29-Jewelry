document.addEventListener('DOMContentLoaded', function() {
    // Animation cho các phần tử khi load trang
    const elements = document.querySelectorAll('.success-message, .next-steps, .action-buttons');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'all 0.5s ease-out';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 1000 + (index * 200));
    });

    // Hiệu ứng Confetti
    createConfetti();
});

// Tạo hiệu ứng confetti
function createConfetti() {
    const colors = ['#2c3e50', '#27ae60', '#3498db', '#e74c3c', '#f1c40f'];
    const confettiCount = 100;

    for (let i = 0; i < confettiCount; i++) {
        createConfettiPiece(colors[Math.floor(Math.random() * colors.length)]);
    }
}

function createConfettiPiece(color) {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    confetti.style.backgroundColor = color;
    confetti.style.left = Math.random() * 100 + 'vw';
    confetti.style.animationDelay = Math.random() * 3 + 's';
    confetti.style.opacity = Math.random();
    confetti.style.transform = `rotate(${Math.random() * 360}deg)`;

    document.querySelector('.success-container').appendChild(confetti);

    // Xóa confetti sau khi animation kết thúc
    confetti.addEventListener('animationend', () => {
        confetti.remove();
    });
}

// Thêm styles cho confetti
const style = document.createElement('style');
style.textContent = `
    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        pointer-events: none;
        animation: confettiFall 3s linear forwards;
    }

    @keyframes confettiFall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
        }
        100% {
            transform: translateY(100vh) rotate(360deg);
        }
    }

    .success-message, .next-steps, .action-buttons {
        opacity: 0;
        transform: translateY(20px);
    }
`;
document.head.appendChild(style);