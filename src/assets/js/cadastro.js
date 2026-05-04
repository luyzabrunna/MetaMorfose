/**
 * cadastro.js
 * Funções de interação da página de cadastro
 */

/**
 * Alterna visibilidade do campo de senha
 * @param {string} inputId - ID do input de senha
 * @param {HTMLElement} btn - Botão que foi clicado
 */
function toggleSenha(inputId, btn) {
    const input   = document.getElementById(inputId);
    const aberto  = btn.querySelector('.icone-olho');
    const fechado = btn.querySelector('.icone-olho-fechado');

    if (input.type === 'password') {
        input.type = 'text';
        aberto.classList.add('oculto');
        fechado.classList.remove('oculto');
    } else {
        input.type = 'password';
        aberto.classList.remove('oculto');
        fechado.classList.add('oculto');
    }
}