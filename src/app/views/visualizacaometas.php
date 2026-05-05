<?php

require_once __DIR__ . '/../controllers/AuthController.php';

// Impede cache em páginas protegidas
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verifica se usuário está logado
AuthController::verificar();

// Recupera dados do usuário logado
$usuario = AuthController::usuarioLogado();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Minhas Metas - MetaMorfose</title>

  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/400.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/500.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/600.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/700.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <link rel="stylesheet" href="../../assets/css/visualizacaometas.css" />

</head>

<body>

  <!-- OVERLAY -->
  <div class="overlay" id="overlay"></div>

  <!-- TOPBAR MOBILE -->
  <div class="topbar-mobile">

    <img
      src="../../assets/images/logo.1.png"
      alt="MetaMorfose"
      class="logo-mobile"
    />

    <button class="btn-hamburguer" id="btnHamburguer">
      <i class="fa-solid fa-bars"></i>
    </button>

  </div>

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">

    <div class="sidebar-logo">

      <img
        src="../../assets/images/logo.1.png"
        alt="MetaMorfose"
        class="logo-img"
      />

      <button class="btn-fechar-menu" id="btnFecharSidebar">
        <i class="fa-solid fa-xmark"></i>
      </button>

    </div>

    <div class="sidebar-criar">

      <span>Criar<br>nova meta</span>

      <button
        class="btn-plus"
        onclick="window.location.href='criarmeta.php'">

        <i class="fa-solid fa-plus"></i>

      </button>

    </div>

    <nav class="sidebar-nav">

      <a href="dashboard.php" class="nav-item">
        <i class="fa-solid fa-table-cells-large"></i>
        <span>Dashboard</span>
      </a>

      <a href="visualizacaometas.php" class="nav-item ativo">
        <i class="fa-regular fa-circle-dot"></i>
        <span>Metas</span>
      </a>

      <a href="visualizacaosessao.php" class="nav-item">
        <i class="fa-regular fa-clock"></i>
        <span>Sessões</span>
      </a>

    </nav>

    <!-- LOGOUT -->
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
        <h1>Minhas metas</h1>
        <p>Acompanhe seu progresso</p>
      </div>

      <div class="banner-illustration">
        <img
          src="../../assets/images/imagemvisualizacaometas.png"
          alt="Ilustração"
        />
      </div>

    </div>

    <!-- GRID DE METAS -->
    <div class="metas-grid">

      <!-- META -->
      <div
        class="meta-card"
        onclick="window.location.href='detalhemeta.php'"
        style="cursor:pointer;">

        <div class="meta-header">

          <span class="meta-titulo">Estudar Java</span>

          <span class="meta-prazo">
            até 30/04
            <i class="fa-regular fa-clock"></i>
          </span>

        </div>

        <div class="meta-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida" style="width: 60%"></div>
          </div>

          <span class="meta-porcentagem">60%</span>

        </div>

        <p class="meta-horas">12h de 20h</p>

        <span class="meta-status em-andamento">
          Em andamento
        </span>

      </div>

      <!-- META -->
      <div
        class="meta-card"
        onclick="window.location.href='detalhemeta.php'"
        style="cursor:pointer;">

        <div class="meta-header">

          <span class="meta-titulo">Banco de Dados</span>

          <span class="meta-prazo">
            até 12/05
            <i class="fa-regular fa-clock"></i>
          </span>

        </div>

        <div class="meta-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida" style="width: 45%"></div>
          </div>

          <span class="meta-porcentagem">45%</span>

        </div>

        <p class="meta-horas">9h de 20h</p>

        <span class="meta-status em-andamento">
          Em andamento
        </span>

      </div>

       <!-- META -->
      <div
        class="meta-card"
        onclick="window.location.href='detalhemeta.php'"
        style="cursor:pointer;">

        <div class="meta-header">

          <span class="meta-titulo">Engenharia de Software</span>

          <span class="meta-prazo">
            até 12/05
            <i class="fa-regular fa-clock"></i>
          </span>

        </div>

        <div class="meta-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida" style="width: 45%"></div>
          </div>

          <span class="meta-porcentagem">45%</span>

        </div>

        <p class="meta-horas">18h de 20h</p>

        <span class="meta-status em-andamento">
          Em andamento
        </span>

      </div>

      <!-- META -->
      <div
        class="meta-card"
        onclick="window.location.href='detalhemeta.php'"
        style="cursor:pointer;">

        <div class="meta-header">

          <span class="meta-titulo">Estudar Java</span>

          <span class="meta-prazo">
            até 30/04
            <i class="fa-regular fa-clock"></i>
          </span>

        </div>

        <div class="meta-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida" style="width: 60%"></div>
          </div>

          <span class="meta-porcentagem">60%</span>

        </div>

        <p class="meta-horas">12h de 20h</p>

        <span class="meta-status em-andamento">
          Em andamento
        </span>

      </div>

      <!-- META -->
      <div
        class="meta-card"
        onclick="window.location.href='detalhemeta.php'"
        style="cursor:pointer;">

        <div class="meta-header">

          <span class="meta-titulo">Banco de Dados</span>

          <span class="meta-prazo">
            até 12/05
            <i class="fa-regular fa-clock"></i>
          </span>

        </div>

        <div class="meta-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida" style="width: 45%"></div>
          </div>

          <span class="meta-porcentagem">45%</span>

        </div>

        <p class="meta-horas">9h de 20h</p>

        <span class="meta-status em-andamento">
          Em andamento
        </span>

      </div>

      <!-- META -->
      <div
        class="meta-card"
        onclick="window.location.href='detalhemeta.php'"
        style="cursor:pointer;">

        <div class="meta-header">

          <span class="meta-titulo">Engenharia de Software</span>

          <span class="meta-prazo">
            até 12/05
            <i class="fa-regular fa-clock"></i>
          </span>

        </div>

        <div class="meta-progresso">

          <div class="barra-fundo">
            <div class="barra-preenchida" style="width: 45%"></div>
          </div>

          <span class="meta-porcentagem">45%</span>

        </div>

        <p class="meta-horas">18h de 20h</p>

        <span class="meta-status em-andamento">
          Em andamento
        </span>

      </div>


    </div>

  </main>

  <script src="../../assets/js/menu.js" defer></script>

</body>
</html>