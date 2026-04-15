// ═════ ANIMAÇÃO DAS BARRAS DE PROGRESSO ═════
function animateProgressBars() {
    const bars = document.querySelectorAll('.progress-bar-fill[data-width]');
    bars.forEach((bar, idx) => {
        setTimeout(() => {
            bar.style.width = bar.getAttribute('data-width');
        }, 300 + idx * 80);
    });
}

// ═════ INICIALIZAÇÃO ═════
document.addEventListener('DOMContentLoaded', animateProgressBars);