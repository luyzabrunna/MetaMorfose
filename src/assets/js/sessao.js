/**
 * Página: Nova Sessão de Estudo / Editar Sessão
 * Integrado ao MetaController.php
 */

// ── ESTADO GLOBAL ──
let metasList = [];
let modoEdicao = false;
let sessaoEditandoId = null;

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
        this.selectedId = null;

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

    setOptions(opts) {
        this.options = opts || [];
        this.onInput();
    }

    clear() {
        if (!this.input) return;
        this.input.value = '';
        this.selectedId = null;
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

        if (this.filteredOptions.length === 0) {
            html = `<div class="combobox-empty">Nenhuma meta encontrada.</div>`;
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
    }

    selectOption(id, name) {
        if (!this.input || !this.clearBtn) return;
        this.input.value = name;
        this.selectedId = id;
        this.clearBtn.classList.add('visible');
        this.highlightIndex = -1;
        this.close();
        if (this.onSelect) this.onSelect(id, name);
    }

    setValue(id, name) {
        this.selectOption(id, name);
    }

    getSelectedId() {
        return this.selectedId;
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

// ── INSTÂNCIA GLOBAL DO COMBOBOX ──
let metaCombobox = null;

// ── INICIALIZAÇÃO ──
document.addEventListener('DOMContentLoaded', async () => {
    await carregarMetas();
    initSliders();
    initDateInput();
    initFormActions();
    verificarModoEdicao();
});

// ── BUSCA METAS REAIS DA API ──
async function carregarMetas() {
    try {
        const res = await fetch('../controllers/MetaController.php?acao=listar_metas', {
            credentials: 'include'
        });
        const json = await res.json();

        if (json.status === 'success') {
            metasList = json.data.map(m => ({ id: String(m.id), name: m.titulo }));
        } else {
            metasList = [];
        }
    } catch (e) {
        console.error('Erro ao carregar metas:', e);
        metasList = [];
    }

    // Inicia combobox depois de ter os dados
    metaCombobox = new Combobox('metaInput', 'metaDropdown', 'metaClear', metasList, (id, name) => {
        console.log('Meta selecionada:', id, name);
    });
    metaCombobox.setOptions(metasList);
}

// ── VERIFICA SE É EDIÇÃO (URL: ?sessao_id=X&meta_id=Y) ──
async function verificarModoEdicao() {
    const params = new URLSearchParams(window.location.search);
    sessaoEditandoId = params.get('sessao_id');
    const metaIdParam = params.get('meta_id');

    if (sessaoEditandoId) {
        modoEdicao = true;
        document.querySelector('.form-title') && (document.querySelector('.form-title').textContent = 'Editar Sessão de Estudo');
        const registerBtn = document.getElementById('registerBtn');
        if (registerBtn) registerBtn.textContent = 'SALVAR ALTERAÇÕES';

        // Busca sessões da meta para preencher o form
        if (metaIdParam) {
            await preencherFormEdicao(sessaoEditandoId, metaIdParam);
        }
    } else if (metaIdParam) {
        // Veio da tela de detalhes com meta pré-selecionada
        aguardarComboboxESelecionar(metaIdParam);
    }
}

// Aguarda o combobox carregar e pré-seleciona a meta
function aguardarComboboxESelecionar(metaId) {
    const tentativa = setInterval(() => {
        if (metaCombobox && metasList.length > 0) {
            clearInterval(tentativa);
            const meta = metasList.find(m => m.id === String(metaId));
            if (meta) metaCombobox.setValue(meta.id, meta.name);
        }
    }, 100);
}

// Preenche o formulário no modo edição
async function preencherFormEdicao(sessaoId, metaId) {
    try {
        const res = await fetch(`../controllers/MetaController.php?acao=listar_sessoes_meta&meta_id=${metaId}`, {
            credentials: 'include'
        });
        const json = await res.json();

        if (json.status === 'success') {
            const sessao = json.data.find(s => String(s.id) === String(sessaoId));
            if (!sessao) return;

            // Preenche data
            const dateInput = document.getElementById('dateInput');
            if (dateInput) dateInput.value = sessao.data;

            // Preenche observação
            const obsInput = document.getElementById('obsInput');
            if (obsInput) obsInput.value = sessao.observacao || '';

            // Preenche tempo (converte decimal para horas/minutos)
            const tempo = parseFloat(sessao.tempo_estudado) || 0;
            const horas = Math.floor(tempo);
            const minutos = Math.round((tempo - horas) * 60);
            const hourInput = document.getElementById('hourInput');
            const minuteInput = document.getElementById('minuteInput');
            if (hourInput) hourInput.value = horas || '';
            if (minuteInput) minuteInput.value = minutos || '';

            // Preenche sliders
            if (sessao.foco) setSliderValue('focus', parseInt(sessao.foco));
            if (sessao.progresso) setSliderValue('progress', parseInt(sessao.progresso));

            // Pré-seleciona a meta
            aguardarComboboxESelecionar(metaId);
        }
    } catch (e) {
        console.error('Erro ao carregar sessão para edição:', e);
    }
}

// Define valor de um slider programaticamente
function setSliderValue(sliderType, value) {
    const wrapper = document.querySelector(`.slider-wrapper[data-slider="${sliderType}"]`);
    if (!wrapper) return;

    const fill = wrapper.querySelector('.slider-fill');
    const thumb = wrapper.querySelector('.slider-thumb');
    const valueDisplay = wrapper.querySelector('.slider-value');

    const leftPercent = (value - 1) / 9 * 100;
    if (fill) fill.style.width = leftPercent + '%';
    if (thumb) { thumb.style.left = leftPercent + '%'; thumb.setAttribute('data-value', value); }
    if (valueDisplay) valueDisplay.textContent = value;
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

    const startDrag = (e) => { isDragging = true; e.preventDefault(); };
    const moveDrag = (e) => {
        if (isDragging) {
            const clientX = e.clientX || e.touches?.[0]?.clientX;
            if (clientX) updateSlider(clientX);
        }
    };
    const endDrag = () => { isDragging = false; };

    thumb.addEventListener('mousedown', startDrag);
    track.addEventListener('mousedown', (e) => { startDrag(e); updateSlider(e.clientX); });
    document.addEventListener('mousemove', moveDrag);
    document.addEventListener('mouseup', endDrag);

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
    const registerBtn = document.getElementById('registerBtn');
    if (registerBtn) registerBtn.addEventListener('click', handleRegister);

    const modalOkBtn = document.getElementById('modalOkBtn');
    if (modalOkBtn) {
        modalOkBtn.addEventListener('click', () => {
            const modal = document.getElementById('successModal');
            if (modal) modal.classList.remove('active');
            if (modoEdicao) {
                window.location.href = 'visualizacaosessao.php';
            } else {
                resetForm();
            }
        });
    }

    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            window.history.back();
        });
    }
}

// ── PEGA VALOR DO SLIDER ──
function getSliderValue(sliderType) {
    const wrapper = document.querySelector(`.slider-wrapper[data-slider="${sliderType}"]`);
    if (!wrapper) return null;
    const thumb = wrapper.querySelector('.slider-thumb');
    return thumb ? parseInt(thumb.getAttribute('data-value')) : null;
}

// ── VALIDAÇÃO E ENVIO ──
async function handleRegister() {
    const metaId = metaCombobox?.getSelectedId();
    const dateInput = document.getElementById('dateInput');
    const hourInput = document.getElementById('hourInput');
    const minuteInput = document.getElementById('minuteInput');
    const obsInput = document.getElementById('obsInput');

    const date = dateInput?.value;
    const horas = parseInt(hourInput?.value) || 0;
    const minutos = parseInt(minuteInput?.value) || 0;
    const observacao = obsInput?.value.trim() || '';
    const foco = getSliderValue('focus');
    const progresso = getSliderValue('progress');

    // Validação
    if (!metaId) { showToast('Por favor, selecione uma meta.'); return; }
    if (!date) { showToast('Por favor, selecione a data.'); return; }
    if (horas === 0 && minutos === 0) { showToast('Por favor, informe o tempo estudado.'); return; }

    // Converte para decimal (ex: 1h 30min = 1.5)
    const tempoDecimal = horas + (minutos / 60);

    const payload = {
        meta_id: metaId,
        data: date,
        tempo_estudado: tempoDecimal,
        observacao,
        foco,
        progresso
    };

    const registerBtn = document.getElementById('registerBtn');
    if (registerBtn) { registerBtn.disabled = true; registerBtn.textContent = 'Salvando...'; }

    try {
        let acao, mensagemSucesso;

        if (modoEdicao) {
            acao = 'atualizar_sessao';
            payload.id = sessaoEditandoId;
            mensagemSucesso = 'Sessão atualizada!';
        } else {
            acao = 'criar_sessao';
            mensagemSucesso = 'Sessão registrada!';
        }

        const res = await fetch('../controllers/MetaController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ acao, ...payload }),
            credentials: 'include'
        });

        const json = await res.json();

        if (json.status === 'success') {
            // Atualiza texto do modal
            const modalMsg = document.querySelector('.modal p');
            if (modalMsg) modalMsg.textContent = mensagemSucesso + ' Continue assim!';
            const modal = document.getElementById('successModal');
            if (modal) modal.classList.add('active');
        } else {
            showToast('Erro: ' + json.message);
        }
    } catch (e) {
        console.error('Erro:', e);
        showToast('Erro de conexão. Tente novamente.');
    } finally {
        if (registerBtn) {
            registerBtn.disabled = false;
            registerBtn.textContent = modoEdicao ? 'SALVAR ALTERAÇÕES' : 'REGISTRAR';
        }
    }
}

// ── RESET DO FORMULÁRIO ──
function resetForm() {
    const metaInput = document.getElementById('metaInput');
    const metaClear = document.getElementById('metaClear');
    if (metaInput) metaInput.value = '';
    if (metaClear) metaClear.classList.remove('visible');
    if (metaCombobox) metaCombobox.selectedId = null;

    const obsInput = document.getElementById('obsInput');
    if (obsInput) obsInput.value = '';

    const hourInput = document.getElementById('hourInput');
    const minuteInput = document.getElementById('minuteInput');
    if (hourInput) hourInput.value = '';
    if (minuteInput) minuteInput.value = '';

    const dateInput = document.getElementById('dateInput');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

    setSliderValue('focus', 7);
    setSliderValue('progress', 6);
}

// ── TOAST ──
function showToast(message) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}