-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mariadb:3306
-- Tempo de geração: 12/10/2026 às 21:11
-- Versão do servidor: 10.11.19-MariaDB-ubu2204
-- Versão do PHP: 8.3.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atalaia`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alarme`
--

CREATE TABLE `alarme` (
  `id` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `cor` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dados`
--

CREATE TABLE `dados` (
  `id` bigint(20) NOT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `id_sensor` int(11) NOT NULL,
  `temperatura` decimal(10,2) DEFAULT NULL,
  `umidade` decimal(10,2) DEFAULT NULL,
  `gas_co` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `id_sensor` int(11) DEFAULT NULL,
  `tipo_sensor` varchar(50) DEFAULT NULL,
  `evento` varchar(255) NOT NULL,
  `ocorrencia` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sensores`
--

CREATE TABLE `sensores` (
  `id` int(11) NOT NULL,
  `device_token` varchar(100) NOT NULL,
  `localizacao` varchar(100) NOT NULL,
  `chave_secreta` varchar(255) NOT NULL,
  `temperatura_max` decimal(10,2) DEFAULT NULL,
  `umidade_min` decimal(10,2) DEFAULT NULL,
  `umidade_max` decimal(10,2) DEFAULT NULL,
  `gas_max` decimal(10,2) DEFAULT NULL,
  `alarme_sonoro` tinyint(1) NOT NULL DEFAULT 0,
  `executar_script` tinyint(1) NOT NULL DEFAULT 0,
  `script_alarme` varchar(255) DEFAULT NULL,
  `offline` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sensores_evento`
--

CREATE TABLE `sensores_evento` (
  `id` int(11) NOT NULL,
  `id_sensor` int(11) DEFAULT NULL,
  `device_token` varchar(100) DEFAULT NULL,
  `canal` int(11) NOT NULL DEFAULT 0,
  `localizacao` varchar(100) NOT NULL,
  `nome_evento` varchar(100) NOT NULL,
  `nivel_evento` enum('informacao','atencao','critico') NOT NULL DEFAULT 'informacao',
  `estado` tinyint(1) NOT NULL DEFAULT 0,
  `estado_anterior` tinyint(1) NOT NULL DEFAULT 0,
  `data_hora` datetime DEFAULT NULL,
  `alarme_sonoro` tinyint(1) NOT NULL DEFAULT 0,
  `executar_script` tinyint(1) NOT NULL DEFAULT 0,
  `script_alarme` varchar(255) DEFAULT NULL,
  `offline` tinyint(1) NOT NULL DEFAULT 0,
  `ordem_eventos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alarme`
--
ALTER TABLE `alarme`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_alarme_sensor` (`id_sensor`);

--
-- Índices de tabela `dados`
--
ALTER TABLE `dados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dados_sensor` (`id_sensor`),
  ADD KEY `idx_dados_data_hora` (`data_hora`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_sensor` (`id_sensor`),
  ADD KEY `idx_logs_data_hora` (`data_hora`);

--
-- Índices de tabela `sensores`
--
ALTER TABLE `sensores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_device_token` (`device_token`);

--
-- Índices de tabela `sensores_evento`
--
ALTER TABLE `sensores_evento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evento_sensor` (`id_sensor`),
  ADD KEY `idx_evento_token` (`device_token`);

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
-- AUTO_INCREMENT de tabela `sensores_evento`
--
ALTER TABLE `sensores_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alarme`
--
ALTER TABLE `alarme`
  ADD CONSTRAINT `fk_alarme_sensor` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `dados`
--
ALTER TABLE `dados`
  ADD CONSTRAINT `fk_dados_sensor` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_sensor` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `sensores_evento`
--
ALTER TABLE `sensores_evento`
  ADD CONSTRAINT `fk_evento_sensor` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
