<?php
// Inicia a sessão para verificar se o usuário já está logado
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaMoforse - Criar Conta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/cadastro.css">
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
                <img src="../assets/images/cadastro.png" alt="Ilustração cadastro">
            </div>
        </div>
        
        <!-- LADO DIREITO -->
        <div class="right">
            <!-- Logo para mobile/tablet -->
            <div class="mobile-logo">
                <img src="../assets/images/logo.png" alt="MetaMoforse">
             </div>
            <div class="form-box">
                <h2 class="form-title">Criar conta</h2>
                <p class="form-sub">Cadastre-se para começar</p>

                <!-- Formulário apontando para o script de processamento -->
                <form action="auth_cadastro.php" method="POST">
                    
                    <div class="field">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required>
                    </div>

                    <div class="field">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="seu@email.com" required>
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

                <p class="login-link">Já tem uma conta? <a href="login.php">Entrar</a></p>
            </div>
        </div>
    </div>

    <!-- Script do toggle de senha (mantido igual) -->
    <script>
        function toggleSenha(inputId, btn) {
            const input = document.getElementById(inputId);
            const aberto = btn.querySelector('.icone-olho');
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
    </script>
</body>
</html>