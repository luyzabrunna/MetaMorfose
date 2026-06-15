document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.querySelector('.metas-grid');
    if (!grid) return;

    grid.innerHTML = '<p style="text-align:center; padding:40px; color:#888;">Carregando metas...</p>';

    try {
        // ✅ CAMINHO CORRIGIDO (controllers com c minúsculo)
        const res = await fetch('../../app/controllers/MetaController.php?acao=listar_metas', {
            credentials: 'include'
        });
        const json = await res.json();

        if (json.status === 'success') {
            if (json.data.length === 0) {
                grid.innerHTML = `
                    <div style="text-align:center; padding:60px 20px;">
                        <p style="font-size:1.1rem; color:#8363c6; margin-bottom:16px;">
                            Você não tem nenhuma meta ainda. Faça uma e dê seu primeiro passo.
                        </p>
                        <button onclick="window.location.href='criarmeta.php'" class="btn-criar" style="padding:12px 28px;">
                            Criar primeira meta
                        </button>
                    </div>
                `;
                return;
            }

            grid.innerHTML = json.data.map(meta => `
                <div class="meta-card" onclick="window.location.href='detalhemeta.php?id=${meta.id}'" style="cursor:pointer;">
                    <div class="meta-header">
                        <span class="meta-titulo">${meta.titulo}</span>
                        <span class="meta-prazo">até ${formatarData(meta.prazo)} <i class="fa-regular fa-clock"></i></span>
                    </div>
                    <div class="meta-progresso">
                        <div class="barra-fundo">
                            <div class="barra-preenchida" style="width: ${meta.progresso}%"></div>
                        </div>
                        <span class="meta-porcentagem">${Math.round(meta.progresso)}%</span>
                    </div>
                    <p class="meta-horas">${meta.horas_estudadas}h de ${meta.horas_planejadas}h</p>
                    <span class="meta-status ${meta.status}">${meta.status.replace('_', ' ')}</span>
                </div>
            `).join('');
        } else {
            grid.innerHTML = `<p style="text-align:center; color:#f87171;">Erro ao carregar metas: ${json.message}</p>`;
        }
    } catch (e) {
        console.error('Erro:', e);
        grid.innerHTML = '<p style="text-align:center; color:#f87171;">Erro de conexão. Tente novamente.</p>';
    }
});

function formatarData(dataStr) {
    if (!dataStr) return '';
    const [ano, mes, dia] = dataStr.split('T')[0].split('-');
    return `${dia}/${mes}`;
}