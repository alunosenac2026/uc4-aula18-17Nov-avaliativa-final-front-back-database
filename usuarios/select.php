
<?php

require_once __DIR__ . '/../includes/autenticacao.php';
require_once __DIR__ . '/../config/conexao.php';

// Verifica se o usuário está autenticado.
verificarAutenticacao();

// Inicializa as variáveis.
$usuarios = [];
$erro = '';

// Cria um token para proteger os formulários de exclusão.
if (empty($_SESSION['csrf_exclusao'])) {
    $_SESSION['csrf_exclusao'] = bin2hex(random_bytes(32));
}

// Consulta os usuários cadastrados.
try {

    $sql = "SELECT id, nome, email, login
            FROM Usuario
            ORDER BY nome ASC";

    $stmt = $pdo->query($sql);

    $usuarios = $stmt->fetchAll();

} catch (PDOException $e) {

    error_log(
        'Erro ao consultar usuários: ' . $e->getMessage()
    );

    $erro = 'Não foi possível carregar a lista de usuários.';

}

// Mensagens relacionadas à exclusão.
$mensagensExclusao = [
    'sucesso' => 'Usuário excluído com sucesso!',
    'propria_conta' => 'Você não pode excluir sua própria conta.',
    'id_invalido' => 'O usuário informado é inválido.',
    'nao_encontrado' => 'O usuário informado não foi encontrado.',
    'erro' => 'Não foi possível excluir o usuário.'
];

$exclusao = $_GET['exclusao'] ?? '';

$mensagemExclusao = '';

if (
    is_string($exclusao) &&
    isset($mensagensExclusao[$exclusao])
) {
    $mensagemExclusao = $mensagensExclusao[$exclusao];
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Usuários | Sistema de Gerenciamento</title>
</head>

<body>

    <main>

        <header>

            <h1>Gerenciamento de Usuários</h1>

            <p>
                Bem-vindo,
                <?= htmlspecialchars(
                    $_SESSION['usuario_nome'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>!
            </p>

            <nav>
                <a href="insert.php">Cadastrar usuário</a>
                |
                <a href="../logout.php">Sair do sistema</a>
            </nav>

        </header>

        <section>

            <h2>Usuários cadastrados</h2>

            <?php if (
                isset($_GET['cadastro']) &&
                $_GET['cadastro'] === 'sucesso'
            ): ?>

                <p role="status">
                    Usuário cadastrado com sucesso!
                </p>

            <?php endif; ?>

            <?php if (
                isset($_GET['edicao']) &&
                $_GET['edicao'] === 'sucesso'
            ): ?>

                <p role="status">
                    Usuário atualizado com sucesso!
                </p>

            <?php endif; ?>

            <?php if ($mensagemExclusao !== ''): ?>

                <p role="<?= $exclusao === 'sucesso'
                    ? 'status'
                    : 'alert' ?>">

                    <?= htmlspecialchars(
                        $mensagemExclusao,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </p>

            <?php endif; ?>

            <?php if ($erro !== ''): ?>

                <p role="alert">
                    <?= htmlspecialchars(
                        $erro,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php elseif (count($usuarios) === 0): ?>

                <p>Nenhum usuário cadastrado.</p>

            <?php else: ?>

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Login</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($usuarios as $usuario): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        (string) $usuario['id'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $usuario['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $usuario['email'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $usuario['login'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>

                                    <a href="update.php?id=<?= (int) $usuario['id'] ?>">
                                        Editar
                                    </a>

                                    <?php if (
                                        (int) $usuario['id'] !==
                                        (int) $_SESSION['usuario_id']
                                    ): ?>

                                        <form
                                            method="POST"
                                            action="delete.php"
                                            style="display: inline;"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não poderá ser desfeita.');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $usuario['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars(
                                                    $_SESSION['csrf_exclusao'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >

                                            <button type="submit">
                                                Excluir
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span>
                                            (Sua conta)
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>
