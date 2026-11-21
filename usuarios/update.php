

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

                // Atualiza os dados, incluindo a senha somente se informada.
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

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="pagina-sistema">

    <header class="sistema-cabecalho">

        <div class="container">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div class="sistema-marca">

                    <h1>Gerenciamento de Usuários</h1>
                    <p>Painel administrativo</p>

                </div>

                <div class="d-flex align-items-center gap-3">

                    <span class="sistema-usuario">
                        <?= htmlspecialchars(
                            $_SESSION['usuario_nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <a
                        href="../logout.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Sair
                    </a>

                </div>

            </div>

        </div>

    </header>

    <main class="container sistema-conteudo">

        <section class="sistema-card sistema-formulario">

            <div class="mb-4">

                <h2 class="sistema-titulo">
                    Editar usuário
                </h2>

                <p class="sistema-subtitulo">
                    Atualize as informações do usuário selecionado.
                </p>

            </div>

            <?php if (!empty($erros)): ?>

                <div class="alert alert-danger" role="alert">

                    <?php foreach ($erros as $erro): ?>

                        <p class="mb-1">
                            <?= htmlspecialchars(
                                $erro,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <form method="POST" action="">

                <div class="mb-3">

                    <label for="nome" class="form-label">
                        Nome completo
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars(
                            $nome,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="Nome do usuário"
                        maxlength="100"
                        autocomplete="name"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label for="email" class="form-label">
                        E-mail
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="usuario@exemplo.com"
                        maxlength="150"
                        autocomplete="email"
                        required
                    >

                </div>

                <div class="row g-3 mb-3">

                    <div class="col-md-6">

                        <label for="login" class="form-label">
                            Login
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="login"
                            name="login"
                            value="<?= htmlspecialchars(
                                $login,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            placeholder="Login de acesso"
                            maxlength="50"
                            autocomplete="off"
                            required
                        >

                    </div>

                    <div class="col-md-6">

                        <label for="senha" class="form-label">
                            Nova senha
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="senha"
                            name="senha"
                            placeholder="Opcional"
                            minlength="8"
                            autocomplete="new-password"
                            aria-describedby="ajuda-senha"
                        >

                    </div>

                </div>

                <p id="ajuda-senha" class="form-text mb-4">
                    Deixe o campo de senha vazio para manter a senha atual.
                    Caso deseje alterá-la, utilize pelo menos 8 caracteres,
                    contendo letras e números.
                </p>

                <div class="d-flex flex-wrap justify-content-end gap-2">

                    <a
                        href="select.php"
                        class="btn btn-outline-secondary px-4"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primario px-4"
                    >
                        Salvar alterações
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>

