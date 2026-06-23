<?php

// Imports do primeiro bloco de código
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

// Carrega o controller responsável pelos endpoints de usuários.
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';

// Define controller e action por query string.
// Exemplo: ?controller=usuarios&action=listar
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch ($controller) {
    
    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;

            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                $authController->dashboard();
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo 'Ação de autenticação não encontrada.';
        }
        break;

    case 'usuarios':
        exigirAutenticacao();
        $usuariosController = new UsuariosController();

        switch ($action) {
            case 'listar':
                $usuariosController->listar();
                break;

            case 'buscar':
            case 'buscarPorId': // Mapeia ambas as possibilidades dos seus códigos anteriores
                $usuariosController->buscarPorId();
                break;

            case 'criar':
                $usuariosController->criar();
                break;

            case 'atualizar':
                $usuariosController->atualizar();
                break;

            case 'inativar':
                $usuariosController->inativar();
                break;

            default:
                http_response_code(404);
                echo 'Ação de usuários não encontrada.';
        }
        break;

    case 'atendimentos':
        exigirAutenticacao();
        $atendimentoController = new AtendimentosController();

        switch ($action) {
            case 'listar':
                $atendimentoController->listar();
                break;
                
            case 'buscar':
                $atendimentoController->buscarPorId();
                break;
                
            case 'criar':
                $atendimentoController->criar();
                break;
                
            case 'alterarStatus':
                $atendimentoController->alterarStatus();
                break;
                
            default:
                http_response_code(404);
                echo 'Ação de atendimentos não encontrada.';
        }
        break;

    case 'pessoas':
        exigirAutenticacao();
        $pessoaController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoaController->listar();
                break;
                
            case 'buscar':
                $pessoaController->buscarPorId();
                break;
                
            case 'criar':
                $pessoaController->criar();
                break;
                
            case 'atualizar':
                $pessoaController->atualizar();
                break;
                
            case 'inativar':
                $pessoaController->inativar();
                break;
                
            default:
                http_response_code(404);
                echo 'Ação de pessoas não encontrada.';
        }
        break;

    case 'tipos_atendimentos':
        exigirAutenticacao();
        $tipoAtendimentoController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tipoAtendimentoController->listar();
                break;
                
            case 'buscar':
                $tipoAtendimentoController->buscarPorId();
                break;
                
            case 'criar':
                $tipoAtendimentoController->criar();
                break;
                
            case 'atualizar':
                $tipoAtendimentoController->atualizar();
                break;
                
            case 'inativar':
                $tipoAtendimentoController->inativar();
                break;
                
            default:
                http_response_code(404);
                echo 'Ação de tipos de atendimento não encontrada.';
        }
        break;

    case 'home':
        echo '<h1>AtendeLab</h1>';
        echo '<p>Projeto em execução. Exemplos de rotas para testar:</p>';
        echo '<ul>';
        echo '<li><a href="?controller=auth&action=login">Ir para o Login</a></li>';
        echo '<li><a href="?controller=usuarios&action=listar">Listar Usuários (Requer Login)</a></li>';
        echo '<li><a href="?controller=pessoas&action=listar">Listar Pessoas</a></li>';
        echo '<li><a href="?controller=tipos_atendimento&action=listar">Listar Tipos de Atendimento</a></li>';
        echo '<li><a href="?controller=atendimentos&action=listar">Listar Atendimentos</a></li>';
        echo '</ul>';
        break;

    // ROTA NÃO ENCONTRADA (404)
    default:
        http_response_code(404);
        echo 'Controller não encontrado.';
}

?>