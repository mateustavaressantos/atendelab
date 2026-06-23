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

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void
    {
        try {
            $sql = 'SELECT a.id, p.nome AS pessoa_nome,
                           t.nome AS tipo_nome,
                           u.nome AS responsavel_nome,
                           a.descricao, a.status,
                           a.data_atendimento, a.horario_atendimento,
                           a.observacao_final
                    FROM atendimentos a
                    INNER JOIN pessoas p ON p.id = a.pessoa_id
                    INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                    INNER JOIN usuarios u ON u.id = a.usuario_id
                    ORDER BY a.id DESC';

            $atendimentos = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($atendimentos as &$atendimento) {
                $atendimento['protocolo'] = 'ATD-' . str_pad((string) $atendimento['id'], 4, '0', STR_PAD_LEFT);
            }

            $this->json($atendimentos);

        } catch (PDOException $e) {
            error_log("Erro no AtendimentosController (Listar): " . $e->getMessage());
            $this->json(['erro' => 'Erro ao listar atendimentos.'], 500);
        }
    }

    public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT a.*, p.nome AS pessoa_nome,
                        t.nome AS tipo_nome, u.nome AS responsavel_nome
                FROM atendimentos a
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u ON u.id = a.usuario_id
                WHERE a.id = :id'
            );
            $stmt->execute(['id' => $id]);

            $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$atendimento) {
                $this->json(['erro' => 'Atendimento não encontrado.'], 404);
                return;
            }

            $atendimento['protocolo'] = 'ATD-' . str_pad((string) $atendimento['id'], 4, '0', STR_PAD_LEFT);

            $this->json($atendimento);

        } catch (PDOException $e) {
            error_log("Erro no AtendimentosController (BuscarPorId): " . $e->getMessage());
            $this->json(['erro' => 'Erro ao buscar atendimento.'], 500);
        }
    }

    public function criar(): void
    {
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        
        $data_atendimento = $_POST['data_atendimento'] ?? '';
        $horario_atendimento = $_POST['horario_atendimento'] ?? ''; 
        $descricao = trim($_POST['descricao'] ?? '');
        
        $observacao_final = trim($_POST['observacao_final'] ?? '') ?: null;
        
        $status = $_POST['status'] ?? 'aberto'; 

        if (!$pessoa_id || !$usuario_id || !$tipo_atendimento_id || $descricao === '' || $data_atendimento === '' || $horario_atendimento === '') {
            $this->json(['erro' => 'Pessoa, Usuário, Tipo, Data e Horário são obrigatórios.'], 422);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'Status do atendimento inválido.'], 422);
            return;
        }

        $sqlCheck = 'SELECT 
                        (SELECT status FROM pessoas WHERE id = :pessoa_id) AS status_pessoa,
                        (SELECT status FROM usuarios WHERE id = :usuario_id) AS status_usuario,
                        (SELECT status FROM tipos_atendimentos WHERE id = :tipo_id) AS status_tipo';

        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            'pessoa_id' => $pessoa_id, 
            'usuario_id' => $usuario_id, 
            'tipo_id' => $tipo_atendimento_id
        ]);
        $check = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($check['status_pessoa'] === 'inativo' || 
            $check['status_usuario'] === 'inativo' || 
            $check['status_tipo'] === 'inativo') {
            
            $this->json(['erro' => 'Não é possível abrir um atendimento com cadastros inativos.'], 403);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos 
                    (pessoa_id, usuario_id, tipo_atendimento_id, data_atendimento, horario_atendimento, descricao, observacao_final, status)
                    VALUES 
                    (:pessoa_id, :usuario_id, :tipo_atendimento_id, :data_atendimento, :horario_atendimento, :descricao, :observacao_final, :status)';

            $stmt = $this->pdo->prepare($sql);
            
            $stmt->execute(compact(
                'pessoa_id', 'usuario_id', 'tipo_atendimento_id', 'data_atendimento', 
                'horario_atendimento', 'descricao', 'observacao_final', 'status'
            ));

            $this->json([
                'mensagem' => 'Atendimento registrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], 201);

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->json(['erro' => 'Erro de integridade. Verifique se a pessoa, o usuário e o tipo de atendimento existem.'], 409);
            } else {
                error_log("Erro no AtendimentosController (Criar): " . $e->getMessage());
                $this->json(['erro' => 'Erro ao registrar atendimento.'], 500);
            }
        }
    }

    public function alterarStatus(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? null;
        
        $observacao_final = trim($_POST['observacao_final'] ?? '') ?: null;

        if (!$id || empty($status)) {
            $this->json(['erro' => 'Os campos ID e Status são obrigatórios.'], 422);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'Status do atendimento inválido.'], 422);
            return;
        }

        if ($status === 'concluido' && empty($observacao_final)) {
            $this->json(['erro' => 'Informe a observação final para concluir.'], 422);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET status = :status,
                        observacao_final = :observacao_final
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(compact('status', 'observacao_final', 'id'));

            $this->json(['mensagem' => 'Status e observação do atendimento atualizados com sucesso.']);

        } catch (PDOException $e) {
            error_log("Erro no AtendimentosController (AlterarStatus): " . $e->getMessage());
            $this->json(['erro' => 'Erro ao atualizar o status do atendimento.'], 500);
        }
    }
}
?>