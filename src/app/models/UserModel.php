<?php

// Importa a conexão com o banco
require_once __DIR__ . '/../config/database.php';

// Model responsável pelos dados do usuário
class UserModel {

    // Conexão com o banco
    private $conn;

    // Nome da tabela
    private $table = 'usuario';

    // Construtor — inicia a conexão
    public function __construct() {

        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // BUSCA USUÁRIO POR E-MAIL

    public function findByEmail($email) {

        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':email', $email);

        $stmt->execute();

        // Retorna o usuário encontrado ou false se não existir
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // BUSCA USUÁRIO POR ID

    public function findById($id) {

        $sql = "SELECT id, nome, email, criado_em FROM {$this->table} WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        // Retorna o usuário encontrado ou false se não existir
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CRIA NOVO USUÁRIO

    public function create($nome, $email, $senha) {

        $sql = "INSERT INTO {$this->table} (nome, email, senha)
                VALUES (:nome, :email, :senha)";

        $stmt = $this->conn->prepare($sql);

        // Criptografa a senha antes de salvar
        $hash = password_hash($senha, PASSWORD_BCRYPT);

        $stmt->bindParam(':nome',  $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $hash);

        // Retorna true se inseriu ou false se falhou
        return $stmt->execute();
    }

    // ATUALIZA DADOS DO USUÁRIO

    public function update($id, $nome, $email) {

        $sql = "UPDATE {$this->table}
                SET nome = :nome, email = :email
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nome',  $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id',    $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ATUALIZA SENHA DO USUÁRIO

    public function updateSenha($id, $novaSenha) {

        $sql = "UPDATE {$this->table}
                SET senha = :senha
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        // Criptografa a nova senha
        $hash = password_hash($novaSenha, PASSWORD_BCRYPT);

        $stmt->bindParam(':senha', $hash);
        $stmt->bindParam(':id',    $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // EXCLUI USUÁRIO

    public function delete($id) {

        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}