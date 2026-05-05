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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaMorfose - Nova Sessão de Estudo</title>
    
    <!-- Fontes e Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/400.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/500.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/600.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.1.0/700.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/sessaoestudo.css">
</head>
<body>

    <!-- OVERLAY -->
    <div class="overlay" id="overlay" onclick="fecharMenu()"></div>

    <!-- TOPBAR MOBILE -->
    <div class="topbar-mobile">

        <img src="../../assets/images/logo.1.png" alt="MetaMorfose" class="logo-mobile" />

        <button class="btn-hamburguer" id="btnHamburguer" onclick="abrirMenu()">
            <i class="fa-solid fa-bars"></i>
        </button>

    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-logo">

            <img src="../../assets/images/logo.1.png" alt="MetaMorfose" class="logo-img" />

            <button class="btn-fechar-menu" onclick="fecharMenu()">
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

        <!-- Menu -->
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
                <h1>Nova sessão de estudo</h1>
                <p>Continue evoluindo</p>
            </div>

            <div class="banner-illustration">
                <img src="../../assets/images/imagemsessao.png" alt="Ilustração">
            </div>

        </div>

        <!-- FORM CARD -->
        <div class="form-card">

            <h2 class="form-title">
                Registrar Sessão de Estudo
            </h2>

            <div class="form-grid">

                <!-- Meta -->
                <div class="form-group">

                    <label class="form-label" for="metaInput">
                        Meta associada:
                    </label>

                    <div class="combobox-wrapper" id="metaCombobox">

                        <input
                            type="text"
                            class="combobox-input"
                            id="metaInput"
                            placeholder="Digite ou selecione..."
                            autocomplete="off"
                        >

                        <button
                            class="combobox-clear"
                            id="metaClear"
                            type="button"
                            title="Limpar"
                        >
                            &times;
                        </button>

                        <i class="fa-solid fa-chevron-down combobox-chevron"></i>

                        <div class="combobox-dropdown" id="metaDropdown"></div>

                    </div>

                </div>

                <!-- Observação -->
                <div class="form-group">

                    <label class="form-label" for="obsInput">
                        Observação
                    </label>

                    <textarea
                        class="form-input"
                        id="obsInput"
                        placeholder="Escreva suas observações..."
                    ></textarea>

                </div>

                <!-- Data -->
                <div class="form-group">

                    <label class="form-label" for="dateInput">
                        Data
                    </label>

                    <input
                        type="date"
                        class="form-input"
                        id="dateInput"
                    >

                </div>

                <!-- Auto-avaliação -->
                <div class="form-group">

                    <label class="form-label">
                        Auto-avaliação
                        <span class="range-info">(de 1 a 10)</span>
                    </label>

                    <div class="slider-container">

                        <!-- Foco -->
                        <div class="slider-group">

                            <span class="slider-label">
                                Nível de foco
                            </span>

                            <div class="slider-wrapper" data-slider="focus">

                                <div class="slider-track">
                                    <div class="slider-fill focus" style="width: 65%;"></div>
                                </div>

                                <div class="slider-thumb" style="left: 65%;" data-value="7"></div>

                                <span class="slider-value">7</span>

                            </div>

                        </div>

                        <!-- Progresso -->
                        <div class="slider-group">

                            <span class="slider-label">
                                Percepção de progresso
                            </span>

                            <div class="slider-wrapper" data-slider="progress">

                                <div class="slider-track">
                                    <div class="slider-fill progress" style="width: 60%;"></div>
                                </div>

                                <div class="slider-thumb" style="left: 60%;" data-value="6"></div>

                                <span class="slider-value">6</span>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Tempo -->
                <div class="form-group full-width">

                    <label class="form-label">
                        Tempo estudado
                    </label>

                    <div style="display:flex; gap:10px; align-items:flex-start;">

                        <!-- Horas -->
                        <div style="flex:1;">

                            <label class="form-label" style="font-size:0.85rem; color:#888; margin-bottom:5px; display:block;">
                                Horas
                            </label>

                            <input
                                type="number"
                                class="form-input"
                                id="hourInput"
                                min="0"
                                placeholder="0"
                            >

                        </div>

                        <!-- Minutos -->
                        <div style="flex:1;">

                            <label class="form-label" style="font-size:0.85rem; color:#888; margin-bottom:5px; display:block;">
                                Minutos
                            </label>

                            <input
                                type="number"
                                class="form-input"
                                id="minuteInput"
                                min="0"
                                max="59"
                                placeholder="0"
                            >

                        </div>

                    </div>

                </div>

                <!-- Botões -->
                <div class="form-actions full-width">

                    <button type="button" class="btn-criar" id="registerBtn">
                        REGISTRAR
                    </button>

                    <button type="button" class="btn-cancelar" id="cancelBtn">
                        CANCELAR
                    </button>

                </div>

            </div>

        </div>

    </main>

    <!-- MODAL -->
    <div class="modal-overlay" id="successModal">

        <div class="modal">

            <div class="modal-icon">
                <i class="fa-solid fa-check" style="color:#4CAF50; font-size:24px;"></i>
            </div>

            <h3>
                Sessão registrada!
            </h3>

            <p>
                Sua sessão de estudo foi salva com sucesso. Continue assim!
            </p>

            <button class="btn-criar" id="modalOkBtn" style="padding:10px 36px; min-width:120px;">
                OK
            </button>

        </div>

    </div>

    <!-- TOAST -->
    <div class="toast" id="toast"></div>

    <!-- JS -->
    <script src="../../assets/js/menu.js"></script>
    <script src="../../assets/js/sessao.js"></script>

</body>
</html>