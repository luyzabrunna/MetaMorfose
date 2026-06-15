document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const metaId = urlParams.get('id');

    if (!metaId) {
        console.warn('ID da meta não encontrado na URL');
        return;
    }

    await carregarMeta(metaId);
});

// ── CARREGA DADOS DA META ──
async function carregarMeta(metaId) {
    try {
        const res = await fetch(`../../app/controllers/MetaController.php?acao=detalhe_meta&id=${metaId}`, {
            credentials: 'include'
        });
        const json = await res.json();

        if (json.status === 'success') {
            preencherMeta(json.meta);
            preencherSessoes(json.sessoes, json.meta);
            iniciarBotoesMeta(metaId);
        } else {
            alert('Erro ao carregar meta: ' + json.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro de conexão ao carregar meta.');
    }
}

// ── PREENCHE DADOS DA META ──
function preencherMeta(meta) {
    const tituloEl = document.querySelector('.meta-titulo');
    if (tituloEl) tituloEl.textContent = meta.titulo;

    const prazoEl = document.querySelector('.meta-prazo .campo-valor');
    if (prazoEl) prazoEl.textContent = new Date(meta.prazo).toLocaleDateString('pt-BR');

    const descEl = document.querySelector('.meta-descricao p');
    if (descEl) descEl.textContent = meta.descricao;

    const barraEl = document.querySelector('.barra-preenchida');
    if (barraEl) barraEl.style.width = meta.progresso + '%';

    const pctEl = document.querySelector('.porcentagem');
    if (pctEl) pctEl.textContent = Math.round(meta.progresso) + '%';

    const horasEl = document.querySelector('.horas-texto');
    if (horasEl) horasEl.textContent = `${meta.horas_estudadas}h de ${meta.horas_planejadas}h estudadas`;

    // Status badge
    const statusEl = document.querySelector('.meta-status-badge');
    if (statusEl) {
        const statusTexto = meta.status.replace('_', ' ');
        statusEl.textContent = statusTexto;
        statusEl.className = `campo-valor meta-status-badge badge ${meta.status}`;
    }

    // Botão Nova Sessão com meta_id
    const btnNovaSessao = document.getElementById('btnNovaSessao');
    if (btnNovaSessao) {
        btnNovaSessao.onclick = () => {
            window.location.href = `sessaoestudo.php?meta_id=${meta.id}`;
        };
    }
}

// ── PREENCHE SESSÕES ──
function preencherSessoes(sessoes, meta) {
    const sessoesGrid = document.querySelector('.sessoes-grid');
    if (!sessoesGrid) return;

    if (!sessoes || sessoes.length === 0) {
        sessoesGrid.innerHTML = `
            <div style="text-align:center; padding:30px 20px;">
                <p style="color:#888; margin-bottom:14px;">
                    Crie uma sessão para ficar menos um passo de realizar sua meta.
                </p>
                <button
                    onclick="window.location.href='sessaoestudo.php?meta_id=${meta.id}'"
                    class="btn-nova-sessao"
                    style="padding:10px 24px;"
                >
                    Nova sessão
                </button>
            </div>
        `;
        return;
    }

    // Mostra apenas as 3 mais recentes
    const recentes = sessoes.slice(0, 3);

    sessoesGrid.innerHTML = recentes.map(sessao => {
        const dataFormatada = formatarData(sessao.data);
        const tempoFormatado = formatarTempo(parseFloat(sessao.tempo_estudado) || 0);
        const foco = parseInt(sessao.foco) || 0;
        const progresso = parseInt(sessao.progresso) || 0;

        return `
            <div class="card">

                <div class="sessao-header">
                    <span class="sessao-titulo">${meta.titulo}</span>
                    <span class="sessao-data">${dataFormatada} <i class="fa-regular fa-clock"></i></span>
                </div>

                <p class="sessao-horas">${tempoFormatado} de ${meta.horas_planejadas}h</p>

                ${foco ? `
                <div class="sessao-avaliacao">
                    <span class="avaliacao-label">Nível de foco</span>
                    <div class="barra-fundo">
                        <div class="barra-preenchida" style="width:${(foco/10)*100}%"></div>
                    </div>
                    <span class="avaliacao-valor">${foco} de 10</span>
                </div>` : ''}

                ${progresso ? `
                <div class="sessao-avaliacao">
                    <span class="avaliacao-label">Percepção de progresso</span>
                    <div class="barra-fundo">
                        <div class="barra-preenchida gradiente-2" style="width:${(progresso/10)*100}%"></div>
                    </div>
                    <span class="avaliacao-valor">${progresso} de 10</span>
                </div>` : ''}

                ${sessao.observacao ? `<p class="sessao-obs">${sessao.observacao}</p>` : ''}

                <div class="sessao-acoes">
                    <button class="btn-editar" onclick="editarSessao(${sessao.id}, ${meta.id})">Editar</button>
                    <button class="btn-excluir" onclick="excluirSessao(${sessao.id}, ${meta.id})">Excluir</button>
                </div>

            </div>
        `;
    }).join('');
}

// ── BOTÕES DA META ──
function iniciarBotoesMeta(metaId) {
    const btnEditar = document.querySelector('.meta-acoes .btn-editar');
    if (btnEditar) btnEditar.addEventListener('click', () => editarMeta(metaId));

    const btnExcluir = document.querySelector('.meta-acoes .btn-excluir');
    if (btnExcluir) btnExcluir.addEventListener('click', () => excluirMeta(metaId));
}

// ── EDITAR META ──
function editarMeta(metaId) {
    window.location.href = `criarmeta.php?id=${metaId}`;
}

// ── EXCLUIR META ──
async function excluirMeta(metaId) {
    if (!confirm('Tem certeza que deseja excluir esta meta? Todas as sessões vinculadas também serão excluídas.')) return;

    try {
        const res = await fetch('../../app/controllers/MetaController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ acao: 'excluir_meta', id: metaId }),
            credentials: 'include'
        });
        const json = await res.json();

        if (json.status === 'success') {
            alert('Meta excluída com sucesso!');
            window.location.href = 'visualizacaometas.php';
        } else {
            alert('Erro ao excluir: ' + json.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro de conexão ao excluir meta.');
    }
}

// ── EDITAR SESSÃO ──
function editarSessao(sessaoId, metaId) {
    window.location.href = `sessaoestudo.php?sessao_id=${sessaoId}&meta_id=${metaId}`;
}

// ── EXCLUIR SESSÃO ──
async function excluirSessao(sessaoId, metaId) {
    if (!confirm('Tem certeza que deseja excluir esta sessão?')) return;

    try {
        const res = await fetch('../../app/controllers/MetaController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ acao: 'excluir_sessao', id: sessaoId }),
            credentials: 'include'
        });
        const json = await res.json();

        if (json.status === 'success') {
            await carregarMeta(metaId);
        } else {
            alert('Erro ao excluir sessão: ' + json.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro de conexão ao excluir sessão.');
    }
}

// ── HELPERS ──
function formatarData(dataStr) {
    if (!dataStr) return '';
    const [ano, mes, dia] = dataStr.split('T')[0].split('-');
    return `${dia}/${mes}`;
}

function formatarTempo(decimal) {
    const horas = Math.floor(decimal);
    const minutos = Math.round((decimal - horas) * 60);
    if (horas === 0) return `${minutos}min`;
    if (minutos === 0) return `${horas}h`;
    return `${horas}h ${minutos}min`;
}