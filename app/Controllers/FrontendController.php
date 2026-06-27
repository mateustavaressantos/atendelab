<?php

require_once __DIR__ . '/../Middleware/auth.php';

class FrontendController
{
    public function pessoas(): void
    {
        exigirAutenticacao();
        require_once __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tiposAtendimentos(): void
    {
        exigirAutenticacao();
        require_once __DIR__ . '/../Views/tipos_atendimentos/index.php';
    }

    public function atendimentos(): void
    {
        exigirAutenticacao();
        require_once __DIR__ . '/../Views/atendimentos/index.php';
    }
}