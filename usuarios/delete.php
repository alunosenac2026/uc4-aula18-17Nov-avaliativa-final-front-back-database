
<?php

require_once __DIR__ . '/../includes/autenticacao.php';
require_once __DIR__ . '/../config/conexao.php';

// Verifica se o usuário está autenticado.
verificarAutenticacao();

// Permite exclusões somente por POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método não permitido.');
}

// Valida o token de segurança.
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    !isset($_SESSION['csrf_exclusao']) ||
    !hash_equals($_SESSION['csrf_exclusao'], $token)
) {
    http_response_code(403);
    exit('Solicitação inválida.');
}

// Valida o ID do usuário.
$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    header('Location: select.php?exclusao=id_invalido');
    exit;
}

// Impede a exclusão da própria conta.
if ($id === (int) $_SESSION['usuario_id']) {
    header('Location: select.php?exclusao=propria_conta');
    exit;
}

try {

    // Exclui somente o usuário identificado.
    $sql = "DELETE FROM Usuario
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'id' => $id
    ]);

    if ($stmt->rowCount() === 0) {
        header('Location: select.php?exclusao=nao_encontrado');
        exit;
    }

    // Renova o token após uma exclusão bem-sucedida.
    $_SESSION['csrf_exclusao'] = bin2hex(random_bytes(32));

    header('Location: select.php?exclusao=sucesso');
    exit;

} catch (PDOException $e) {

    error_log(
        'Erro ao excluir usuário: ' . $e->getMessage()
    );

    header('Location: select.php?exclusao=erro');
    exit;

}
