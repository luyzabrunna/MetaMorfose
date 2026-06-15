-- ============================================================
-- MetaMorfose - Banco de Dados Completo
-- ============================================================

CREATE DATABASE IF NOT EXISTS metamorfose;
USE metamorfose;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ── TABELA: usuario ──
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── TABELA: meta ──
CREATE TABLE IF NOT EXISTS `meta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descricao` text,
  `horas_planejadas` decimal(6,2) NOT NULL,
  `horas_estudadas` decimal(6,2) DEFAULT '0.00',
  `prazo` date NOT NULL,
  `status` enum('nao_iniciada','em_andamento','concluida') DEFAULT 'nao_iniciada',
  `usuario_id` int NOT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `meta_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── TABELA: sessao ──
CREATE TABLE IF NOT EXISTS `sessao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `tempo_estudado` decimal(6,2) NOT NULL,
  `observacao` text,
  `foco` tinyint DEFAULT NULL,
  `progresso` tinyint DEFAULT NULL,
  `meta_id` int NOT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `meta_id` (`meta_id`),
  CONSTRAINT `sessao_ibfk_1` FOREIGN KEY (`meta_id`) REFERENCES `meta` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── TRIGGERS ──
-- Atualiza horas_estudadas e status da meta automaticamente

DROP TRIGGER IF EXISTS `atualizar_horas_insert`;
DROP TRIGGER IF EXISTS `atualizar_horas_update`;
DROP TRIGGER IF EXISTS `atualizar_horas_delete`;

DELIMITER $$

CREATE TRIGGER `atualizar_horas_insert`
AFTER INSERT ON `sessao`
FOR EACH ROW
BEGIN
    DECLARE total DECIMAL(6,2);
    DECLARE planejadas DECIMAL(6,2);

    SELECT COALESCE(SUM(tempo_estudado), 0) INTO total
    FROM sessao WHERE meta_id = NEW.meta_id;

    SELECT horas_planejadas INTO planejadas
    FROM meta WHERE id = NEW.meta_id;

    UPDATE meta SET
        horas_estudadas = total,
        status = CASE
            WHEN total = 0 THEN 'nao_iniciada'
            WHEN total >= planejadas THEN 'concluida'
            ELSE 'em_andamento'
        END
    WHERE id = NEW.meta_id;
END$$

CREATE TRIGGER `atualizar_horas_update`
AFTER UPDATE ON `sessao`
FOR EACH ROW
BEGIN
    DECLARE total DECIMAL(6,2);
    DECLARE planejadas DECIMAL(6,2);

    SELECT COALESCE(SUM(tempo_estudado), 0) INTO total
    FROM sessao WHERE meta_id = NEW.meta_id;

    SELECT horas_planejadas INTO planejadas
    FROM meta WHERE id = NEW.meta_id;

    UPDATE meta SET
        horas_estudadas = total,
        status = CASE
            WHEN total = 0 THEN 'nao_iniciada'
            WHEN total >= planejadas THEN 'concluida'
            ELSE 'em_andamento'
        END
    WHERE id = NEW.meta_id;
END$$

CREATE TRIGGER `atualizar_horas_delete`
AFTER DELETE ON `sessao`
FOR EACH ROW
BEGIN
    DECLARE total DECIMAL(6,2);
    DECLARE planejadas DECIMAL(6,2);

    SELECT COALESCE(SUM(tempo_estudado), 0) INTO total
    FROM sessao WHERE meta_id = OLD.meta_id;

    SELECT horas_planejadas INTO planejadas
    FROM meta WHERE id = OLD.meta_id;

    UPDATE meta SET
        horas_estudadas = total,
        status = CASE
            WHEN total = 0 THEN 'nao_iniciada'
            WHEN total >= planejadas THEN 'concluida'
            ELSE 'em_andamento'
        END
    WHERE id = OLD.meta_id;
END$$

DELIMITER ;