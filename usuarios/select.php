
<?php

require_once __DIR__ . '/../includes/autenticacao.php';

verificarAutenticacao();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel | Gerenciamento de Usuários</title>
</head>

<body>

    <main>

        <h1>Bem-vindo ao Sistema!</h1>

        <p>
            Olá,
            <?= htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8') ?>!
        </p>

        <p>Autenticação realizada com sucesso.</p>

        <p><a href="../logout.php">Sair do sistema</a></p>
        

    </main>

</body>

</html>
