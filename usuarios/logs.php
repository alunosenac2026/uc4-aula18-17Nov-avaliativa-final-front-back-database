<?php

require_once __DIR__ . '/../includes/autenticacao.php';
require_once __DIR__ . '/../config/conexao.php';

verificarAutenticacao();

$logs = [];
$erro = '';

try {

    $sql = "SELECT
                id,
                usuario_id,
                acao,
                dados_anteriores,
                dados_novos,
                data_hora
            FROM Log_Auditoria
            ORDER BY id DESC";

    $stmt = $pdo->query($sql);
    $logs = $stmt->fetchAll();

} catch (PDOException $e) {

    error_log(
        'Erro ao consultar auditoria: ' . $e->getMessage()
    );

    $erro = 'Não foi possível carregar os registros de auditoria.';

}

function escaparLog($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

function formatarDadosLog(?string $json): string
{
    if ($json === null) {
        return '—';
    }

    $dados = json_decode($json, true);

    if (!is_array($dados)) {
        return 'Dados indisponíveis';
    }

    $campos = ['id', 'nome', 'email', 'login'];
    $linhas = [];

    foreach ($campos as $campo) {
        if (array_key_exists($campo, $dados)) {
            $linhas[] = ucfirst($campo) . ': ' . $dados[$campo];
        }
    }

    return implode("\n", $linhas);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Auditoria | Sistema de Gerenciamento</title>

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
                        <?= escaparLog($_SESSION['usuario_nome'] ?? '') ?>
                    </span>

                    <a
                        href="../logout.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Sair
                    </a>

                </div>

            </div>

            <?php require __DIR__ . '/../includes/navegacao.php'; ?>

        </div>

    </header>

    <main class="container sistema-conteudo">

        <section class="sistema-card">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

                <div>

                    <h2 class="sistema-titulo">
                        Relatório de auditoria
                    </h2>

                    <p class="sistema-subtitulo">
                        Histórico de cadastros, alterações e exclusões de usuários.
                    </p>

                </div>

                <a
                    href="select.php"
                    class="btn btn-outline-secondary"
                >
                    Voltar à listagem
                </a>

            </div>

            <?php if ($erro !== ''): ?>

                <div class="alert alert-danger" role="alert">
                    <?= escaparLog($erro) ?>
                </div>

            <?php elseif (empty($logs)): ?>

                <div class="alert alert-info" role="status">
                    Nenhum registro de auditoria encontrado.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table sistema-tabela align-middle">

                        <thead>

                            <tr>
                                <th scope="col">Evento</th>
                                <th scope="col">Usuário afetado</th>
                                <th scope="col">Ação</th>
                                <th scope="col">Data e hora</th>
                                <th scope="col">Dados anteriores</th>
                                <th scope="col">Dados novos</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($logs as $log): ?>

                                <tr>

                                    <td>
                                        <?= escaparLog($log['id']) ?>
                                    </td>

                                    <td>
                                        <?= escaparLog($log['usuario_id']) ?>
                                    </td>

                                    <td>

                                        <?php
                                        $classesAcao = [
                                            'INSERT' => 'text-bg-success',
                                            'UPDATE' => 'text-bg-primary',
                                            'DELETE' => 'text-bg-danger'
                                        ];

                                        $classe = $classesAcao[$log['acao']]
                                            ?? 'text-bg-secondary';
                                        ?>

                                        <span class="badge <?= $classe ?>">
                                            <?= escaparLog($log['acao']) ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?= escaparLog(
                                            date(
                                                'd/m/Y H:i:s',
                                                strtotime($log['data_hora'])
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <pre class="auditoria-dados"><?= escaparLog(
                                            formatarDadosLog($log['dados_anteriores'])
                                        ) ?></pre>
                                    </td>

                                    <td>
                                        <pre class="auditoria-dados"><?= escaparLog(
                                            formatarDadosLog($log['dados_novos'])
                                        ) ?></pre>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <p class="sistema-total">
                    Total de eventos: <?= count($logs) ?>
                </p>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
