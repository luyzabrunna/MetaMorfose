<?php

// Classe responsável pela conexão com o banco de dados
class Database {
    private $host = 'db'; // Nome do serviço do MySQL no Docker
    private $db   = 'metamorfose'; // Nome do banco de dados
    private $user = 'metamorfose_user';  // Usuário do banco criado no docker-compose
    private $pass = 'metamorfose_pass';  // Senha do usuário do banco
    private $conn; // Variável que armazenará a conexão

    // Método responsável por criar e retornar a conexão
    public function getConnection() {
        $this->conn = null; // Inicializa a conexão como nula
        try {
            // Cria conexão usando PDO com MySQL
            $this->conn = new PDO(
                // DNS = informações de conexão: tipo, host, nome do banco e charset
                "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4", 
                // Usuário do banco
                $this->user, 
                // Senha do banco
                $this->pass
            );

            // Faz o PDO mostrar erros de forma mais detalhada (útil para desenvolvimento)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Caso a conexão falhe
            // Se ocorrer um erro, exibe a mensagem e termina o script
            die("Erro de conexão: " . $e->getMessage());
        }
        // Retorna a conexão estabelecida
        return $this->conn;
    }
        }