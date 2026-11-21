
<?php

require_once __DIR__ . '/../includes/autenticacao.php';
require_once __DIR__ . '/../config/conexao.php';

verificarAutenticacao();

$erros = [];
$usuario = null;
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Valida o ID recebido.
if (!$id || $id <= 0) {
    header('Location: select.php');
    exit;
}

// Busca o usuário.
try {

    $sql = "SELECT id, nome, email, login
            FROM Usuario
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $usuario = $stmt->fetch();

    if (!$usuario) {
        header('Location: select.php');
        exit;
    }

} catch (PDOException $e) {

    error_log('Erro ao consultar usuário: ' . $e->getMessage());

    http_response_code(500);
    exit('Não foi possível carregar os dados do usuário.');

}

// Inicializa os campos com os dados atuais.
$nome = $usuario['nome'];
$email = $usuario['email'];
$login = $usuario['login'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Validação dos campos.
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

    // Validação da nova senha, caso informada.
    if ($senha !== '') {

        if (strlen($senha) < 8) {
            $erros[] = 'A senha deve possuir pelo menos 8 caracteres.';
        } elseif (
            !preg_match('/[a-zA-Z]/', $senha) ||
            !preg_match('/[0-9]/', $senha)
        ) {
            $erros[] = 'A senha deve conter pelo menos uma letra e um número.';
        }

    }

    if (empty($erros)) {

        try {

            // Verifica duplicidade, ignorando o próprio usuário.
            $sql = "SELECT id
                    FROM Usuario
                    WHERE (email = :email OR login = :login)
                    AND id != :id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'email' => $email,
                'login' => $login,
                'id' => $id
            ]);

            if ($stmt->fetch()) {

                $erros[] = 'O e-mail ou login informado já pertence a outro usuário.';

            } else {

                // Atualiza os dados básicos.
                if ($senha !== '') {

                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                    $sql = "UPDATE Usuario
                            SET nome = :nome,
                                email = :email,
                                login = :login,
                                senha = :senha
                            WHERE id = :id";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'login' => $login,
                        'senha' => $senhaHash,
                        'id' => $id
                    ]);

                } else {

                    $sql = "UPDATE Usuario
                            SET nome = :nome,
                                email = :email,
                                login = :login
                            WHERE id = :id";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'login' => $login,
                        'id' => $id
                    ]);

                }

                header('Location: select.php?edicao=sucesso');
                exit;

            }

        } catch (PDOException $e) {

            error_log('Erro ao atualizar usuário: ' . $e->getMessage());

            $erros[] = 'Não foi possível atualizar o usuário. Tente novamente.';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Usuário | Sistema de Gerenciamento</title>
</head>

<body>

    <main>

        <h1>Editar Usuário</h1>

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

                <label for="senha">Nova senha:</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    minlength="8"
                    autocomplete="new-password"
                >

                <small>
                    Deixe em branco para manter a senha atual.
                    Se informar uma nova senha, utilize pelo menos
                    8 caracteres, contendo letras e números.
                </small>

            </div>

            <button type="submit">Salvar alterações</button>

        </form>

        <p>
            <a href="select.php">Voltar à lista de usuários</a>
        </p>

    </main>

</body>

</html>
