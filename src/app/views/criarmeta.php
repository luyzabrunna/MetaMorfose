<?php
require_once __DIR__ . '/../controllers/AuthController.php';

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

AuthController::verificar();
$usuario = AuthController::usuarioLogado();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nova Meta - MetaMorfose</title>

  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/400.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/500.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/600.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/700.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/css/criarmeta.css" />
</head>

<body>

  <div class="overlay" id="overlay"></div>

  <div class="topbar-mobile">
    <img src="../../assets/images/logo.1.png" alt="MetaMorfose" class="logo-mobile" />
    <button class="btn-hamburguer" id="btnHamburguer">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <img src="../../assets/images/logo.1.png" alt="MetaMorfose" class="logo-img" />
      <button class="btn-fechar-menu" id="btnFecharSidebar">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="sidebar-criar">
      <span>Criar<br>nova meta</span>
      <button class="btn-plus" onclick="window.location.href='criarmeta.php'">
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

    <a href="../controllers/logout.php" class="sidebar-sair">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Sair</span>
    </a>
  </aside>

  <main class="main-content">
    <div class="banner">
      <div class="banner-text">
        <h1 id="page-title">Nova meta</h1>
        <p id="page-subtitle">Dê o primeiro passo</p>
      </div>
      <div class="banner-illustration">
        <img src="../../assets/images/imagemmeta.png" alt="Ilustração" />
      </div>
    </div>

    <div class="form-card">
      <h2 class="form-title" id="form-title">Criar nova meta</h2>

      <form id="formMeta">
        <!-- Input hidden para ID (edição) -->
        <input type="hidden" id="metaId" name="metaId">

        <div class="form-group">
          <label for="titulo">Título</label>
          <input type="text" id="titulo" name="titulo" required />
        </div>

        <div class="form-group">
          <label for="descricao">Descrição</label>
          <textarea id="descricao" name="descricao" required></textarea>
        </div>

        <div class="form-group">
          <label for="horas">Horas totais</label>
          <input type="time" id="horas" name="horas" required />
        </div>

        <div class="form-group">
          <label for="prazo">Prazo</label>
          <input type="date" id="prazo" name="prazo" required />
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-criar" id="btnSubmit">CRIAR META</button>
          <button type="button" class="btn-cancelar" onclick="window.location.href='visualizacaometas.php'">CANCELAR</button>
        </div>
      </form>
    </div>
  </main>

  <!-- SCRIPTS -->
  <script src="../../assets/js/menu.js" defer></script>
  <script src="../../assets/js/criarmeta.js" ></script>

</body>
</html>