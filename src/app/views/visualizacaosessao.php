<?php

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Importa o controller de autenticação
require_once __DIR__ . '/../controllers/AuthController.php';

// Protege a página
AuthController::verificar();

// Dados do usuário logado
$usuario = AuthController::usuarioLogado();

// Nome do usuário
$nomeUsuario = $usuario['nome'] ?? 'Usuário';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Minhas Sessões - MetaMorfose</title>

  <!-- Fontes -->
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/400.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/500.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/600.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/700.css" rel="stylesheet">

  <!-- Ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <!-- CSS -->
  <link rel="stylesheet" href="../../assets/css/visualizacaosessao.css" />
</head>
<body>

  <!-- OVERLAY -->
  <div class="overlay" id="overlay"></div>

  <!-- TOPBAR MOBILE -->
  <div class="topbar-mobile">

    <img src="../../assets/images/logo.1.png" alt="MetaMorfose" class="logo-mobile" />

    <button class="btn-hamburguer" id="btnHamburguer">
      <i class="fa-solid fa-bars"></i>
    </button>

  </div>

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">

    <div class="sidebar-logo">

      <img src="../../assets/images/logo.1.png" alt="MetaMorfose" class="logo-img" />

      <button class="btn-fechar-menu" id="btnFecharSidebar">
        <i class="fa-solid fa-xmark"></i>
      </button>

    </div>

    <!-- Criar Meta -->
    <div class="sidebar-criar">

      <span>
        Criar<br>nova meta
      </span>

      <button class="btn-plus" onclick="window.location.href='criarmeta.php'">
        <i class="fa-solid fa-plus"></i>
      </button>

    </div>

    <!-- Navegação -->
    <nav class="sidebar-nav">

      <a href="dashboard.php" class="nav-item">
        <i class="fa-solid fa-table-cells-large"></i>
        <span>Dashboard</span>
      </a>

      <a href="visualizacaometas.php" class="nav-item">
        <i class="fa-regular fa-circle-dot"></i>
        <span>Metas</span>
      </a>

      <a href="visualizacaosessao.php" class="nav-item ativo">
        <i class="fa-regular fa-clock"></i>
        <span>Sessões</span>
      </a>

    </nav>

    <!-- Logout -->
    <a href="../controllers/logout.php" class="sidebar-sair">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Sair</span>
    </a>

  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <!-- BANNER -->
    <div class="banner">

      <div class="banner-text">
        <h1>Minhas sessões de estudos</h1>
        <p>Sua vitrine de aprendizado</p>
      </div>

      <div class="banner-illustration">
        <img src="../../assets/images/imagemvisualsessao.png" alt="Ilustração" />
      </div>

    </div>

    <!-- GRID -->
    <div class="sessoes-grid">

      <!-- CARD 1 -->
      <div class="sessao-card" onclick="window.location.href='detalhemeta.php'" style="cursor:pointer;">

        <div class="sessao-header">

          <span class="sessao-titulo">
            Estudar Java
          </span>

          <div class="sessao-meta">
            <span class="sessao-data">30/04</span>
            <i class="fa-regular fa-clock sessao-icone"></i>
          </div>

        </div>

        <div class="sessao-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida roxo-principal" style="width: 89%"></div>
          </div>

          <div class="progresso-info">
            <span class="progresso-label">1h de 20h</span>
            <span class="progresso-valor">89%</span>
          </div>

        </div>

        <div class="sessao-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida roxo-escuro" style="width: 70%"></div>
          </div>

          <div class="progresso-info">
            <span class="progresso-label">nível de foco</span>
            <span class="progresso-valor">7 de 10</span>
          </div>

        </div>

        <div class="sessao-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida roxo-claro" style="width: 60%"></div>
          </div>

          <div class="progresso-info">
            <span class="progresso-label">percepção de progresso</span>
            <span class="progresso-valor">6 de 10</span>
          </div>

        </div>

      </div>

      <!-- CARD 2 -->
      <div class="sessao-card" onclick="window.location.href='detalhemeta.php'" style="cursor:pointer;">

        <div class="sessao-header">

          <span class="sessao-titulo">
            Banco de Dados
          </span>

          <div class="sessao-meta">
            <span class="sessao-data">28/04</span>
            <i class="fa-regular fa-clock sessao-icone"></i>
          </div>

        </div>

        <div class="sessao-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida roxo-principal" style="width: 75%"></div>
          </div>

          <div class="progresso-info">
            <span class="progresso-label">3h de 10h</span>
            <span class="progresso-valor">75%</span>
          </div>

        </div>

        <div class="sessao-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida roxo-escuro" style="width: 90%"></div>
          </div>

          <div class="progresso-info">
            <span class="progresso-label">nível de foco</span>
            <span class="progresso-valor">9 de 10</span>
          </div>

        </div>

        <div class="sessao-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida roxo-claro" style="width: 80%"></div>
          </div>

          <div class="progresso-info">
            <span class="progresso-label">percepção de progresso</span>
            <span class="progresso-valor">8 de 10</span>
          </div>

        </div>

      </div>

    </div>

  </main>

  <!-- JS -->
  <script src="../../assets/js/menu.js" defer></script>
  <script src="../../assets/js/visualizacaosessao.js"></script>

</body>
</html>
