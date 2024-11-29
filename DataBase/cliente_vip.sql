-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 08/11/2024 às 20:50
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
-- Banco de dados: `cliente_vip`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `campanha`
--

CREATE TABLE `campanha` (
  `id_campanha` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `inicio` date NOT NULL,
  `termino` date NOT NULL,
  `status` varchar(200) NOT NULL,
  `orcamento` decimal(8,2) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `INTERESSE_id_interesse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `nome` varchar(200) NOT NULL,
  `telefone` varchar(12) NOT NULL,
  `email` varchar(200) NOT NULL,
  `endereco` varchar(200) NOT NULL,
  `idade` int(11) NOT NULL,
  `cpf` varchar(12) NOT NULL,
  `genero` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome`, `telefone`, `email`, `endereco`, `idade`, `cpf`, `genero`, `senha`) VALUES
(1, 'Erick Beran Ferreira M', '17991508653', 'reccojp65@gmail.com', 'Rua das Palmeiras', 16, '45264541841', 'M', '$2y$10$K2CApJdsv6dAZbkSfDCAoeqt7myjv485LONvl74M7v1VHssZeAYDG'),
(2, 'João Pedro', '17991508653', 'reccojp65@gmail.com', 'Rua das Palmeiras', 16, '12345678909', 'Masculino', '$2y$10$T4fTlaBGL7Wsl8f1jB2WaOxQciLQsd3n/OetVp0S2dT84lo6dLzny'),
(3, 'Juan Roberto Costa', '17989891234', 'juan@teste.com', 'Rua dos Estudantes, 321', 24, '31095703510', 'Masculino', '$2y$10$YRgWd3mlRTU1H8o299lHourjEgJmlRekUrIaEBLZLjPrb6KXKvkN.'),
(4, 'Rafael', '1234', 'asd@gmail.com', 'votuporanga', 123, '46610527067', 'Masculino', '$2y$10$2Xibk5xoeprT/oP0CXmFuOWScEXQ02iZ0pPdqg.9gyl9pigyNp5Hm');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contato_cliente`
--

CREATE TABLE `contato_cliente` (
  `id_relacao` int(11) NOT NULL,
  `data_contato` date NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `assunto` varchar(150) NOT NULL,
  `id_interesse` int(11) NOT NULL,
  `id_campanha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `empresa_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `setor` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `empresas`
--

INSERT INTO `empresas` (`empresa_id`, `nome`, `setor`, `endereco`, `cidade`, `estado`, `cep`, `telefone`, `email`, `data_criacao`) VALUES
(1, 'Data Den', 'Tecnologia', 'Pintópolis Rua do Cacete 6969', 'Votuporanga', 'São Paulo ', '15520-000', '1799150865', 'godobertoocaramartinez@gmail.com', '2024-10-25 14:02:15'),
(2, 'Leing', 'Transporte', 'Rua das Palmeiras', 'Votuporanga', 'São Paulo ', '15520-000', '17991508653', 'penis.imensodasilva@gmail.com', '2024-10-25 14:26:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `interesse`
--

CREATE TABLE `interesse` (
  `id_interesse` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `empresa` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `cargo` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `leads`
--

INSERT INTO `leads` (`id`, `nome`, `email`, `empresa`, `mensagem`, `data_criacao`, `cargo`, `telefone`, `status`) VALUES
(7, 'Erick Beran', 'reccojp65@gmail.com', 'Data den', 'Navio do erick', '2024-10-25 16:43:05', 'Membro', '17991508653', 'Completadas');

-- --------------------------------------------------------

--
-- Estrutura para tabela `negocios`
--

CREATE TABLE `negocios` (
  `id_negocio` int(11) NOT NULL,
  `data_negocio` date NOT NULL,
  `valor_transacao` decimal(10,2) NOT NULL,
  `notas` text DEFAULT NULL,
  `status` enum('Aberto','Em Progresso','Fechado') NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `negocios`
--

INSERT INTO `negocios` (`id_negocio`, `data_negocio`, `valor_transacao`, `notas`, `status`, `titulo`, `descricao`, `empresa_id`) VALUES
(6, '2024-04-23', 2500.00, NULL, 'Fechado', 'Gato net', 'Olha brasileiros', 1),
(7, '2024-04-23', 1700.00, NULL, 'Em Progresso', 'Gato net2', 'Olha brasileiros', 1),
(8, '2024-04-23', 12313.00, NULL, 'Aberto', 'Gato net3', 'Olha brasileiros', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas`
--

CREATE TABLE `tarefas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('Pendente','Em Progresso','Concluída') NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `prioridade` enum('Alta','Média','Baixa') DEFAULT 'Média',
  `data_termino` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `campanha`
--
ALTER TABLE `campanha`
  ADD PRIMARY KEY (`id_campanha`),
  ADD KEY `fk_CAMPANHA_INTERESSE1_idx` (`INTERESSE_id_interesse`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `unique_cpf` (`cpf`);

--
-- Índices de tabela `contato_cliente`
--
ALTER TABLE `contato_cliente`
  ADD PRIMARY KEY (`id_relacao`),
  ADD KEY `fk_RELACAO_CLIENTE1_idx` (`id_cliente`),
  ADD KEY `fk_RELACAO_INTERESSE1_idx` (`id_interesse`),
  ADD KEY `fk_CONTATO_CLIENTE_CAMPANHA1_idx` (`id_campanha`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`empresa_id`);

--
-- Índices de tabela `interesse`
--
ALTER TABLE `interesse`
  ADD PRIMARY KEY (`id_interesse`);

--
-- Índices de tabela `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `negocios`
--
ALTER TABLE `negocios`
  ADD PRIMARY KEY (`id_negocio`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Índices de tabela `tarefas`
--
ALTER TABLE `tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `campanha`
--
ALTER TABLE `campanha`
  MODIFY `id_campanha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `contato_cliente`
--
ALTER TABLE `contato_cliente`
  MODIFY `id_relacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `interesse`
--
ALTER TABLE `interesse`
  MODIFY `id_interesse` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `negocios`
--
ALTER TABLE `negocios`
  MODIFY `id_negocio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `tarefas`
--
ALTER TABLE `tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `campanha`
--
ALTER TABLE `campanha`
  ADD CONSTRAINT `fk_CAMPANHA_INTERESSE1` FOREIGN KEY (`INTERESSE_id_interesse`) REFERENCES `interesse` (`id_interesse`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `contato_cliente`
--
ALTER TABLE `contato_cliente`
  ADD CONSTRAINT `fk_CONTATO_CLIENTE_CAMPANHA1` FOREIGN KEY (`id_campanha`) REFERENCES `campanha` (`id_campanha`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_RELACAO_CLIENTE1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_RELACAO_INTERESSE1` FOREIGN KEY (`id_interesse`) REFERENCES `interesse` (`id_interesse`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Restrições para tabelas `negocios`
--
ALTER TABLE `negocios`
  ADD CONSTRAINT `negocios_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`);

--
-- Restrições para tabelas `tarefas`
--
ALTER TABLE `tarefas`
  ADD CONSTRAINT `tarefas_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
