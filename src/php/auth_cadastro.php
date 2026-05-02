<?php
session_start();

// Configurações do banco
$host = 'db';
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

// Recebe os dados
$nome  = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

// Validações
if (empty($nome) || empty($email) || empty($senha)) {
    echo "<script>alert('Preencha todos os campos!'); window.history.back();</script>";
    exit;
}

if ($senha !== $confirmar) {
    echo "<script>alert('As senhas não conferem!'); window.history.back();</script>";
    exit;
}

// Criptografa a senha (PARTE MAIS IMPORTANTE!)
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere no banco
try {
    $sql = "INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome'  => $nome,
        ':email' => $email,
        ':senha' => $senhaHash
    ]);

    // Sucesso: redireciona para o login
    header("Location: login.php?sucesso=1");
    exit;

} catch (\PDOException $e) {
    // E-mail duplicado (erro 1062 do MySQL)
    if ($e->errorInfo[1] == 1062) {
        echo "<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>";
    } else {
        echo "Erro: " . $e->getMessage();
    }
}
?>