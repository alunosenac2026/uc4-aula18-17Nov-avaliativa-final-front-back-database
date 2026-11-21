
<?php

require_once __DIR__ . '/../includes/autenticacao.php';
require_once __DIR__ . '/../config/conexao.php';

verificarAutenticacao();

$erros = [];
$nome = '';
$email = '';
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Validação dos campos obrigatórios.
    if ($nome === '') {
        $erros[] = 'Informe o nome do usuário.';
    }

    if ($email === '') {
        $erros[] = 'Informe o e-mail do usuário.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um endereço de e-mail válido.';
    }

    if ($login === '') {
        $erros[] = 'Informe o login do usuário.';
    }

    // Validação da senha.
    if (strlen($senha) < 8) {
        $erros[] = 'A senha deve possuir pelo menos 8 caracteres.';
    } elseif (
        !preg_match('/[a-zA-Z]/', $senha) ||
        !preg_match('/[0-9]/', $senha)
    ) {
        $erros[] = 'A senha deve conter pelo menos uma letra e um número.';
    }

    // Verifica duplicidade e realiza o cadastro.
    if (empty($erros)) {

        try {

            $sql = "SELECT id
                    FROM Usuario
                    WHERE email = :email OR login = :login";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'email' => $email,
                'login' => $login
            ]);

            $usuarioExistente = $stmt->fetch();

            if ($usuarioExistente) {

                $erros[] = 'O e-mail ou login informado já está cadastrado.';

            } else {

                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $sql = "INSERT INTO Usuario (nome, email, login, senha)
                        VALUES (:nome, :email, :login, :senha)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'login' => $login,
                    'senha' => $senhaHash
                ]);

                header('Location: select.php?cadastro=sucesso');
                exit;

            }

        } catch (PDOException $e) {

            error_log('Erro ao cadastrar usuário: ' . $e->getMessage());

            $erros[] = 'Não foi possível cadastrar o usuário. Tente novamente.';

        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Usuário | Sistema de Gerenciamento</title>
</head>

<body>

    <main>

        <h1>Cadastrar Usuário</h1>

        <?php if (!empty($erros)): ?>

            <div role="alert">

                <?php foreach ($erros as $erro): ?>

                    <p>
                        <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div>

                <label for="nome">Nome:</label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"
                    maxlength="100"
                    required
                >

            </div>

            <div>

                <label for="email">E-mail:</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                    maxlength="150"
                    required
                >

            </div>

            <div>

                <label for="login">Login:</label>

                <input
                    type="text"
                    id="login"
                    name="login"
                    value="<?= htmlspecialchars($login, ENT_QUOTES, 'UTF-8') ?>"
                    maxlength="50"
                    required
                >

            </div>

            <div>

                <label for="senha">Senha:</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    minlength="8"
                    required
                    autocomplete="new-password"
                >

                <small>
                    Mínimo de 8 caracteres, contendo letras e números.
                </small>

            </div>

            <button type="submit">Cadastrar usuário</button>

        </form>

        <p>
            <a href="select.php">Voltar à lista de usuários</a>
        </p>

    </main>

</body>

</html>
