-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/06/2026 às 01:21
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atendelab`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `tipo_atendimento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('aberto','em_andamento','concluido') DEFAULT 'aberto',
  `data_atendimento` date NOT NULL,
  `horario_atendimento` time NOT NULL,
  `observacao_final` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `pessoa_id`, `tipo_atendimento_id`, `usuario_id`, `descricao`, `status`, `data_atendimento`, `horario_atendimento`, `observacao_final`, `criado_em`, `atualizado_em`) VALUES
(2, 4, 4, 4, 'Mudança de Turma', 'aberto', '2026-06-23', '20:00:00', NULL, '2026-06-23 22:44:46', '2026-06-23 22:44:46'),
(6, 4, 4, 4, 'Mudança de Turma 2', 'aberto', '2026-06-23', '20:00:00', NULL, '2026-06-23 22:57:20', '2026-06-23 22:57:20'),
(7, 4, 4, 4, 'Mudança de Turma 3', 'aberto', '2026-06-23', '20:00:00', NULL, '2026-06-23 22:57:51', '2026-06-23 22:57:51'),
(8, 4, 4, 4, 'Mudança de Turma 4', 'concluido', '2026-06-23', '20:00:00', 'Fecho', '2026-06-23 22:58:30', '2026-06-23 23:03:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `documento` varchar(30) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `curso` varchar(120) DEFAULT NULL,
  `periodo` varchar(20) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `documento`, `telefone`, `email`, `curso`, `periodo`, `observacoes`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'João da Silva', '123.456.789-00', '(47) 99999-0001', 'joao.silva@exemplo.com', 'Engenharia de Software', '5º', NULL, 'ativo', '2026-06-23 19:44:42', '2026-06-23 19:44:42'),
(2, 'Ana Carolina', '987.654.321-00', '(47) 99999-0002', 'ana.carolina@exemplo.com', 'Sistemas de Informação', '7º', NULL, 'ativo', '2026-06-23 19:44:42', '2026-06-23 19:44:42'),
(3, 'Carlos Henrique Souza', '321.654.987-10', '(47) 99999-0010', 'carlos.souza@exemplo.com', 'Engenharia de Software', '3º', 'Aluno interessado em orientação sobre atividades complementares.', 'ativo', '2026-06-23 19:50:34', '2026-06-23 19:50:34'),
(4, 'Mariana Oliveira Costa', '741.852.963-20', '(47) 99999-0011', 'mariana.oliveira@exemplo.com', 'Sistemas de Informação', '5º', NULL, 'inativo', '2026-06-23 19:50:34', '2026-06-23 23:06:51'),
(7, 'Emilly', '123.456.789-01', '(47) 99999-0012', 'emilly@exem.com', 'Engenharia de Software', '5º', 'Aula04 Atualizado', 'inativo', '2026-06-23 22:00:09', '2026-06-23 23:21:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_atendimentos`
--

CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_atendimentos`
--

INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Dúvida acadêmica', 'Dúvidas sobre disciplinas, conteúdos e atividades.', 'ativo', '2026-06-23 19:44:42', '2026-06-23 19:44:42'),
(2, 'Orientação de atividade', 'Orientações sobre trabalhos, TCC e projetos.', 'ativo', '2026-06-23 19:44:42', '2026-06-23 19:44:42'),
(3, 'Suporte técnico', 'Problemas com sistemas, equipamentos e acessos.', 'ativo', '2026-06-23 19:44:42', '2026-06-23 19:44:42'),
(4, 'Matrícula e documentação', 'Solicitações de matrícula, declarações e históricos.', 'ativo', '2026-06-23 19:44:42', '2026-06-23 19:44:42'),
(5, 'Acesso ao laboratório', 'Liberação de uso e agendamento de laboratórios.', 'ativo', '2026-06-23 19:44:42', '2026-06-23 19:44:42'),
(6, 'Revisão de avaliação', 'Solicitações de revisão de provas, trabalhos e atividades avaliativas.', 'ativo', '2026-06-23 19:50:34', '2026-06-23 19:50:34'),
(7, 'Apoio à extensão', 'Orientações relacionadas a projetos de extensão e atividades comunitárias.', 'ativo', '2026-06-23 19:50:34', '2026-06-23 19:50:34'),
(8, 'Aula 04', 'Criado para Aula 04 Atualizado', 'inativo', '2026-06-23 22:13:25', '2026-06-23 22:15:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`, `atualizado_em`) VALUES
(4, 'admin', 'admin@atendelab.com', '$2y$10$iurk3a2SFJexyFVkd5aU7uBcX4tRI/y4vy4LzwNpeCgbvyDB5r3uO', 'admin', 'ativo', '2026-06-23 19:56:02', '2026-06-23 19:56:02'),
(5, 'Mateus', 'mateus@atendelab.com', '$2y$10$UikX.oET7Q1UnZJxzXmDme/ZxT8cgvouofCgfNVWs5oYOxnFkOZsi', 'atendente', 'inativo', '2026-06-23 23:05:34', '2026-06-23 23:05:34');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atendimentos_pessoa` (`pessoa_id`),
  ADD KEY `fk_atendimentos_tipo` (`tipo_atendimento_id`),
  ADD KEY `fk_atendimentos_usuario` (`usuario_id`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- Índices de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_atendimentos_pessoa` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`),
  ADD CONSTRAINT `fk_atendimentos_tipo` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`),
  ADD CONSTRAINT `fk_atendimentos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
