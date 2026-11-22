
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

-- =====================================================
-- DESAFIO EXTRA - SISTEMA DE AUDITORIA
-- =====================================================

-- Armazena o historico das operacoes realizadas em Usuario.
-- Nao possui chave estrangeira para preservar logs de exclusoes.
CREATE TABLE IF NOT EXISTS Log_Auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    acao VARCHAR(10) NOT NULL,
    dados_anteriores JSON NULL,
    dados_novos JSON NULL,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Remove somente as definicoes anteriores das triggers.
-- Os registros das tabelas permanecem preservados.
DROP TRIGGER IF EXISTS trg_usuario_after_insert;
DROP TRIGGER IF EXISTS trg_usuario_after_update;
DROP TRIGGER IF EXISTS trg_usuario_after_delete;

DELIMITER $$

CREATE TRIGGER trg_usuario_after_insert
AFTER INSERT ON Usuario
FOR EACH ROW
BEGIN
    INSERT INTO Log_Auditoria (
        usuario_id, acao, dados_anteriores, dados_novos
    )
    VALUES (
        NEW.id,
        'INSERT',
        NULL,
        JSON_OBJECT(
            'id', NEW.id,
            'nome', NEW.nome,
            'email', NEW.email,
            'login', NEW.login
        )
    );
END$$

CREATE TRIGGER trg_usuario_after_update
AFTER UPDATE ON Usuario
FOR EACH ROW
BEGIN
    INSERT INTO Log_Auditoria (
        usuario_id, acao, dados_anteriores, dados_novos
    )
    VALUES (
        NEW.id,
        'UPDATE',
        JSON_OBJECT(
            'id', OLD.id,
            'nome', OLD.nome,
            'email', OLD.email,
            'login', OLD.login
        ),
        JSON_OBJECT(
            'id', NEW.id,
            'nome', NEW.nome,
            'email', NEW.email,
            'login', NEW.login
        )
    );
END$$

CREATE TRIGGER trg_usuario_after_delete
AFTER DELETE ON Usuario
FOR EACH ROW
BEGIN
    INSERT INTO Log_Auditoria (
        usuario_id, acao, dados_anteriores, dados_novos
    )
    VALUES (
        OLD.id,
        'DELETE',
        JSON_OBJECT(
            'id', OLD.id,
            'nome', OLD.nome,
            'email', OLD.email,
            'login', OLD.login
        ),
        NULL
    );
END$$

DELIMITER ;

-- Consulta os registros de auditoria.
SELECT
    id,
    usuario_id,
    acao,
    dados_anteriores,
    dados_novos,
    data_hora
FROM Log_Auditoria
ORDER BY id DESC;
