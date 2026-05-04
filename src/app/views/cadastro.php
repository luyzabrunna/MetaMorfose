<?php

$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Evita cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();

// Processa formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erro = $auth->register();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaMorfose - Criar Conta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/cadastro.css">
</head>
<body>
    <div class="container">

        <!-- LADO ESQUERDO -->
        <div class="left">
            <div class="left-text">
                <h1 class="left-title">Vamos começar</h1>
                <p class="left-sub">Organize seus estudos com facilidade</p>
            </div>
            <div class="left-illustration">
                <img src="../../assets/images/cadastro.png" alt="Ilustração cadastro">
            </div>
        </div>

        <!-- LADO DIREITO -->
        <div class="right">

            <!-- Logo para mobile/tablet -->
            <div class="mobile-logo">
                <img src="../../assets/images/logo.png" alt="MetaMorfose">
            </div>

            <div class="form-box">
                <h2 class="form-title">Criar conta</h2>
                <p class="form-sub">Cadastre-se para começar</p>

                <!-- Exibe mensagem de erro se houver -->
                <?php if (!empty($erro)): ?>
                    <div class="erro-msg"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <form method="POST">

                    <div class="field">
                        <label for="nome">Nome</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            placeholder="Seu nome completo"
                            value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                            required>
                    </div>

                    <div class="field">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="seu@email.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required>
                    </div>

                    <div class="field">
                        <label for="senha">Senha</label>
                        <div class="input-senha">
                            <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                            <button type="button" class="toggle-senha" onclick="toggleSenha('senha', this)">
                                <svg class="icone-olho" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="icone-olho-fechado oculto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="field">
                        <label for="confirmar">Confirmar Senha</label>
                        <div class="input-senha">
                            <input type="password" id="confirmar" name="confirmar" placeholder="••••••••" required>
                            <button type="button" class="toggle-senha" onclick="toggleSenha('confirmar', this)">
                                <svg class="icone-olho" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="icone-olho-fechado oculto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-criar">Criar Conta</button>
                </form>

                <p class="login-link">Já tem uma conta? <a href="../views/login.php">Entrar</a></p>
            </div>
        </div>
    </div>

    <script src="../../assets/js/cadastro.js"></script>
</body>
</html>