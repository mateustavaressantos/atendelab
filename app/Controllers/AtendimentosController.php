<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $sql = 'SELECT 
                        a.id, 
                        a.data_atendimento, 
                        a.horario_atendimento AS hora_atendimento, 
                        a.descricao, 
                        a.observacao, 
                        a.status,
                        p.nome AS pessoa_nome, 
                        u.nome AS usuario_nome, 
                        t.nome AS tipo_atendimento_nome
                    FROM atendimentos a
                    JOIN pessoas p ON a.pessoa_id = p.id
                    JOIN usuarios u ON a.usuario_id = u.id
                    JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                    ORDER BY a.id DESC';

            $stmt = $this->pdo->query($sql);
            $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao listar atendimentos.'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido ou não fornecido.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $sql = 'SELECT 
                        a.id, 
                        a.pessoa_id, 
                        a.usuario_id, 
                        a.tipo_atendimento_id, 
                        a.data_atendimento, 
                        a.horario_atendimento AS hora_atendimento, 
                        a.descricao, 
                        a.observacao, 
                        a.status
                    FROM atendimentos a
                    WHERE a.id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($atendimento) {
                echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(['erro' => 'Atendimento não encontrado.'], JSON_UNESCAPED_UNICODE);
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao buscar atendimento.'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        
        $data_atendimento = $_POST['data_atendimento'] ?? '';
        $hora_atendimento = $_POST['hora_atendimento'] ?? ''; 
        $descricao = trim($_POST['descricao'] ?? null);
        $observacao = trim($_POST['observacao'] ?? null);
        
        $status = $_POST['status'] ?? 'aberto'; 

        if (!$pessoa_id || !$usuario_id || !$tipo_atendimento_id || empty($data_atendimento) || empty($hora_atendimento)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Pessoa, Usuário, Tipo, Data e Hora são obrigatórios.']);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status do atendimento inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (pessoa_id, usuario_id, tipo_atendimento_id, data_atendimento, horario_atendimento, descricao, observacao, status)
                    VALUES (:pessoa_id, :usuario_id, :tipo_atendimento_id, :data_atendimento, :hora_atendimento, :descricao, :observacao, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':hora_atendimento', $hora_atendimento);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':observacao', $observacao);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento registrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            if ($e->getCode() == 23000) {
                echo json_encode(['erro' => 'Erro de integridade. Verifique se a pessoa, o usuário e o tipo de atendimento existem.']);
            } else {
                echo json_encode(['erro' => 'Erro ao registrar atendimento.']);
            }
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? null;

        if (!$id || empty($status)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Os campos ID e Status são obrigatórios.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status do atendimento inválido.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Status do atendimento actualizado com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar o status do atendimento.'], JSON_UNESCAPED_UNICODE);
        }
    }
}

?>