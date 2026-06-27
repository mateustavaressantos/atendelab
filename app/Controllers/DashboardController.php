<?php

require_once __DIR__ . '/../Middleware/auth.php';

class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        if (!$pdo) {
            http_response_code(500);
            exit('Conexão com o banco não disponível.');
        }
        $this->pdo = $pdo;
    }

    public function resumo(): void
    {
        // O middleware já inicia a sessão se necessário
        exigirAutenticacao();

        try {
            $totalPessoas = $this->pdo->query('SELECT COUNT(*) FROM pessoas')->fetchColumn();
            $totalTipos = $this->pdo->query('SELECT COUNT(*) FROM tipos_atendimentos')->fetchColumn();
            $totalAtendimentos = $this->pdo->query('SELECT COUNT(*) FROM atendimentos')->fetchColumn();

            header('Content-Type: application/json');
            echo json_encode([
                'indicadores' => [
                    'total_pessoas' => (int) $totalPessoas,
                    'total_tipos' => (int) $totalTipos,
                    'total_atendimentos' => (int) $totalAtendimentos
                ]
            ]);
            exit;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao consultar o banco: ' . $e->getMessage()]);
            exit;
        }
    }
}