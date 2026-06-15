document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const metaId = urlParams.get('id');

    const form = document.querySelector('form');
    const btnSubmit = document.getElementById('btnSubmit');
    const pageTitle = document.getElementById('page-title');
    const pageSubtitle = document.getElementById('page-subtitle');
    const formTitle = document.getElementById('form-title');

    // ── MODO EDIÇÃO ──
    if (metaId) {
        if (pageTitle) pageTitle.textContent = 'Editar meta';
        if (pageSubtitle) pageSubtitle.textContent = 'Ajuste os detalhes da sua meta';
        if (formTitle) formTitle.textContent = 'Editar meta';
        if (btnSubmit) btnSubmit.textContent = 'SALVAR ALTERAÇÕES';
        document.title = 'Editar Meta - MetaMorfose';

        try {
            // ✅ CAMINHO CORRIGIDO
            const res = await fetch(`../../app/controllers/MetaController.php?acao=detalhe_meta&id=${metaId}`, {
                credentials: 'include'
            });
            const json = await res.json();

            if (json.status === 'success') {
                const meta = json.meta;

                const metaIdInput = document.getElementById('metaId');
                if (metaIdInput) metaIdInput.value = metaId;

                const tituloInput = document.getElementById('titulo');
                if (tituloInput) tituloInput.value = meta.titulo;

                const descInput = document.getElementById('descricao');
                if (descInput) descInput.value = meta.descricao || '';

                const prazoInput = document.getElementById('prazo');
                if (prazoInput) prazoInput.value = meta.prazo;

                const horasInput = document.getElementById('horas');
                if (horasInput && meta.horas_planejadas) {
                    const totalHoras = parseFloat(meta.horas_planejadas);
                    const h = Math.floor(totalHoras);
                    const m = Math.round((totalHoras - h) * 60);
                    horasInput.value = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
                }

            } else {
                alert('Erro ao carregar meta: ' + json.message);
                window.location.href = 'visualizacaometas.php';
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro de conexão ao carregar dados da meta.');
        }
    }

    // ── SUBMIT ──
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const id = document.getElementById('metaId')?.value;

        const tempoValue = formData.get('horas');
        let horasDecimal = 0;
        if (tempoValue && tempoValue.includes(':')) {
            const [h, m] = tempoValue.split(':');
            horasDecimal = parseFloat(h) + (parseFloat(m) / 60);
        } else {
            horasDecimal = parseFloat(tempoValue) || 0;
        }

        const payload = {
            acao: id ? 'atualizar_meta' : 'criar_meta',
            id: id || null,
            titulo: formData.get('titulo'),
            descricao: formData.get('descricao'),
            horas_planejadas: horasDecimal,
            prazo: formData.get('prazo')
        };

        if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.textContent = 'Salvando...'; }

        try {
            // ✅ CAMINHO CORRIGIDO
            const response = await fetch('../../app/controllers/MetaController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
                credentials: 'include'
            });

            const json = await response.json();

            if (json.status === 'success') {
                alert(id ? 'Meta atualizada com sucesso!' : 'Meta criada com sucesso!');
                window.location.href = 'visualizacaometas.php';
            } else {
                alert('Erro: ' + json.message);
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro de conexão ao salvar meta.');
        } finally {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.textContent = id ? 'SALVAR ALTERAÇÕES' : 'CRIAR META';
            }
        }
    });
});