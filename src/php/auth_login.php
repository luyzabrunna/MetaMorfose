<?php
session_start();

// Conexão com o banco (ajuste conforme sua config)
$host = 'db'; // ou 'localhost' se estiver rodando fora do Docker
$db   = 'metamorfose';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Recebe os dados do formulário
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Busca o usuário no banco
$stmt = $pdo->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

// Verifica a senha (use password_verify() se as senhas estiverem hash)
if ($usuario && password_verify($senha, $usuario['senha'])) {
    // Login sucesso: cria a sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    
    // Redireciona para o dashboard
    header("Location: dashboard.php");
    exit;
} else {
    // Login falhou: volta para o login com erro
    header("Location: login.php?erro=1");
    exit;
}
?>