<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard - AtendeLab</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">AtendeLab</span>

        <a class="btn btn-outline-light btn-sm"
           href="?controller=auth&action=logout">
            Sair
        </a>
    </div>
</nav>

<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h4">Área restrita</h1>

            <p class="mb-1">
                Bem-vindo,
                <strong>
                    <?= htmlspecialchars(
                        $usuario['nome'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>.
            </p>

            <p class="text-muted">
                Perfil:
                <?= htmlspecialchars(
                    $usuario['perfil'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <?php if ($usuario['perfil'] === 'admin'): ?>
                <div class="alert alert-info mt-3">
                    <strong>Modo Administrador:</strong> Você possui acesso total às configurações e relatórios do sistema.
                </div>
            <?php endif; ?>

            <a class="btn btn-primary"
               href="?controller=usuarios&action=listar">
                Testar rota protegida de usuários
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title text-secondary mb-0 fw-bold">Módulos Planejados para Implementação</h5>
        </div>
        <div class="card-body">
            <p class="text-muted small">Conforme estabelecido no cronograma da Fábrica de Software, os seguintes módulos serão integrados ao ecossistema para viabilizar o MVP:</p>
            <ul class="list-group list-group-flush border rounded-3 shadow-sm">
                <li class="list-group-item py-3">
                    <strong>1. Módulo de Pessoas Atendidas:</strong> 
                    <span class="text-muted block d-md-inline">Permitirá o cadastro completo de alunos e comunidade externa, documentando dados institucionais, telefone, curso, período e o status do registro.</span>
                </li>
                <li class="list-group-item py-3">
                    <strong>2. Módulo de Tipos de Atendimento:</strong> 
                    <span class="text-muted block d-md-inline">Estruturação de categorias customizadas (Monitorias, Suporte Técnico, Laboratórios) contendo descrições específicas para classificar as demandas recebidas.</span>
                </li>
                <li class="list-group-item py-3">
                    <strong>3. Módulo de Registro de Atendimentos:</strong> 
                    <span class="text-muted block d-md-inline">Área central do sistema responsável por correlacionar o usuário atendido, a tipificação da solicitação, o responsável interno, a data/hora e o acompanhamento de status (Pendente, Em Andamento, Concluído ou Cancelado).</span>
                </li>
                <li class="list-group-item py-3">
                    <strong>4. Módulo de Relatórios e Filtros Avançados:</strong> 
                    <span class="text-muted block d-md-inline">Ferramentas de extração de indicadores consolidados e exportação de dados analíticos formatados para fins de auditoria e prestação de contas.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

</body>
</html>