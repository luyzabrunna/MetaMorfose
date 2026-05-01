CREATE DATABASE IF NOT EXISTS metamorfose;
USE metamorfose;

CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE meta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    horas_planejadas DECIMAL(6,2) NOT NULL,
    horas_estudadas DECIMAL(6,2) DEFAULT 0,
    prazo DATE NOT NULL,
    status ENUM('nao_iniciada', 'em_andamento', 'concluida') DEFAULT 'nao_iniciada',
    usuario_id INT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE sessao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    data DATE NOT NULL,
    tempo_estudado DECIMAL(6,2) NOT NULL,
    observacao TEXT,
    meta_id INT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meta_id) REFERENCES meta(id) ON DELETE CASCADE
);