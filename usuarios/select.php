
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

        <section class="sistema-card">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

                <div>

                    <h2 class="sistema-titulo">
                        Usuários cadastrados
                    </h2>

                    <p class="sistema-subtitulo">
                        Gerencie os usuários registrados no sistema.
                    </p>

                </div>

                <a
                    href="insert.php"
                    class="btn btn-primario px-4"
                >
                    + Novo usuário
                </a>

            </div>

            <?php if (
                isset($_GET['cadastro']) &&
                $_GET['cadastro'] === 'sucesso'
            ): ?>

                <div class="alert alert-success" role="status">
                    Usuário cadastrado com sucesso!
                </div>

            <?php endif; ?>

            <?php if (
                isset($_GET['edicao']) &&
                $_GET['edicao'] === 'sucesso'
            ): ?>

                <div class="alert alert-success" role="status">
                    Usuário atualizado com sucesso!
                </div>

            <?php endif; ?>

            <?php if ($mensagemExclusao !== ''): ?>

                <div
                    class="alert <?= $exclusao === 'sucesso'
                        ? 'alert-success'
                        : 'alert-danger' ?>"
                    role="<?= $exclusao === 'sucesso'
                        ? 'status'
                        : 'alert' ?>"
                >

                    <?= htmlspecialchars(
                        $mensagemExclusao,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>

            <?php if ($erro !== ''): ?>

                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars(
                        $erro,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php elseif (count($usuarios) === 0): ?>

                <div class="alert alert-info">
                    Nenhum usuário cadastrado.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle sistema-tabela">

                        <thead>

                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nome</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Login</th>
                                <th scope="col">Ações</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($usuarios as $usuario): ?>

                                <tr>

                                    <td>
                                        <?= (int) $usuario['id'] ?>
                                    </td>

                                    <td class="fw-semibold">

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

                                        <div class="d-flex flex-wrap align-items-center gap-2">

                                            <a
                                                href="update.php?id=<?= (int) $usuario['id'] ?>"
                                                class="btn btn-outline-primary btn-sm"
                                            >
                                                Editar
                                            </a>

                                            <?php if (
                                                (int) $usuario['id'] !==
                                                (int) $_SESSION['usuario_id']
                                            ): ?>

                                                <form
                                                    method="POST"
                                                    action="delete.php"
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

                                                    <button
                                                        type="submit"
                                                        class="btn btn-outline-danger btn-sm"
                                                    >
                                                        Excluir
                                                    </button>

                                                </form>

                                            <?php else: ?>

                                                <span class="badge text-bg-secondary">
                                                    Sua conta
                                                </span>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <p class="sistema-total">
                    Total de usuários: <?= count($usuarios) ?>
                </p>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>
