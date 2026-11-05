
<?php
$filtro = $_GET["status"] ?? "Todas";

$falhas = [
    [
        "titulo" => "Velocidade acima do limite",
        "sensor" => "Sensor 001 - Linha Norte",
        "data" => "07/05/2026",
        "status" => "Não resolvida"
    ],
    [
        "titulo" => "Temperatura alta",
        "sensor" => "Sensor 256 - Linha Sul",
        "data" => "05/05/2026",
        "status" => "Resolvida"
    ],
    [
        "titulo" => "Leitura estável",
        "sensor" => "Sensor 111 - Linha Oeste",
        "data" => "22/05/2026",
        "status" => "Não resolvida"
    ]
];

if (isset($_GET["acao"]) && isset($_GET["id"])) {
    $id = (int) $_GET["id"];

    if (isset($falhas[$id])) {
        if ($_GET["acao"] == "resolver") {
            $falhas[$id]["status"] = "Resolvida";
        } elseif ($_GET["acao"] == "reabrir") {
            $falhas[$id]["status"] = "Não resolvida";
        }
    }
}

$resolvidas = 0;
$naoResolvidas = 0;

foreach ($falhas as $falha) {
    if ($falha["status"] == "Resolvida") {
        $resolvidas++;
    } else {
        $naoResolvidas++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento</title>
    <link rel="stylesheet" href="monitoramento.css">
</head>
<body>

</head>

<body class="pagina-monitoramento">

<div class="layout-monitoramento">

    <aside class="menu-lateral-monitoramento">

        <aside class="menu">



       <img class="logo-trem"
    src="https://img.icons8.com/ios/100/ffffff/train.png"
    alt="Logo TremTech">

  <p class="marca">SISTEMA TREMTECH</p>
     <a href="../../public/Tela_Home/home.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
  Home
</a>

<a class="ativo" href="../../public/Tela_Usuario/cadastrar_usuario.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
  Usuários
</a>

<a href="../../public/Tela_Sensores/cadastrar_sensores.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
  Sensores
</a>

<a href="../../public/Tela_Trens/cadastrar_trens.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>
  Trem
</a>

<a href="../../public/Tela_Rotas/cadastrar_rotas.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>
  Rota
</a>
<a href="../../public/Tela_Monitoramento/monitoramento.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>
  Monitoramento
</a>

<a href="../../public/Tela_Relatorios/relatorios.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>
  Relatórios
</a>


<a class="logout" href="../../logout.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png');"></span>
  Logout

  </a>
        </aside>

    </aside>





<main>
    <h1>Falhas operacionais</h1>

    <form method="GET">
        <label for="status">Status:</label>

        <select name="status" id="status" onchange="this.form.submit()">
            <option value="Todas" <?= $filtro == "Todas" ? "selected" : "" ?>>
                Todas
            </option>

            <option value="Resolvida" <?= $filtro == "Resolvida" ? "selected" : "" ?>>
                Resolvidas
            </option>

            <option value="Não resolvida" <?= $filtro == "Não resolvida" ? "selected" : "" ?>>
                Não resolvidas
            </option>
        </select>
    </form>

    <section>
        <h2><?= $resolvidas ?></h2>
        <p>Falhas resolvidas</p>

        <h2><?= $naoResolvidas ?></h2>
        <p>Falhas não resolvidas</p>
    </section>

    <section>
        <?php
        $encontrou = false;

        foreach ($falhas as $id => $falha):
            if (
                $filtro != "Todas" &&
                $falha["status"] != $filtro
            ) {
                continue;
            }

            $encontrou = true;
        ?>

            <article>
                <h3><?= htmlspecialchars($falha["titulo"]) ?></h3>

                <p><?= htmlspecialchars($falha["sensor"]) ?></p>

                <p>Data: <?= htmlspecialchars($falha["data"]) ?></p>

                <p>Status: <?= htmlspecialchars($falha["status"]) ?></p>

                <?php if ($falha["status"] == "Não resolvida"): ?>
                    <a href="?acao=resolver&id=<?= $id ?>&status=<?= urlencode($filtro) ?>">
                        Marcar como resolvida
                    </a>
                <?php else: ?>
                    <a href="?acao=reabrir&id=<?= $id ?>&status=<?= urlencode($filtro) ?>">
                        Reabrir falha
                    </a>
                <?php endif; ?>
            </article>

        <?php endforeach; ?>

        <?php if (!$encontrou): ?>
            <p>Nenhuma falha encontrada.</p>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
