/**
 * 🔹 AUTO-PREENCHER FORMULÁRIO AO VINDAR DA TELA DE VISUALIZAÇÃO
 * Adicione este código na sua tela de edição (sessoes.html)
 */
document.addEventListener('DOMContentLoaded', () => {
    
    // 1️⃣ Verifica se veio com ?editar=ID na URL
    const params = new URLSearchParams(window.location.search);
    const idParaEditar = params.get('editar');

    if (idParaEditar) {
        // 2️⃣ Carrega sessões do localStorage
        const sessions = JSON.parse(localStorage.getItem('sessions')) || [];
        
        // 3️⃣ Busca a sessão pelo ID
        const sessao = sessions.find(s => s.id === parseInt(idParaEditar));
        
        if (sessao) {
            // 4️⃣ Preenche os campos do seu formulário de edição
            // ⚠️ Ajuste os IDs/names conforme os inputs da SUA tela de edição
            document.getElementById('metaInput');
            document.getElementById('dateInput');
            document.getElementById('hourInput');
            document.getElementById('minuteInput');

            // 5️⃣ (Opcional) Rola a página até o formulário ou destaca-o
            const form = document.querySelector('.form-edicao'); // Ajuste o seletor
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
                form.style.boxShadow = '0 0 0 3px rgba(167, 139, 250, 0.5)';
                setTimeout(() => form.style.boxShadow = '', 2000);
            }

            // 6️⃣ Limpa a URL para não recarregar a edição ao atualizar a página
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
});