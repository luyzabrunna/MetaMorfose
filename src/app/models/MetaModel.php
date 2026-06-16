<?php
class MetaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /* ============ CRUD DE METAS ============ */

    public function criar($usuario_id, $titulo, $descricao, $horas_planejadas, $prazo) {
        $sql = "INSERT INTO meta (usuario_id, titulo, descricao, horas_planejadas, prazo, status) 
                VALUES (?, ?, ?, ?, ?, 'nao_iniciada')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$usuario_id, $titulo, $descricao, $horas_planejadas, $prazo]);
    }

    public function listarPorUsuario($usuario_id) {
        $sql = "SELECT * FROM meta WHERE usuario_id = ? ORDER BY criado_em DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($meta_id, $usuario_id) {
        $sql = "SELECT * FROM meta WHERE id = ? AND usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$meta_id, $usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($meta_id, $usuario_id, $dados) {
        $sql = "UPDATE meta SET 
                titulo = ?, descricao = ?, horas_planejadas = ?, prazo = ?, status = ? 
                WHERE id = ? AND usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['titulo'], $dados['descricao'], $dados['horas_planejadas'],
            $dados['prazo'], $dados['status'], $meta_id, $usuario_id
        ]);
    }

    public function excluir($meta_id, $usuario_id) {
        $sql = "DELETE FROM meta WHERE id = ? AND usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$meta_id, $usuario_id]);
    }

    /* ============ CRUD DE SESSÕES ============ */

    public function criarSessao($meta_id, $usuario_id, $data, $tempo_estudado, $observacao = '', $foco = null, $progresso = null) {
        // Verifica se a meta pertence ao usuário
        $meta = $this->buscarPorId($meta_id, $usuario_id);
        if (!$meta) return false;

        $sql = "INSERT INTO sessao (meta_id, data, tempo_estudado, observacao, foco, progresso) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$meta_id, $data, $tempo_estudado, $observacao, $foco, $progresso]);
    }

    public function listarSessoesPorMeta($meta_id, $usuario_id) {
        $meta = $this->buscarPorId($meta_id, $usuario_id);
        if (!$meta) return [];

        $sql = "SELECT * FROM sessao WHERE meta_id = ? ORDER BY data DESC, criado_em DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$meta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodasSessoes($usuario_id) {
        $sql = "SELECT s.*, m.titulo as meta_titulo, m.horas_planejadas 
                FROM sessao s 
                JOIN meta m ON s.meta_id = m.id 
                WHERE m.usuario_id = ? 
                ORDER BY s.data DESC, s.criado_em DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarSessao($sessao_id, $usuario_id, $dados) {
        $sql = "SELECT s.* FROM sessao s 
                JOIN meta m ON s.meta_id = m.id 
                WHERE s.id = ? AND m.usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sessao_id, $usuario_id]);
        if (!$stmt->fetch()) return false;

        $sql = "UPDATE sessao SET data = ?, tempo_estudado = ?, observacao = ?, foco = ?, progresso = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['data'], $dados['tempo_estudado'], $dados['observacao'],
            $dados['foco'], $dados['progresso'], $sessao_id
        ]);
    }

    public function excluirSessao($sessao_id, $usuario_id) {

        // Primeiro verifica se a sessão pertence ao usuário
         $sql = "SELECT s.id FROM sessao s 
                JOIN meta m ON s.meta_id = m.id 
                WHERE s.id = ? AND m.usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sessao_id, $usuario_id]);
        if (!$stmt->fetch()) return false;

        // Depois exclui só da tabela sessao (sem JOIN)
        $sql = "DELETE FROM sessao WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$sessao_id]);
    }

    /* ============ MÉTODOS PARA O DASHBOARD ============ */
 
    // 3 metas mais recentes
    public function listarMetasRecentes($usuario_id, $limite = 3) {
        $sql = "SELECT * FROM meta 
                WHERE usuario_id = ? 
                ORDER BY criado_em DESC 
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(2, (int) $limite, PDO::PARAM_INT); // ← cast para int
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3 sessões mais recentes
    public function listarSessoesRecentes($usuario_id, $limite = 3) {
        $sql = "SELECT s.*, m.titulo as meta_titulo 
                FROM sessao s 
                JOIN meta m ON s.meta_id = m.id 
                WHERE m.usuario_id = ? 
                ORDER BY s.data DESC, s.criado_em DESC 
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(2, (int) $limite, PDO::PARAM_INT); // ← cast para int
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    // Meta com prazo mais próximo (não concluída)
    public function metaPrazoMaisProximo($usuario_id) {
        $sql = "SELECT * FROM meta 
                WHERE usuario_id = ? 
                AND status != 'concluida' 
                AND prazo >= CURDATE() 
                ORDER BY prazo ASC 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    // Total de horas estudadas pelo usuário
    public function totalHorasEstudadas($usuario_id) {
        $sql = "SELECT COALESCE(SUM(s.tempo_estudado), 0) as total 
                FROM sessao s 
                JOIN meta m ON s.meta_id = m.id 
                WHERE m.usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario_id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($resultado['total'], 1);
    }
}