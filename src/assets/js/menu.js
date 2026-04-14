/**
 * menu.js - Controle do menu sidebar responsivo
 * Projeto: MetaMorfose
 */

(function() {
  'use strict';

  // Cache dos elementos
  const elements = {
    sidebar: null,
    overlay: null,
    btnHamburguer: null,
    btnFechar: null,
    navItems: null,
    btnCriar: null
  };

  // Estado do menu
  let menuAberto = false;

  /**
   * Inicializa o menu após o DOM estar pronto
   */
  function init() {
    // Seleciona elementos
    elements.sidebar = document.getElementById('sidebar');
    elements.overlay = document.getElementById('overlay');
    elements.btnHamburguer = document.getElementById('btnHamburguer');
    elements.btnFechar = elements.sidebar?.querySelector('.btn-fechar-menu');
    elements.navItems = elements.sidebar?.querySelectorAll('.nav-item');
    elements.btnCriar = elements.sidebar?.querySelector('.btn-plus');

    // Valida se elementos essenciais existem
    if (!elements.sidebar || !elements.overlay) {
      console.warn('menu.js: Elementos #sidebar ou #overlay não encontrados');
      return;
    }

    // Registra eventos
    registerEvents();
  }

  /**
   * Registra todos os event listeners
   */
  function registerEvents() {
    // Botão hamburger (mobile)
    if (elements.btnHamburguer) {
      elements.btnHamburguer.addEventListener('click', abrirMenu);
    }

    // Botão fechar dentro do sidebar
    if (elements.btnFechar) {
      elements.btnFechar.addEventListener('click', fecharMenu);
    }

    // Overlay (clicar fora fecha)
    elements.overlay.addEventListener('click', fecharMenu);

    // Fechar ao clicar em um item de navegação
    if (elements.navItems?.length) {
      elements.navItems.forEach(item => {
        item.addEventListener('click', () => {
          // Fecha apenas se for mobile (sidebar com classe 'aberto')
          if (menuAberto) fecharMenu();
        });
      });
    }

    // Botão "Criar nova meta" (+)
    if (elements.btnCriar) {
      elements.btnCriar.addEventListener('click', (e) => {
        e.preventDefault();
        if (menuAberto) fecharMenu();
        // Aqui você pode redirecionar ou abrir modal de criação
        // Ex: window.location.href = 'criar-meta.html';
        console.log('Abrir formulário de nova meta');
      });
    }

    // Tecla ESC para fechar
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menuAberto) {
        fecharMenu();
      }
    });

    // Previne que o sidebar feche ao clicar dentro dele
    elements.sidebar.addEventListener('click', (e) => {
      e.stopPropagation();
    });
  }

  /**
   * Abre o menu sidebar
   */
  function abrirMenu() {
    if (menuAberto) return;
    
    elements.sidebar.classList.add('aberto');
    elements.overlay.classList.add('ativo');
    document.body.style.overflow = 'hidden'; // Trava scroll do body
    menuAberto = true;
    
    // Dispara evento customizado (útil para integrações)
    document.dispatchEvent(new CustomEvent('menuAberto'));
  }

  /**
   * Fecha o menu sidebar
   */
  function fecharMenu() {
    if (!menuAberto) return;
    
    elements.sidebar.classList.remove('aberto');
    elements.overlay.classList.remove('ativo');
    document.body.style.overflow = ''; // Restaura scroll
    menuAberto = false;
    
    // Dispara evento customizado
    document.dispatchEvent(new CustomEvent('menuFechado'));
  }

  /**
   * Toggle (abre/fecha) - útil se quiser reutilizar
   */
  function toggleMenu() {
    menuAberto ? fecharMenu() : abrirMenu();
  }

  // Expõe funções globalmente (caso precise chamar inline no HTML)
  window.abrirMenu = abrirMenu;
  window.fecharMenu = fecharMenu;
  window.toggleMenu = toggleMenu;

  // Inicializa quando o DOM estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();