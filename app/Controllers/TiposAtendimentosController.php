<?php

class TiposAtendimentosController
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
        $sql = 'SELECT id, nome, descricao, status, criado_em, atualizado_em
                FROM tipos_atendimentos 
                ORDER BY id DESC';
                
        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $sql = 'SELECT id, nome, descricao, status, criado_em, atualizado_em
                FROM tipos_atendimentos 
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tipo) {
            $this->json(['erro' => 'Tipo de atendimento não encontrado.'], 404);
            return;
        }
        
        $this->json($tipo);
    }

    public function criar(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '') ?: null;
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '') {
            $this->json(['erro' => 'O nome do tipo de atendimento é obrigatório.'], 422);
            return;
        }
        
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $sql = 'INSERT INTO tipos_atendimentos (nome, descricao, status)
                    VALUES (:nome, :descricao, :status)';
                    
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->execute(compact('nome', 'descricao', 'status'));
            
            $this->json([
                'mensagem' => 'Tipo de atendimento cadastrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], 201);
            
        } catch (PDOException $e) {
            error_log("Erro no TiposAtendimentosController (Criar): " . $e->getMessage());
            $this->json(['erro' => 'Erro ao cadastrar tipo de atendimento.'], 500);
        }
    }

    public function atualizar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '') ?: null;
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '') {
            $this->json(['erro' => 'ID e nome são obrigatórios para atualização.'], 422);
            return;
        }
        
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $sql = 'UPDATE tipos_atendimentos 
                    SET nome = :nome, 
                        descricao = :descricao, 
                        status = :status
                    WHERE id = :id';
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(compact('id', 'nome', 'descricao', 'status'));
            
            $this->json(['mensagem' => 'Tipo de atendimento atualizado com sucesso.']);
            
        } catch (PDOException $e) {
            error_log("Erro no TiposAtendimentosController (Atualizar): " . $e->getMessage());
            $this->json(['erro' => 'Erro ao atualizar tipo de atendimento.'], 500);
        }
    }

    public function inativar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        try {
            $sql = "UPDATE tipos_atendimentos SET status = 'inativo' WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            $this->json(['mensagem' => 'Tipo de atendimento inativado com sucesso.']);
            
        } catch (PDOException $e) {
            error_log("Erro no TiposAtendimentosController (Inativar): " . $e->getMessage());
            $this->json(['erro' => 'Erro ao inativar tipo de atendimento.'], 500);
        }
    }
}
?>