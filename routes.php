<?php
// Carrega o controller responsável pelos endpoints de usuários.
// Observação: o arquivo no projeto está no singular (UsuarioController.php).
require_once __DIR__ . '/app/Controllers/UsuarioController.php';
require_once __DIR__ . '/app/Controllers/AtendimentoController.php';
require_once __DIR__ . '/app/Controllers/PessoaController.php';
require_once __DIR__ . '/app/Controllers/TipoAtendimentoController.php';

// Define controller e action por query string.
// Exemplo: ?controller=usuarios&action=listar
$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Este roteador é simples: só reconhece o controller "usuarios".
if ($controller === 'usuarios') {
    $usuariosController = new UsuariosController();

    // Escolhe qual método do controller executar.
    switch ($action) {
        case 'listar':
            $usuariosController->listar();
            break;
            
        case 'buscar':
            $usuariosController->buscarPorId();
            break;
            
        case 'criar':
            $usuariosController->criar();
            break;
            
        case 'atualizar':
            $usuariosController->atualizar();
            break;
            
        case 'excluir':
            $usuariosController->excluir();
            break;
            
        default:
            // Retorno padrão para action inválida.
            echo 'Ação de usuários não encontrada.';
            break;
    }
} elseif ($controller === 'atendimentos') {
    $atendimentoController = new AtendimentoController();

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
            
        case 'atualizar':
            $atendimentoController->atualizar();
            break;
            
        case 'excluir':
            $atendimentoController->excluir();
            break;
            
        default:
            echo 'Ação de atendimentos não encontrada.';
            break;
    }
} elseif ($controller === 'pessoas') {
    $pessoaController = new PessoaController();

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
            
        case 'excluir':
            $pessoaController->excluir();
            break;
            
        default:
            echo 'Ação de pessoas não encontrada.';
            break;
    }
} elseif ($controller === 'tipos_atendimento') {
    $tipoAtendimentoController = new TipoAtendimentoController();

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
            
        case 'excluir':
            $tipoAtendimentoController->excluir();
            break;
            
        default:
            echo 'Ação de tipos de atendimento não encontrada.';
            break;
    }
} else {
    // Resposta básica para indicar que a aplicação está no ar.
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução. Exemplos de rotas para testar:</p>';
    echo '<ul>';
    echo '<li><a href="?controller=usuarios&action=listar">Listar Usuários</a></li>';
    echo '<li><a href="?controller=pessoas&action=listar">Listar Pessoas</a></li>';
    echo '<li><a href="?controller=tipos_atendimentos&action=listar">Listar Tipos de Atendimento</a></li>';
    echo '<li><a href="?controller=atendimentos&action=listar">Listar Atendimentos</a></li>';
    echo '</ul>';
}

?>