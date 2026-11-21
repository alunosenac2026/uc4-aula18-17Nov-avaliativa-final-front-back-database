
<?php

require_once __DIR__ . '/includes/autenticacao.php';

// Limpa todos os dados armazenados na sessão.
$_SESSION = [];

// Remove o cookie da sessão, se estiver habilitado.
if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax'
        ]
    );
}

// Destrói a sessão.
session_destroy();

// Retorna ao formulário de login.
header('Location: index.php');
exit;
