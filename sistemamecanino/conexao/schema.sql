-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/06/2026 às 17:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

 create database oficinamecanica;
  use oficinamecanica;

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `nome completo` varchar(100) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque_pecas_has_os`
--

CREATE TABLE `estoque_pecas_has_os` (
  `estoque_pecas_id` int(11) NOT NULL,
  `OS_id` int(11) NOT NULL,
  `OS_veiculo_id1` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque_pecas_has_os1`
--

CREATE TABLE `estoque_pecas_has_os1` (
  `estoque_pecas_id` int(11) NOT NULL,
  `OS_id` int(11) NOT NULL,
  `OS_veiculo_id1` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro`
--

CREATE TABLE `financeiro` (
  `id` int(10) UNSIGNED NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  `descricao` varchar(255) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `tipo` enum('1: Receita','2: Despesa') NOT NULL,
  `status` enum('PAGO','Aguardando','Cancelado') NOT NULL,
  `OS_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `os`
--

CREATE TABLE `os` (
  `id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `mecanico_id` int(11) NOT NULL,
  `data_entrada` datetime NOT NULL,
  `veiculo_id1` int(11) NOT NULL,
  `clientes_cpf` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pecas`
--

CREATE TABLE `pecas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco_venda` decimal(10,2) NOT NULL,
  `estoque_atual` int(11) DEFAULT 0,
  `estoque_minimo` int(11) DEFAULT 5,
  `url_imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pecas_na_os`
--

CREATE TABLE `pecas_na_os` (
  `pecas_id` int(11) NOT NULL,
  `OS_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `idservicos` int(11) NOT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos_has_os`
--

CREATE TABLE `servicos_has_os` (
  `servicos_idservicos` int(11) NOT NULL,
  `OS_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `perfil` enum('Admin','Mecanico','Recepcionista') NOT NULL,
  `nivel_acesso` int(11) DEFAULT 1,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `salario_base` decimal(10,2) DEFAULT NULL,
  `decimal_comissao` decimal(5,2) DEFAULT 0.00,
  `data_cadastro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_completo`, `email`, `cpf`, `perfil`, `nivel_acesso`, `telefone`, `senha`, `foto_perfil`, `salario_base`, `decimal_comissao`, `data_cadastro`) VALUES
(1, 'Administrador Geral', 'admin@admin.com', '11122233344', 'Admin', 1, '(11) 99999-9999', '$2y$10$DIq5VtmiziLpH2zGtfoQFezgA6BAXnMawBf7H6E6IgHPQG3AU1MmG', NULL, NULL, 0.00, '2026-06-18 18:21:47'),
(2, 'viniciu', 'recep@gmail.com', '12345678900', 'Recepcionista', 1, '(87) 40028-9222', '$2y$10$buQHaqqwTxcvEL2A9Q4ld.MwHkhUSGejWB.OK4538JISO/QVFR2oK', NULL, NULL, 0.00, '2026-06-18 18:48:16'),
(3, 'vinicius', 'vini@gmail.com', '12345601420', 'Mecanico', 1, '(87) 40028-9222', '$2y$10$05fU3t5d7Ga9po2ypcVoe.g4DZyYs/c6bjDVoOqxuRLpiNwSBZAOW', NULL, NULL, 0.00, '2026-06-18 18:50:33'),
(4, 'vinicius', 'viny@gmail.com', '12902370413', 'Mecanico', 1, '(87) 90276-3823', '$2y$10$QWkqvC2VE92BNtQUV5lnuuRS1nczDqULmGiPWTTc2ly2RXx4Gwm7y', NULL, NULL, 0.00, '2026-06-25 14:04:40'),
(5, 'vinicius', 'admin@gmail.com', '12136351761', 'Admin', 1, '(31) 23412-3124', '$2y$10$qVv/bBbVQ0Sub0QPxJ9sG.KAU9ZA5Gq43JchWSYBSMdL.p6cLtTqS', NULL, NULL, 0.00, '2026-06-26 14:35:20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculo`
--

CREATE TABLE `veiculo` (
  `id` int(11) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `marca/modelo` varchar(50) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `cor` varchar(20) DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `clientes_cpf` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cpf`);

--
-- Índices de tabela `estoque_pecas_has_os`
--
ALTER TABLE `estoque_pecas_has_os`
  ADD PRIMARY KEY (`estoque_pecas_id`,`OS_id`,`OS_veiculo_id1`),
  ADD KEY `fk_estoque_pecas_has_OS_OS1` (`OS_id`);

--
-- Índices de tabela `estoque_pecas_has_os1`
--
ALTER TABLE `estoque_pecas_has_os1`
  ADD PRIMARY KEY (`estoque_pecas_id`,`OS_id`,`OS_veiculo_id1`),
  ADD KEY `fk_estoque_pecas_has_OS1_OS1` (`OS_id`);

--
-- Índices de tabela `financeiro`
--
ALTER TABLE `financeiro`
  ADD PRIMARY KEY (`id`,`OS_id`),
  ADD KEY `fk_Financeiro_OS1` (`OS_id`);

--
-- Índices de tabela `os`
--
ALTER TABLE `os`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_OS_veiculo1` (`veiculo_id1`),
  ADD KEY `fk_OS_clientes1` (`clientes_cpf`);

--
-- Índices de tabela `pecas`
--
ALTER TABLE `pecas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pecas_na_os`
--
ALTER TABLE `pecas_na_os`
  ADD PRIMARY KEY (`pecas_id`,`OS_id`),
  ADD KEY `fk_pecas_has_OS_OS1` (`OS_id`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`idservicos`);

--
-- Índices de tabela `servicos_has_os`
--
ALTER TABLE `servicos_has_os`
  ADD PRIMARY KEY (`servicos_idservicos`,`OS_id`),
  ADD KEY `fk_servicos_has_OS_OS1` (`OS_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `veiculo`
--
ALTER TABLE `veiculo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_veiculo_clientes1` (`clientes_cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `financeiro`
--
ALTER TABLE `financeiro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `os`
--
ALTER TABLE `os`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pecas`
--
ALTER TABLE `pecas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `veiculo`
--
ALTER TABLE `veiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `estoque_pecas_has_os`
--
ALTER TABLE `estoque_pecas_has_os`
  ADD CONSTRAINT `fk_estoque_pecas_has_OS_OS1` FOREIGN KEY (`OS_id`) REFERENCES `os` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_estoque_pecas_has_OS_estoque_pecas1` FOREIGN KEY (`estoque_pecas_id`) REFERENCES `pecas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `estoque_pecas_has_os1`
--
ALTER TABLE `estoque_pecas_has_os1`
  ADD CONSTRAINT `fk_estoque_pecas_has_OS1_OS1` FOREIGN KEY (`OS_id`) REFERENCES `os` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_estoque_pecas_has_OS1_estoque_pecas1` FOREIGN KEY (`estoque_pecas_id`) REFERENCES `pecas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `financeiro`
--
ALTER TABLE `financeiro`
  ADD CONSTRAINT `fk_Financeiro_OS1` FOREIGN KEY (`OS_id`) REFERENCES `os` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `os`
--
ALTER TABLE `os`
  ADD CONSTRAINT `fk_OS_clientes1` FOREIGN KEY (`clientes_cpf`) REFERENCES `clientes` (`cpf`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_OS_veiculo1` FOREIGN KEY (`veiculo_id1`) REFERENCES `veiculo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `pecas_na_os`
--
ALTER TABLE `pecas_na_os`
  ADD CONSTRAINT `fk_pecas_has_OS_OS1` FOREIGN KEY (`OS_id`) REFERENCES `os` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pecas_has_OS_pecas1` FOREIGN KEY (`pecas_id`) REFERENCES `pecas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `servicos_has_os`
--
ALTER TABLE `servicos_has_os`
  ADD CONSTRAINT `fk_servicos_has_OS_OS1` FOREIGN KEY (`OS_id`) REFERENCES `os` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_servicos_has_OS_servicos1` FOREIGN KEY (`servicos_idservicos`) REFERENCES `servicos` (`idservicos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `veiculo`
--
ALTER TABLE `veiculo`
  ADD CONSTRAINT `fk_veiculo_clientes1` FOREIGN KEY (`clientes_cpf`) REFERENCES `clientes` (`cpf`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
