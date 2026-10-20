<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - TremTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/style.css">
  </head>

<body>

<nav class="barra">
    <div class="menu">
        <a class="marca" href="home.php">🚆 SISTEMA TREMTECH</a>

        <a class="link-menu" href="home.php">Home</a>

        <a class="link-menu" href="../../public/Tela_Usuario/cadastrar_usuario.php">
            Usuários
        </a>

        <a class="link-menu" href="../../public/Tela_Sensores/cadastrar_sensores.php">
            Sensores
        </a>

        <a class="link-menu" href="../../public/Tela_Relatorios/relatorios.php">
            Relatórios
        </a>

        <div class="usuario">
            <strong><?= escapar($_SESSION['tipo'] ?? 'Usuário') ?></strong><br>
            <small><?= escapar($nome) ?></small>
        </div>
    </div>
</nav>

<main class="conteudo">

    <section class="boas-vindas">
        <p>Bem-vindo de volta!</p>
        <h1>Monitore usuários, trens e sensores em um só lugar.</h1>
    </section>

    <section class="indicadores">

        <div class="indicador">
            <div class="nome_indicador">Usuários cadastrados</div>
            <div class="numero"><?= (int) $totalUsuarios ?></div>
        </div>

        <div class="indicador">
            <div class="nome_indicador">Trens cadastrados</div>
            <div class="numero"><?= (int) $totalTrens ?></div>
        </div>

        <div class="indicador">
            <div class="nome_indicador">Sensores cadastrados</div>
            <div class="numero"><?= (int) $totalSensores ?></div>
        </div>

        <div class="indicador">
            <div class="nome_indicador">Velocidade média</div>
            <div class="numero">
                <?= number_format((float) $velocidadeMedia, 0, ',', '.') ?>
            </div>
            <small>km/h</small>
        </div>

    </section>