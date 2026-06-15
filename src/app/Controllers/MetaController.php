<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MetaModel.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Não autenticado']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    $model = new MetaModel($pdo);
    $usuario_id = $_SESSION['usuario_id'];

    $acao = $_GET['acao'] ?? '';
    if (!$acao) {
        $input = json_decode(file_get_contents('php://input'), true);
        $acao = $input['acao'] ?? '';
    }

    switch ($acao) {

        /* ===== METAS ===== */

        case 'criar_meta':
            $input = json_decode(file_get_contents('php://input'), true);

            // ✅ CORREÇÃO: JS envia 'horas_planejadas' (já em decimal), não 'horas'
            $horas = isset($input['horas_planejadas'])
                ? (float) $input['horas_planejadas']
                : converterTempoParaDecimal($input['horas'] ?? '00:00');

            $sucesso = $model->criar(
                $usuario_id,
                $input['titulo'],
                $input['descricao'] ?? '',
                $horas,
                $input['prazo']
            );
            echo json_encode([
                'status'  => $sucesso ? 'success' : 'error',
                'message' => $sucesso ? 'Meta criada!' : 'Erro ao criar meta'
            ]);
            break;

        case 'listar_metas':
            $metas = $model->listarPorUsuario($usuario_id);
            foreach ($metas as &$meta) {
                $meta['progresso'] = $meta['horas_planejadas'] > 0
                    ? min(100, ($meta['horas_estudadas'] / $meta['horas_planejadas']) * 100)
                    : 0;
            }
            echo json_encode(['status' => 'success', 'data' => $metas]);
            break;

        case 'detalhe_meta':
            $meta_id = $_GET['id'] ?? 0;
            $meta = $model->buscarPorId($meta_id, $usuario_id);
            if (!$meta) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Meta não encontrada']);
                break;
            }
            $meta['progresso'] = $meta['horas_planejadas'] > 0
                ? min(100, ($meta['horas_estudadas'] / $meta['horas_planejadas']) * 100)
                : 0;
            $sessoes = $model->listarSessoesPorMeta($meta_id, $usuario_id);
            echo json_encode(['status' => 'success', 'meta' => $meta, 'sessoes' => $sessoes]);
            break;

        case 'atualizar_meta':
            $input = json_decode(file_get_contents('php://input'), true);

            // ✅ CORREÇÃO: JS não envia 'status', então preserva o status atual
            if (!isset($input['status'])) {
                $metaAtual = $model->buscarPorId($input['id'], $usuario_id);
                $input['status'] = $metaAtual['status'] ?? 'nao_iniciada';
            }

            // ✅ CORREÇÃO: JS envia 'horas_planejadas' já em decimal
            // Garante que o campo correto está presente no array $input
            if (!isset($input['horas_planejadas']) && isset($input['horas'])) {
                $input['horas_planejadas'] = converterTempoParaDecimal($input['horas']);
            }

            $sucesso = $model->atualizar($input['id'], $usuario_id, $input);
            echo json_encode([
                'status'  => $sucesso ? 'success' : 'error',
                'message' => $sucesso ? 'Meta atualizada!' : 'Erro ao atualizar'
            ]);
            break;

        case 'excluir_meta':
            $input = json_decode(file_get_contents('php://input'), true);
            $sucesso = $model->excluir($input['id'], $usuario_id);
            echo json_encode([
                'status'  => $sucesso ? 'success' : 'error',
                'message' => $sucesso ? 'Meta excluída!' : 'Erro ao excluir'
            ]);
            break;

        /* ===== SESSÕES ===== */

        case 'criar_sessao':
            $input = json_decode(file_get_contents('php://input'), true);
            $sucesso = $model->criarSessao(
                $input['meta_id'], $usuario_id, $input['data'],
                $input['tempo_estudado'], $input['observacao'] ?? '',
                $input['foco'] ?? null, $input['progresso'] ?? null
            );
            echo json_encode([
                'status'  => $sucesso ? 'success' : 'error',
                'message' => $sucesso ? 'Sessão registrada!' : 'Erro ao registrar sessão'
            ]);
            break;

        case 'listar_sessoes_meta':
            $meta_id = $_GET['meta_id'] ?? 0;
            $sessoes = $model->listarSessoesPorMeta($meta_id, $usuario_id);
            echo json_encode(['status' => 'success', 'data' => $sessoes]);
            break;

        case 'listar_todas_sessoes':
            $sessoes = $model->listarTodasSessoes($usuario_id);
            foreach ($sessoes as &$sessao) {
                $sessao['progresso_meta'] = $sessao['horas_planejadas'] > 0
                    ? min(100, ($sessao['tempo_estudado'] / $sessao['horas_planejadas']) * 100)
                    : 0;
            }
            echo json_encode(['status' => 'success', 'data' => $sessoes]);
            break;

        case 'atualizar_sessao':
            $input = json_decode(file_get_contents('php://input'), true);
            $sucesso = $model->atualizarSessao($input['id'], $usuario_id, $input);
            echo json_encode([
                'status'  => $sucesso ? 'success' : 'error',
                'message' => $sucesso ? 'Sessão atualizada!' : 'Erro ao atualizar'
            ]);
            break;

        case 'excluir_sessao':
            $input = json_decode(file_get_contents('php://input'), true);
            $sucesso = $model->excluirSessao($input['id'], $usuario_id);
            echo json_encode([
                'status'  => $sucesso ? 'success' : 'error',
                'message' => $sucesso ? 'Sessão excluída!' : 'Erro ao excluir'
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Ação não reconhecida']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno: ' . $e->getMessage()]);
}

function converterTempoParaDecimal($tempo) {
    if (strpos($tempo, ':') !== false) {
        list($h, $m) = explode(':', $tempo);
        return (float)$h + ((float)$m / 60);
    }
    return (float)$tempo;
}