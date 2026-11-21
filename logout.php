
<?php

session_start();

// Limpa todos os dados da sessão.
$_SESSION = [];

// Remove o cookie da sessão, se estiver habilitado.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destrói a sessão.
session_destroy();

// Retorna ao formulário de login.
header('Location: index.php');
exit;

