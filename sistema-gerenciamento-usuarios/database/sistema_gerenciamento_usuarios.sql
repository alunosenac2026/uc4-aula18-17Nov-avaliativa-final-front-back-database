
-- =====================================================
-- PROJETO FINAL UC4
-- SISTEMA DE GERENCIAMENTO DE USUARIOS
-- =====================================================

-- Cria o banco de dados caso ainda não exista.
CREATE DATABASE IF NOT EXISTS sistema_gerenciamento_usuarios
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sistema_gerenciamento_usuarios;

-- Cria a tabela de usuários caso ainda não exista.
CREATE TABLE IF NOT EXISTS Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Cadastra o administrador inicial somente se
-- ainda não existir usuário com o e-mail ou login reservado.
INSERT INTO Usuario (nome, email, login, senha)
SELECT
    'Administrador',
    'admin@sistema.local',
    'admin',
    '$2y$10$j2c/nbxzGEVw87rwdj2Ba.bvMOnRZHKNyVwCuf6Y8BpWrF.2w29YO'
WHERE NOT EXISTS (
    SELECT 1
    FROM Usuario
    WHERE email = 'admin@sistema.local'
       OR login = 'admin'
);

-- Exibe a estrutura da tabela.
DESCRIBE Usuario;

-- Lista os usuários sem expor os hashes das senhas.
SELECT id, nome, email, login
FROM Usuario
ORDER BY id ASC;
