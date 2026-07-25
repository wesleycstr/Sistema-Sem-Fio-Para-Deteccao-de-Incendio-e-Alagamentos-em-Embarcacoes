-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Tempo de geração: 25/07/2026 às 12:00
-- Versão do servidor: 10.5.29-MariaDB-ubu2004
-- Versão do PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `siscav`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alarme`
--

CREATE TABLE `alarme` (
  `id` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `status` int(11) DEFAULT 1,
  `ultima_atualizacao` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dados`
--

CREATE TABLE `dados` (
  `id` bigint(20) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `temperatura` float DEFAULT NULL,
  `umidade` float DEFAULT NULL,
  `gas_co` float DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL,
  `id_sensor` int(11) DEFAULT NULL,
  `evento` text NOT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sensores`
--

CREATE TABLE `sensores` (
  `id` int(11) NOT NULL,
  `localizacao` varchar(255) NOT NULL,
  `chave_secreta` varchar(255) NOT NULL,
  `temperatura_max` float DEFAULT 40,
  `umidade_min` float DEFAULT 20,
  `umidade_max` float DEFAULT 80,
  `gas_max` float DEFAULT 100,
  `alarme_sonoro` tinyint(1) DEFAULT 0,
  `offline` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `device_token` varchar(100) DEFAULT NULL,
  `executar_script` tinyint(1) NOT NULL DEFAULT 0,
  `script_alarme` varchar(500) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `ordem_cards` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tokens_recebidos`
--

CREATE TABLE `tokens_recebidos` (
  `id` bigint(20) NOT NULL,
  `token` varchar(255) NOT NULL,
  `timestamp_recebido` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alarme`
--
ALTER TABLE `alarme`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_sensor` (`id_sensor`);

--
-- Índices de tabela `dados`
--
ALTER TABLE `dados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dados_sensor` (`id_sensor`),
  ADD KEY `idx_dados_datahora` (`data_hora`),
  ADD KEY `idx_sensor_datahora` (`id_sensor`,`data_hora`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_sensor` (`id_sensor`),
  ADD KEY `idx_logs_data` (`data_hora`);

--
-- Índices de tabela `sensores`
--
ALTER TABLE `sensores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_token` (`device_token`);

--
-- Índices de tabela `tokens_recebidos`
--
ALTER TABLE `tokens_recebidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token` (`token`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alarme`
--
ALTER TABLE `alarme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dados`
--
ALTER TABLE `dados`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sensores`
--
ALTER TABLE `sensores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tokens_recebidos`
--
ALTER TABLE `tokens_recebidos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alarme`
--
ALTER TABLE `alarme`
  ADD CONSTRAINT `alarme_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `dados`
--
ALTER TABLE `dados`
  ADD CONSTRAINT `dados_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
