
<?php
$filtro = $_GET["status"] ?? "Todas";
$falhas = [
    [
        
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
      
$resolvidas = 0;
$naoResolvidas = 0;

foreach ($falhas as $falha) {
    if ($falha["status"] == "Resolvida") {
        $resolvidas++;
    } else {
        $naoResolvidas++;
    }
}
  
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Monitoramento</title>
</head>
<body>
    <main>
        <h1>Falhas operacionais</h1>
        
<form method="GET">
    <label for="status">Status:</label>

    <select name="status" id="status" onchange="this.form.submit()">
        <option value="Todas">Todas</option>
        <option value="Resolvida">Resolvidas</option>
        <option value="Não resolvida">Não resolvidas</option>
        
<section>
    <h2><?= $resolvidas ?></h2>
    <p>Falhas resolvidas</p>

    <h2><?= $naoResolvidas ?></h2>
    <p>Falhas não resolvidas</p>
    
<section>
    <?php foreach ($falhas as $id => $falha): ?>

        <?php
        if (
            $filtro != "Todas" &&
            $falha["status"] != $filtro
        ) {
            continue;
        }
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


        <article>
            <h3><?= htmlspecialchars($falha["titulo"]) ?></h3>

            <p><?= htmlspecialchars($falha["sensor"]) ?></p>

            <p>Data: <?= htmlspecialchars($falha["data"]) ?></p>

            <p>Status: <?= htmlspecialchars($falha["status"]) ?></p>
        </article>

    <?php endforeach; ?>
    
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

</section>

    </select>
</form>

    </main>
</body>
</html>
