
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

            error_log(
                'Erro durante a autenticação: ' . $e->getMessage()
            );

            $erro = 'Não foi possível realizar o login. Tente novamente.';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | Sistema de Gerenciamento de Usuários</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <main class="pagina-login">

        <div class="login-container">

            <header class="login-identidade">

                <h1>Sistema de Gerenciamento de Usuários</h1>

                <p>
                    Controle de acesso e gerenciamento de usuários
                </p>

            </header>

            <section class="login-card" aria-labelledby="titulo-login">

                <h2 id="titulo-login">Bem-vindo de volta</h2>

                <p class="login-descricao">
                    Informe suas credenciais para acessar o sistema.
                </p>

                <?php if ($erro !== ''): ?>

                    <div class="alert alert-danger" role="alert">

                        <?= htmlspecialchars(
                            $erro,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                <?php endif; ?>

                <form method="POST" action="">

                    <div class="mb-3">

                        <label for="login" class="form-label">
                            Login
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="login"
                            name="login"
                            placeholder="Digite seu login"
                            required
                            autocomplete="username"
                            autofocus
                        >

                    </div>

                    <div class="mb-4">

                        <label for="senha" class="form-label">
                            Senha
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="senha"
                            name="senha"
                            placeholder="Digite sua senha"
                            required
                            autocomplete="current-password"
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primario w-100"
                    >
                        Entrar no sistema
                    </button>

                </form>

            </section>

            <footer class="login-rodape">

                <p>
                    Acesso restrito a usuários autenticados.
                </p>

            </footer>

        </div>

    </main>

</body>

</html>
