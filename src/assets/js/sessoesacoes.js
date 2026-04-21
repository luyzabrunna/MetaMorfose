/**
 * 🔹 Sessões - Ações de Editar e Excluir
 * Funciona com localStorage para persistência local
 */

// Array de sessões (carrega do localStorage ou usa padrão)
let sessions = JSON.parse(localStorage.getItem('sessions')) || [
    { id: 1, name: 'Estudar Java', date: '2026-04-30', duration: 60, goal: 1200 },
    { id: 2, name: 'Estudar JavaScript', date: '2026-04-29', duration: 45, goal: 60 }
];

let deleteTargetId = null;

// === Inicialização ===
document.addEventListener('DOMContentLoaded', () => {
    // Anima barras de progresso
    animateProgressBars();
    
    // Event listeners para botões de ação
    setupActionButtons();
    
    // Event listeners para modais
    setupModals();
});

// === Animação das barras de progresso ===
function animateProgressBars() {
    document.querySelectorAll('.progress-bar-fill').forEach(bar => {
        const targetWidth = bar.getAttribute('data-width');
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 200);
    });
}

// === Configurar botões Editar/Excluir ===
function setupActionButtons() {
    // Botões de editar
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.id);
            openEditModal(id);
        });
    });
    
    // Botões de excluir
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.id);
            const nome = btn.dataset.nome;
            openDeleteModal(id, nome);
        });
    });
}

// === Configurar modais ===
function setupModals() {
    // Fechar modal ao clicar no X ou botão cancelar
    document.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(btn.dataset.close);
        });
    });
    
    // Fechar modal ao clicar fora
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                if (overlay.id === 'modal-delete') deleteTargetId = null;
            }
        });
    });
    
    // Fechar com tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                modal.classList.remove('active');
            });
            document.body.style.overflow = '';
            deleteTargetId = null;
        }
    });
    
    // Submit do formulário de edição
    document.getElementById('form-edit')?.addEventListener('submit', handleEditSubmit);
    
    // Confirmar exclusão
    document.getElementById('btn-confirm-delete')?.addEventListener('click', confirmDelete);
}

// === Abrir Modal de Edição ===
function openEditModal(id) {
    const session = sessions.find(s => s.id === id);
    if (!session) return;
    
    document.getElementById('edit-id').value = session.id;
    document.getElementById('edit-nome').value = session.name;
    document.getElementById('edit-data').value = session.date;
    document.getElementById('edit-duracao').value = session.duration;
    document.getElementById('edit-meta').value = session.goal;
    
    openModal('modal-edit');
}

// === Salvar Edição ===
function handleEditSubmit(e) {
    e.preventDefault();
    
    const id = parseInt(document.getElementById('edit-id').value);
    const nome = document.getElementById('edit-nome').value.trim();
    const data = document.getElementById('edit-data').value;
    const duracao = parseInt(document.getElementById('edit-duracao').value);
    const meta = parseInt(document.getElementById('edit-meta').value);
    
    if (!nome || !data || isNaN(duracao) || isNaN(meta) || meta <= 0) {
        showToast('Preencha todos os campos corretamente!', 'error');
        return;
    }
    
    const index = sessions.findIndex(s => s.id === id);
    if (index !== -1) {
        sessions[index] = { ...sessions[index], name: nome, date: data, duration: duracao, goal: meta };
        saveSessions();
        closeModal('modal-edit');
        showToast('Sessão atualizada com sucesso!', 'success');
        // Opcional: location.reload(); para atualizar a UI
    }
}

// === Abrir Modal de Exclusão ===
function openDeleteModal(id, nome) {
    deleteTargetId = id;
    document.getElementById('delete-nome-display').textContent = `"${nome}"`;
    openModal('modal-delete');
}

// === Confirmar Exclusão ===
function confirmDelete() {
    if (!deleteTargetId) return;
    
    sessions = sessions.filter(s => s.id !== deleteTargetId);
    saveSessions();
    
    closeModal('modal-delete');
    showToast('Sessão excluída!', 'error');
    
    // Opcional: remover o card visualmente
    const card = document.querySelector(`.session-card[data-id="${deleteTargetId}"]`);
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(-10px)';
        setTimeout(() => card.remove(), 300);
    }
    
    deleteTargetId = null;
}

// === Utilitários ===
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    document.body.style.overflow = '';
    if (modalId === 'modal-delete') deleteTargetId = null;
}

function saveSessions() {
    localStorage.setItem('sessions', JSON.stringify(sessions));
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' 
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
    
    toast.innerHTML = `${icon}<span class="toast-message">${message}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 3000);
}