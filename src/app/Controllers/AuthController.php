<?php

// Importa o model de usuário
require_once __DIR__ . '/../models/UserModel.php';

// Controlador responsável pela autenticação
class AuthController {

    // Variável que armazenará o model
    private $model;

    // Método executado automaticamente ao criar o controller
    public function __construct() {

        // Instancia o model de usuário
        $this->model = new UserModel();

        // Inicia sessão apenas se ainda não existir
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // CADASTRO

    public function register() {

        // Variável de erro
        $erro = null;

        // Verifica se formulário foi enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Recebe dados do formulário
            $nome = trim($_POST['nome'] ?? '');

            $email = trim($_POST['email'] ?? '');

            $senha = $_POST['senha'] ?? '';

            $confirmar = $_POST['confirmar'] ?? '';

            // VALIDAÇÕES

            // Campos vazios
            if (
                empty($nome) ||
                empty($email) ||
                empty($senha) ||
                empty($confirmar)
            ) {

                $erro = "Preencha todos os campos.";

            // Verifica se e-mail é válido
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $erro = "E-mail inválido.";

            //  Verifica tamanho mínimo da senha
            } elseif (strlen($senha) < 6) {

                $erro = "A senha deve ter pelo menos 6 caracteres.";

            // Senhas diferentes
            } elseif ($senha !== $confirmar) {

                $erro = "As senhas não coincidem.";

            // E-mail já existe
            } elseif ($this->model->findByEmail($email)) {

                $erro = "Este e-mail já está cadastrado.";

            } else {

                // CADASTRO DO USUÁRIO

                // Cria usuário no banco
                if ($this->model->create($nome, $email, $senha)) {

                    // Busca usuário recém criado
                    $usuario = $this->model->findByEmail($email);

                    // Cria sessão
                    $_SESSION['usuario_id'] = $usuario['id'];

                    $_SESSION['usuario_nome'] = $usuario['nome'];

                    // Redireciona para dashboard
                    header("Location: ../views/dashboard.php");

                    exit;

                } else {
                    
                    // Caso ocorra erro no cadastro
                    $erro = "Erro ao criar conta. Tente novamente.";
                }
            }
        }

        // Carrega view de cadastro
        require_once __DIR__ . '/../views/cadastro.php';
    }

    // LOGIN

    public function login() {

        // Variável de erro
        $erro = null;

        // Se já estiver logado
        if (isset($_SESSION['usuario_id'])) {

            header("Location: ../views/dashboard.php");

            exit;
        }

        // Verifica envio do formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Recebe dados
            $email = trim($_POST['email'] ?? '');

            $senha = $_POST['senha'] ?? '';

            // Campos vazios
            if (empty($email) || empty($senha)) {

                $erro = "Preencha todos os campos.";

            } else {

                // Busca usuário
                $usuario = $this->model->findByEmail($email);

                // Verifica senha
                if (
                    $usuario &&
                    password_verify($senha, $usuario['senha'])
                ) {

                    // Cria sessão
                    $_SESSION['usuario_id'] = $usuario['id'];

                    $_SESSION['usuario_nome'] = $usuario['nome'];

                    // Redireciona
                    header("Location: ../views/dashboard.php");

                    exit;

                } else {

                    $erro = "E-mail ou senha incorretos.";
                }
            }
        }

        // Carrega view login
        require_once __DIR__ . '/../views/login.php';
    }

    // LOGOUT
    
    public function logout() {

        // Limpa sessão
        $_SESSION = [];

        // Destroi sessão
        session_destroy();

        // Redireciona para login
        header("Location: ../views/login.php");

        exit;
    }

    // VERIFICA LOGIN

    public static function verificar() {

        // Inicia sessão se necessário
        if (session_status() === PHP_SESSION_NONE) {

            session_start();
        }

        // Verifica usuário logado
        if (!isset($_SESSION['usuario_id'])) {

            header("Location: ../views/login.php");

            exit;
        }
    }

    // RETORNA DADOS DO USUÁRIO LOGADO
   
    public static function usuarioLogado() {

        // Inicia sessão se necessário
        if (session_status() === PHP_SESSION_NONE) {

            session_start();
        }

        // Retorna dados
        return [

            'id' => $_SESSION['usuario_id'] ?? null,

            'nome' => $_SESSION['usuario_nome'] ?? null
        ];
    }
}

// EXECUÇÃO DAS AÇÕES

// Cria controller
$auth = new AuthController();

// Pega ação da URL
$acao = $_GET['acao'] ?? '';

// Executa login
if ($acao === 'login') {

    $auth->login();
}

// Executa cadastro
if ($acao === 'register') {

    $auth->register();
}

// Executa logout
if ($acao === 'logout') {

    $auth->logout();
}