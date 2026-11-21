<?php

$host = 'localhost';
$dbname = 'sistema_gerenciamento_usuarios';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {
    error_log('Erro na conexão com o banco de dados: ' . $e->getMessage());

    http_response_code(500);

    exit('Não foi possível conectar ao banco de dados.');
}