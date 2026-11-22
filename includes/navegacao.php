
<?php

$paginaAtual = basename($_SERVER['SCRIPT_NAME']);

?>

<nav class="sistema-navegacao" aria-label="Navegação principal">

    <a
        href="select.php"
        class="sistema-nav-link <?= $paginaAtual === 'select.php'
            ? 'ativo'
            : '' ?>"
        <?= $paginaAtual === 'select.php'
            ? 'aria-current="page"'
            : '' ?>
    >
        Listar usuários
    </a>

    <a
        href="insert.php"
        class="sistema-nav-link <?= $paginaAtual === 'insert.php'
            ? 'ativo'
            : '' ?>"
        <?= $paginaAtual === 'insert.php'
            ? 'aria-current="page"'
            : '' ?>
    >
        Cadastrar usuário
    </a>

</nav>
