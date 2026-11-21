
<?php

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/autenticacao.php';

$erro = '';

if (isset($_SESSION['usuario_id'])) {
    header('Location: usuarios/select.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($login === '' || $senha === '') {

        $erro = 'Preencha todos os campos.';

    } else {

        try {
            $sql = "SELECT id, nome, login, senha
                    FROM Usuario
                    WHERE login = :login
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['login' => $login]);

            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, $usuario['senha'])) {

                session_regenerate_id(true);

                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_login'] = $usuario['login'];

                header('Location: usuarios/select.php');
                exit;

            } else {

                $erro = 'Login ou senha inválidos.';

            }

        } catch (PDOException $e) {

            error_log('Erro durante a autenticação: ' . $e->getMessage());
            $erro = 'Não foi possível realizar o login. Tente novamente.';

        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Gerenciamento de Usuários</title>
</head>

<body>

    <main>

        <h1>Sistema de Gerenciamento de Usuários</h1>

        <h2>Acesso ao sistema</h2>

        <?php if ($erro !== ''): ?>
            <p role="alert">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">

            <div>
                <label for="login">Login:</label>
                <input
                    type="text"
                    id="login"
                    name="login"
                    required
                    autocomplete="username"
                >
            </div>

            <div>
                <label for="senha">Senha:</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit">Entrar</button>

        </form>

    </main>

</body>

</html>
