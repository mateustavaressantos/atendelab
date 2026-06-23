<?php

class PessoasController
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
        $sql = 'SELECT id, nome, documento, telefone, curso, periodo, email, observacoes, status, criado_em, atualizado_em
                FROM pessoas
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

        $sql = 'SELECT id, nome, documento, telefone, curso, periodo, email, observacoes, status, criado_em, atualizado_em
                FROM pessoas
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            $this->json(['erro' => 'Pessoa não encontrada.'], 404);
            return;
        }

        $this->json($pessoa);
    }

    public function criar(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        // Validações
        if ($nome === '' || $documento === '' || $email === '') {
            $this->json(['erro' => 'Nome, documento e e-mail são obrigatórios.'], 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['erro' => 'E-mail inválido.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, documento, telefone, curso, periodo, email, observacoes, status)
                    VALUES (:nome, :documento, :telefone, :curso, :periodo, :email, :observacoes, :status)';

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute(compact(
                'nome', 'documento', 'telefone', 'curso', 'periodo', 'email', 'observacoes', 'status'
            ));

            $this->json([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], 201);

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->json(['erro' => 'Este documento ou e-mail já está cadastrado.'], 409);
            } else {
                error_log("Erro no PessoasController (Criar): " . $e->getMessage());
                $this->json(['erro' => 'Erro ao cadastrar pessoa.'], 500);
            }
        }
    }

    public function atualizar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '' || $documento === '' || $email === '') {
            $this->json(['erro' => 'Id, nome, documento e e-mail são obrigatórios.'], 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['erro' => 'E-mail inválido.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $sql = 'UPDATE pessoas
                    SET nome = :nome,
                        documento = :documento,
                        telefone = :telefone,
                        curso = :curso,
                        periodo = :periodo,
                        email = :email,
                        observacoes = :observacoes,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(compact(
                'nome', 'documento', 'telefone', 'curso', 'periodo', 'email', 'observacoes', 'status', 'id'
            ));

            $this->json(['mensagem' => 'Dados da pessoa atualizados com sucesso.']);

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->json(['erro' => 'Este documento ou e-mail já está sendo usado por outra pessoa.'], 409);
            } else {
                error_log("Erro no PessoasController (Atualizar): " . $e->getMessage());
                $this->json(['erro' => 'Erro ao atualizar dados da pessoa.'], 500);
            }
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
            $sql = "UPDATE pessoas SET status = 'inativo' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $this->json(['mensagem' => 'Pessoa inativada com sucesso.']);

        } catch (PDOException $e) {
            error_log("Erro no PessoasController (Inativar): " . $e->getMessage());
            $this->json(['erro' => 'Erro ao inativar pessoa.'], 500);
        }
    }
}
?>