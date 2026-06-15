/**
 * Página: Minhas Sessões
 * Apenas visualização — sem botões de editar/excluir
 */

document.addEventListener('DOMContentLoaded', () => {
    carregarSessoes();
});

async function carregarSessoes() {
    const grid = document.querySelector('.sessoes-grid');
    if (!grid) return;

    grid.innerHTML = '<p class="empty-state" style="text-align:center; padding:40px;">Carregando sessões...</p>';

    try {
        const res = await fetch('../../app/controllers/MetaController.php?acao=listar_todas_sessoes', {
            credentials: 'include'
        });
        const json = await res.json();

        if (json.status === 'success') {
            renderizarSessoes(json.data, grid);
        } else {
            grid.innerHTML = `<p class="empty-state">Erro ao carregar sessões: ${json.message}</p>`;
        }
    } catch (e) {
        console.error('Erro:', e);
        grid.innerHTML = '<p class="empty-state">Erro de conexão. Tente novamente.</p>';
    }
}

function renderizarSessoes(sessoes, grid) {
    if (!sessoes || sessoes.length === 0) {
        grid.innerHTML = `
            <div style="text-align:center; padding:60px 20px;">
                <p style="font-size:1.1rem; color:#888; margin-bottom:16px;">
                    Você ainda não registrou nenhuma sessão.
                </p>
                <button onclick="window.location.href='sessaoestudo.php'" class="btn-criar" style="padding:12px 28px;">
                    Registrar primeira sessão
                </button>
            </div>
        `;
        return;
    }

    grid.innerHTML = sessoes.map(sessao => {
        const dataFormatada = formatarData(sessao.data);
        const tempoDecimal = parseFloat(sessao.tempo_estudado) || 0;
        const tempoFormatado = formatarTempo(tempoDecimal);
        const horasPlanejadas = parseFloat(sessao.horas_planejadas) || 0;

        const progressoMeta = horasPlanejadas > 0
            ? Math.min(100, Math.round((tempoDecimal / horasPlanejadas) * 100))
            : 0;

        const foco = parseInt(sessao.foco) || 0;
        const progresso = parseInt(sessao.progresso) || 0;
        const focoPercent = (foco / 10) * 100;
        const progressoPercent = (progresso / 10) * 100;

        return `
            <div class="sessao-card">

                <div class="sessao-header">
                    <span class="sessao-titulo">${escapeHtml(sessao.meta_titulo)}</span>
                    <div class="sessao-meta">
                        <span class="sessao-data">${dataFormatada}</span>
                        <i class="fa-regular fa-clock sessao-icone"></i>
                    </div>
                </div>

                <div class="sessao-progresso">
                    <div class="barra-fundo">
                        <div class="barra-preenchida roxo-principal" style="width: ${progressoMeta}%"></div>
                    </div>
                    <div class="progresso-info">
                        <span class="progresso-label">${tempoFormatado} estudadas</span>
                        <span class="progresso-valor">${progressoMeta}%</span>
                    </div>
                </div>

                <div class="sessao-progresso">
                    <div class="barra-fundo">
                        <div class="barra-preenchida roxo-escuro" style="width: ${focoPercent}%"></div>
                    </div>
                    <div class="progresso-info">
                        <span class="progresso-label">nível de foco</span>
                        <span class="progresso-valor">${foco} de 10</span>
                    </div>
                </div>

                <div class="sessao-progresso">
                    <div class="barra-fundo">
                        <div class="barra-preenchida roxo-claro" style="width: ${progressoPercent}%"></div>
                    </div>
                    <div class="progresso-info">
                        <span class="progresso-label">percepção de progresso</span>
                        <span class="progresso-valor">${progresso} de 10</span>
                    </div>
                </div>

                ${sessao.observacao ? `<p class="sessao-obs" style="margin-top:10px; font-size:0.85rem; color:#888;">${escapeHtml(sessao.observacao)}</p>` : ''}

            </div>
        `;
    }).join('');
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

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}