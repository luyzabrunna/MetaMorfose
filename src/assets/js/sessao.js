/**
 * Página: Nova Sessão de Estudo
 * Funcionalidades: Combobox, Sliders, Validação, Modal, Toast
 * 
 * ⚠️ Este arquivo NÃO contém funções de menu.
 * O menu deve ser carregado separadamente via menu.js
 */

// ── DADOS DAS METAS (substituir por busca via API/PHP depois) ──
const metasData = {
    1: { name: "Java" },
    2: { name: "Python" },
    3: { name: "JavaScript" },
    4: { name: "Banco de Dados" },
    5: { name: "Cloud AWS" },
    6: { name: "DevOps" }
};
const metasList = Object.entries(metasData).map(([id, data]) => ({ id, name: data.name }));

// ── CLASSE COMBOBOX ──
class Combobox {
    constructor(inputId, dropdownId, clearBtnId, options, onSelect) {
        this.input = document.getElementById(inputId);
        this.dropdown = document.getElementById(dropdownId);
        this.clearBtn = document.getElementById(clearBtnId);
        this.wrapper = this.input?.closest('.combobox-wrapper');
        this.options = options || [];
        this.onSelect = onSelect;
        this.filteredOptions = [];
        this.highlightIndex = -1;
        this.isOpen = false;

        if (!this.input) return;

        this.input.addEventListener('input', () => this.onInput());
        this.input.addEventListener('focus', () => this.open());
        this.input.addEventListener('keydown', (e) => this.onKeydown(e));
        this.input.addEventListener('blur', () => setTimeout(() => this.close(), 200));
        
        if (this.clearBtn) {
            this.clearBtn.addEventListener('mousedown', (e) => { 
                e.preventDefault(); 
                this.clear(); 
            });
        }
        
        this.input.addEventListener('click', () => { 
            if (!this.input.disabled) this.toggle(); 
        });
    }

    setOptions(opts) { this.options = opts || []; }

    clear() {
        if (!this.input) return;
        this.input.value = '';
        if (this.clearBtn) this.clearBtn.classList.remove('visible');
        this.highlightIndex = -1;
        this.renderDropdown();
    }

    toggle() { 
        if (this.input?.disabled) return;
        this.isOpen ? this.close() : this.open(); 
    }

    open() {
        if (this.input?.disabled || !this.wrapper) return;
        this.isOpen = true;
        this.wrapper.classList.add('open');
        this.onInput();
    }

    close() {
        if (!this.wrapper) return;
        this.isOpen = false;
        this.wrapper.classList.remove('open');
        this.highlightIndex = -1;
    }

    onInput() {
        if (!this.input) return;
        const query = this.input.value.toLowerCase().trim();
        this.filteredOptions = this.options.filter(opt => 
            opt.name.toLowerCase().includes(query)
        );
        this.highlightIndex = -1;
        this.renderDropdown();
    }

    renderDropdown() {
        if (!this.dropdown || !this.input) return;
        
        const query = this.input.value.toLowerCase().trim();
        let html = '';

        if (this.filteredOptions.length === 0 && query.length > 0) {
            html += `<div class="combobox-empty">Nenhum resultado encontrado</div>`;
            html += `<div class="combobox-add-option" data-value="${this.escapeHtml(this.input.value)}">
                <i class="fa-solid fa-plus" style="font-size:10px;"></i>
                Usar "<strong>${this.escapeHtml(this.input.value)}</strong>"
            </div>`;
        } else if (this.filteredOptions.length === 0) {
            html += `<div class="combobox-empty">Comece a digitar para buscar...</div>`;
        } else {
            this.filteredOptions.forEach((opt) => {
                const name = opt.name;
                const qIdx = name.toLowerCase().indexOf(query);
                let displayName;
                if (qIdx >= 0 && query.length > 0) {
                    displayName = this.escapeHtml(name.substring(0, qIdx)) +
                        `<span class="match">${this.escapeHtml(name.substring(qIdx, qIdx + query.length))}</span>` +
                        this.escapeHtml(name.substring(qIdx + query.length));
                } else {
                    displayName = this.escapeHtml(name);
                }
                html += `<div class="combobox-option" data-id="${opt.id}" data-name="${this.escapeHtml(name)}">${displayName}</div>`;
            });
        }
        this.dropdown.innerHTML = html;
        this.bindDropdownEvents();
    }

    bindDropdownEvents() {
        if (!this.dropdown) return;
        
        this.dropdown.querySelectorAll('.combobox-option').forEach(el => {
            el.addEventListener('mousedown', (e) => {
                e.preventDefault();
                this.selectOption(el.getAttribute('data-id'), el.getAttribute('data-name'));
            });
        });
        this.dropdown.querySelectorAll('.combobox-add-option').forEach(el => {
            el.addEventListener('mousedown', (e) => {
                e.preventDefault();
                const val = el.getAttribute('data-value');
                this.selectOption(val, val);
            });
        });
    }

    selectOption(id, name) {
        if (!this.input || !this.clearBtn) return;
        this.input.value = name;
        this.clearBtn.classList.add('visible');
        this.highlightIndex = -1;
        this.close();
        if (this.onSelect) this.onSelect(id, name);
    }

    onKeydown(e) {
        if (!this.dropdown) return;
        
        const items = this.dropdown.querySelectorAll('.combobox-option');
        const count = items.length;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!this.isOpen) this.open();
            if (count > 0) {
                this.highlightIndex = Math.min(this.highlightIndex + 1, count - 1);
                this.updateHighlight(items);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (count > 0) {
                this.highlightIndex = Math.max(this.highlightIndex - 1, 0);
                this.updateHighlight(items);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (this.highlightIndex >= 0 && this.highlightIndex < count) {
                const el = items[this.highlightIndex];
                this.selectOption(el.getAttribute('data-id'), el.getAttribute('data-name'));
            } else if (this.input?.value.trim().length > 0) {
                const addBtn = this.dropdown.querySelector('.combobox-add-option');
                if (addBtn) this.selectOption(addBtn.getAttribute('data-value'), this.input.value.trim());
            }
        } else if (e.key === 'Escape') {
            this.close();
        }
    }

    updateHighlight(items) {
        items.forEach((el, i) => el.classList.toggle('highlighted', i === this.highlightIndex));
        if (this.highlightIndex >= 0 && items[this.highlightIndex]) {
            items[this.highlightIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}

// ── INICIALIZAÇÃO DA PÁGINA ──
document.addEventListener('DOMContentLoaded', () => {
    initCombobox();
    initSliders();
    initDateInput();
    initFormActions();
    initSidebarButtons();
    initNavigation();
});

// ── COMBOBOX ──
function initCombobox() {
    const metaInput = document.getElementById('metaInput');
    if (!metaInput) return;
    
    new Combobox('metaInput', 'metaDropdown', 'metaClear', metasList, (id, name) => {
        console.log('Meta selecionada:', id, name);
        // Aqui você pode adicionar lógica extra ao selecionar uma meta
    });
}

// ── SLIDERS ──
function initSliders() {
    document.querySelectorAll('.slider-wrapper').forEach(initSlider);
}

function initSlider(wrapper) {
    const track = wrapper.querySelector('.slider-track');
    const fill = wrapper.querySelector('.slider-fill');
    const thumb = wrapper.querySelector('.slider-thumb');
    const valueDisplay = wrapper.querySelector('.slider-value');
    
    if (!track || !fill || !thumb || !valueDisplay) return;
    
    let isDragging = false;

    function updateSlider(clientX) {
        const rect = track.getBoundingClientRect();
        let percent = (clientX - rect.left) / rect.width;
        percent = Math.max(0, Math.min(1, percent));
        const displayValue = Math.max(1, Math.min(10, Math.round(percent * 9) + 1));
        const leftPercent = (displayValue - 1) / 9 * 100;
        
        fill.style.width = leftPercent + '%';
        thumb.style.left = leftPercent + '%';
        thumb.setAttribute('data-value', displayValue);
        valueDisplay.textContent = displayValue;
    }

    const startDrag = (e) => { 
        isDragging = true; 
        e.preventDefault(); 
    };
    
    const moveDrag = (e) => { 
        if (isDragging) {
            const clientX = e.clientX || e.touches?.[0]?.clientX;
            if (clientX) updateSlider(clientX);
        }
    };
    
    const endDrag = () => { isDragging = false; };

    // Mouse events
    thumb.addEventListener('mousedown', startDrag);
    track.addEventListener('mousedown', (e) => { 
        startDrag(e); 
        updateSlider(e.clientX); 
    });
    document.addEventListener('mousemove', moveDrag);
    document.addEventListener('mouseup', endDrag);

    // Touch events
    thumb.addEventListener('touchstart', startDrag, { passive: false });
    track.addEventListener('touchstart', (e) => { 
        startDrag(e); 
        if (e.touches[0]) updateSlider(e.touches[0].clientX); 
    }, { passive: false });
    document.addEventListener('touchmove', moveDrag, { passive: false });
    document.addEventListener('touchend', endDrag);
}

// ── DATA INPUT ──
function initDateInput() {
    const dateInput = document.getElementById('dateInput');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
}

// ── AÇÕES DO FORMULÁRIO ──
function initFormActions() {
    // Botão Registrar
    const registerBtn = document.getElementById('registerBtn');
    if (registerBtn) {
        registerBtn.addEventListener('click', handleRegister);
    }

    // Botão Modal OK
    const modalOkBtn = document.getElementById('modalOkBtn');
    if (modalOkBtn) {
        modalOkBtn.addEventListener('click', () => {
            const modal = document.getElementById('successModal');
            if (modal) modal.classList.remove('active');
            resetForm();
            showToast('Sessão salva com sucesso!');
        });
    }

    // Botão Cancelar
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            resetForm();
            showToast('Formulário limpo.');
        });
    }
}

// ── BOTÕES DA SIDEBAR (sem menu, apenas ações) ──
function initSidebarButtons() {
    // Botão "Criar nova meta"
    const btnNovaMeta = document.getElementById('newGoalBtn');
    if (btnNovaMeta) {
        btnNovaMeta.addEventListener('click', (e) => {
            e.preventDefault();
            showToast('Redirecionando para criação de nova meta...');
            // window.location.href = '../pages/criarmeta.html';
        });
    }

    // Botão "Sair"
    const btnSair = document.querySelector('.sidebar-sair');
    if (btnSair) {
        btnSair.addEventListener('click', (e) => {
            e.preventDefault();
            showToast('Saindo...');
            // window.location.href = '../auth/logout.php';
        });
    }
}

// ── NAVEGAÇÃO (apenas marca ativo, sem controle de menu) ──
function initNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            navItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            // Apenas marca como ativo - o menu.js cuida do resto se estiver presente
        });
    });
}

// ── VALIDAÇÃO E REGISTRO ──
function handleRegister() {
    const metaInput = document.getElementById('metaInput');
    const dateInput = document.getElementById('dateInput');
    const timeInput = document.getElementById('timeInput');
    
    const meta = metaInput?.value.trim();
    const date = dateInput?.value;
    const time = timeInput?.value.trim();

    if (!meta) { showToast('Por favor, selecione ou digite a meta.'); return; }
    if (!date) { showToast('Por favor, selecione a data.'); return; }
    if (!time) { showToast('Por favor, informe o tempo estudado.'); return; }

    const modal = document.getElementById('successModal');
    if (modal) modal.classList.add('active');
}

// ── RESET DO FORMULÁRIO ──
function resetForm() {
    // Combobox
    const metaInput = document.getElementById('metaInput');
    const metaClear = document.getElementById('metaClear');
    if (metaInput) metaInput.value = '';
    if (metaClear) metaClear.classList.remove('visible');

    // Campos de texto
    const obsInput = document.getElementById('obsInput');
    const timeInput = document.getElementById('timeInput');
    if (obsInput) obsInput.value = '';
    if (timeInput) timeInput.value = '';
    
    // Data
    const dateInput = document.getElementById('dateInput');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

    // Sliders
    document.querySelectorAll('.slider-wrapper').forEach(wrapper => {
        const fill = wrapper.querySelector('.slider-fill');
        const thumb = wrapper.querySelector('.slider-thumb');
        const valueDisplay = wrapper.querySelector('.slider-value');
        if (!fill || !thumb || !valueDisplay) return;
        
        const defaultValue = wrapper.dataset.slider === 'focus' ? 7 : 6;
        const leftPercent = (defaultValue - 1) / 9 * 100;
        fill.style.width = leftPercent + '%';
        thumb.style.left = leftPercent + '%';
        thumb.setAttribute('data-value', defaultValue);
        valueDisplay.textContent = defaultValue;
    });
}

// ── TOAST ──
function showToast(message) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}