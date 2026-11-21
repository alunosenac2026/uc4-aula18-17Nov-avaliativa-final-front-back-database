
-- =====================================================
-- PROJETO FINAL UC4 - SISTEMA DE GERENCIAMENTO DE USUARIOS
-- =====================================================

CREATE DATABASE IF NOT EXISTS sistema_gerenciamento_usuarios
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sistema_gerenciamento_usuarios;

CREATE TABLE IF NOT EXISTS Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

DESCRIBE Usuario;


USE sistema_gerenciamento_usuarios;

INSERT INTO Usuario (nome, email, login, senha)
VALUES (
    'Administrador',
    'admin@sistema.local',
    'admin',
    '$2y$10$j2c/nbxzGEVw87rwdj2Ba.bvMOnRZHKNyVwCuf6Y8BpWrF.2w29YO'
);

SELECT id, nome, email, login, senha
FROM Usuario;