-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS siscav
DEFAULT CHARACTER SET utf8mb4
DEFAULT COLLATE utf8mb4_unicode_ci;

-- Usar o banco
USE siscav;

-- Tabela sensores
CREATE TABLE sensores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    localizacao VARCHAR(255) NOT NULL
);

-- Tabela dados
CREATE TABLE dados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_sensor INT NOT NULL,
    temperatura FLOAT,
    umidade FLOAT,
    gas_co FLOAT,
    FOREIGN KEY (id_sensor) REFERENCES sensores(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Tabela alarme
CREATE TABLE alarme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sensor INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    cor VARCHAR(20),
    FOREIGN KEY (id_sensor) REFERENCES sensores(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Tabela logs
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_sensor INT NOT NULL,
    evento TEXT NOT NULL,
    FOREIGN KEY (id_sensor) REFERENCES sensores(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
