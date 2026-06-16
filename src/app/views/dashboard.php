<?php

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Autenticação
require_once __DIR__ . '/../controllers/AuthController.php';
AuthController::verificar();
$usuario = AuthController::usuarioLogado();
$nomeUsuario = $usuario['nome'] ?? 'Usuário';

// Banco + Model
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MetaModel.php';

$database = new Database();
$pdo      = $database->getConnection();
$model    = new MetaModel($pdo);

$usuario_id = $usuario['id'];

// ── DADOS REAIS ──
$metasRecentes    = $model->listarMetasRecentes($usuario_id, 3);
$sessoesRecentes  = $model->listarSessoesRecentes($usuario_id, 3);
$metaPrazo        = $model->metaPrazoMaisProximo($usuario_id);
$totalHoras       = $model->totalHorasEstudadas($usuario_id);

// Calcula progresso da meta com prazo mais próximo
$progresso       = 0;
$horasTexto      = '0h estudadas';
if ($metaPrazo) {
    $progresso  = $metaPrazo['horas_planejadas'] > 0
        ? min(100, round(($metaPrazo['horas_estudadas'] / $metaPrazo['horas_planejadas']) * 100))
        : 0;
    $horasTexto = $metaPrazo['horas_estudadas'] . 'h de ' . $metaPrazo['horas_planejadas'] . 'h estudadas';
}

// Formata status para exibição
function formatarStatus($status) {
    $mapa = [
        'nao_iniciada' => ['label' => 'Não iniciada', 'classe' => 'nao-iniciada'],
        'em_andamento' => ['label' => 'Em andamento', 'classe' => 'em-andamento'],
        'concluida'    => ['label' => 'Concluída',    'classe' => 'concluida'],
    ];
    return $mapa[$status] ?? ['label' => $status, 'classe' => ''];
}

// Formata data de sessão
function formatarData($data) {
    if (!$data) return '';
    $d = new DateTime($data);
    return $d->format('d/m');
}

// Formata horas decimais para exibição (ex: 1.5 → 1h30)
function formatarHoras($decimal) {
    $horas   = floor($decimal);
    $minutos = round(($decimal - $horas) * 60);
    if ($minutos > 0) return "{$horas}h{$minutos}";
    return "{$horas}h";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - MetaMorfose</title>
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/400.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/500.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/600.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/700.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/css/dashboard.css" />
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

    <div class="sidebar-criar">
      <span>Criar<br>nova meta</span>
      <button class="btn-plus" onclick="window.location.href='criarmeta.php'">
        <i class="fa-solid fa-plus"></i>
      </button>
    </div>

    <nav class="sidebar-nav">
      <a href="dashboard.php" class="nav-item ativo">
        <i class="fa-solid fa-table-cells-large"></i>
        <span>Dashboard</span>
      </a>
      <a href="visualizacaometas.php" class="nav-item">
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

  <!-- MAIN -->
  <main class="main-content">

    <!-- BANNER -->
    <div class="banner">
      <div class="banner-text">
        <h1>Olá, <?php echo htmlspecialchars($nomeUsuario); ?> 👋</h1>
        <p>Pronta(o) para continuar seus estudos hoje?</p>
      </div>
      <div class="banner-illustration">
        <img src="../../assets/images/imagemdashboard.png" alt="Ilustração" />
      </div>
    </div>

    <!-- GRID -->
    <div class="dashboard-grid">

      <!-- COLUNA ESQUERDA -->
      <div class="coluna-principal">

        <!-- METAS RECENTES -->
        <section class="secao">
          <h2 class="secao-titulo">Metas recentes</h2>
          <div class="cards-grid">

            <?php if (empty($metasRecentes)): ?>
              <p class="sem-dados">Nenhuma meta cadastrada ainda.</p>

            <?php else: ?>
              <?php foreach ($metasRecentes as $meta):
                $s = formatarStatus($meta['status']);
                $pct = $meta['horas_planejadas'] > 0
                  ? min(100, round(($meta['horas_estudadas'] / $meta['horas_planejadas']) * 100))
                  : 0;
              ?>
              <div class="meta-card" onclick="window.location.href='detalhemeta.php?id=<?php echo $meta['id']; ?>'" style="cursor:pointer;">
                <div class="meta-card-header">
                  <span class="meta-card-titulo"><?php echo htmlspecialchars($meta['titulo']); ?></span>
                  <i class="fa-regular fa-circle-dot icone-meta"></i>
                </div>
                <p class="meta-card-desc"><?php echo htmlspecialchars($meta['descricao'] ?? ''); ?></p>
                <p class="meta-card-progresso-texto">Progresso: <?php echo $pct; ?>% concluído</p>
                <div class="barra-fundo">
                  <div class="barra-preenchida" style="width: <?php echo $pct; ?>%"></div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </div>
        </section>

        <!-- SESSÕES RECENTES -->
        <section class="secao">
          <h2 class="secao-titulo">Sessões recentes</h2>
          <div class="cards-grid">

            <?php if (empty($sessoesRecentes)): ?>
              <p class="sem-dados">Nenhuma sessão registrada ainda.</p>

            <?php else: ?>
              <?php foreach ($sessoesRecentes as $sessao): ?>
              <div class="sessao-card" onclick="window.location.href='visualizacaosessao.php'" style="cursor:pointer;">
                <div class="sessao-card-header">
                  <span class="sessao-card-titulo"><?php echo htmlspecialchars($sessao['meta_titulo']); ?></span>
                  <i class="fa-regular fa-clock icone-sessao"></i>
                </div>
                <p class="sessao-card-info">Duração: <?php echo formatarHoras($sessao['tempo_estudado']); ?></p>
                <p class="sessao-card-info">Data: <?php echo formatarData($sessao['data']); ?></p>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </div>
        </section>

      </div>

      <!-- COLUNA DIREITA -->
      <aside class="coluna-lateral">

        <!-- CARD PRAZO MAIS PRÓXIMO -->
        <div class="progresso-card">
          <h3 class="progresso-titulo">Prazo mais próximo</h3>

          <?php if ($metaPrazo): ?>
            <div class="progresso-meta-nome">
              <i class="fa-regular fa-circle-dot icone-meta"></i>
              <span><?php echo htmlspecialchars($metaPrazo['titulo']); ?></span>
            </div>

            <p class="progresso-prazo">
              Prazo: <strong><?php echo date('d/m/Y', strtotime($metaPrazo['prazo'])); ?></strong>
            </p>

            <?php if (!empty($metaPrazo['descricao'])): ?>
              <p class="progresso-desc"><?php echo htmlspecialchars($metaPrazo['descricao']); ?></p>
            <?php endif; ?>

            <div class="progresso-barra-grupo">
              <div class="progresso-topo">
                <span class="campo-label">Progresso</span>
                <span class="porcentagem"><?php echo $progresso; ?>%</span>
              </div>
              <div class="barra-fundo">
                <div class="barra-preenchida" style="width: <?php echo $progresso; ?>%"></div>
              </div>
              <span class="horas-texto"><?php echo $horasTexto; ?></span>
            </div>

            <?php
              $s = formatarStatus($metaPrazo['status']);
            ?>
            <span class="badge <?php echo $s['classe']; ?>"><?php echo $s['label']; ?></span>

          <?php else: ?>
            <p class="sem-dados">Nenhuma meta com prazo próximo.</p>
          <?php endif; ?>
        </div>

        <!-- CARD TOTAL DE HORAS -->
        <div class="horas-card">
          <div class="horas-icone">
            <i class="fa-regular fa-clock"></i>
          </div>
          <p class="horas-label">Total de horas estudadas</p>
          <p class="horas-valor"><?php echo formatarHoras($totalHoras); ?></p>
        </div>

      </aside>

    </div>

  </main>

  <script src="../../assets/js/menu.js" defer></script>

</body>
</html>