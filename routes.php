<?php

// Imports de autenticação e sessão
require_once __DIR__ . '/app/Middleware/auth.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';

// Carrega os controladores responsáveis pelas entidades e regras de negócio
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';

// Define controller e action por query string
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

    case 'dashboard':
        $dashboard = new DashboardController();
        switch ($action) {
            case 'resumo':
                $dashboard->resumo();
                break;
            default:
                http_response_code(404);
                echo 'Ação não encontrada.';
        }
        break;

    case 'frontend':
        $front = new FrontendController();
        switch ($action) {
            case 'pessoas':
                $front->pessoas();
                break;
            case 'tipos':
                $front->tiposAtendimentos();
                break;
            case 'atendimentos':
                $front->atendimentos();
                break;
            default:
                http_response_code(404);
                echo 'Página não encontrada.';
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
            case 'buscarPorId':
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
        require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
        $atendimentosController = new AtendimentosController();
        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;
            case 'visualizar':
                $atendimentosController->buscarPorId();
                break;
            case 'criar':
                $atendimentosController->criar();
                break;
            case 'alterarStatus':
            case 'atualizarStatus':
                $atendimentosController->alterarStatus(); 
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
            case 'buscarPorId':
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

    case 'tipos':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
        $tiposController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $tiposController->buscarPorId();
                break;
            case 'criar':
                $tiposController->criar();
                break;
            case 'atualizar':
                $tiposController->atualizar();
                break;
            case 'inativar':
                $tiposController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de tipos de atendimento não encontrada.');
        }
        break;

    // ROTA NÃO ENCONTRADA (404)
    default:
        http_response_code(404);
        echo 'Controller não encontrado.';
}

?>