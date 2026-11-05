
<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_login();
$resultadoSensores = $conn->query("SELECT id_sensor, nome_sensor FROM sensores ORDER BY nome_sensor");

$filtro = $_GET["status"] ?? "Todas";
$sensorSelecionado = $_GET["sensor"] ?? "Sensor Norte";

$sensores = [
    "Sensor Norte" => [
        "velocidade" => "98 km/h",
        "temperatura" => "35º C",
        "status" => "Alerta",
        "mensagem" => "Velocidade acima da média"
    ],
    "Sensor Sul" => [
        "velocidade" => "75 km/h",
        "temperatura" => "29º C",
        "status" => "Normal",
        "mensagem" => "Funcionamento normal"
    ],
    "Sensor Leste" => [
        "velocidade" => "82 km/h",
        "temperatura" => "31º C",
        "status" => "Normal",
        "mensagem" => "Velocidade estável"
    ],
    "Sensor Oeste" => [
        "velocidade" => "105 km/h",
        "temperatura" => "37º C",
        "status" => "Alerta",
        "mensagem" => "Temperatura acima da média"
    ]
];
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

<main>

    <h1>Painel de Monitoramento</h1>

    <section>
        <h2>Monitoramento em tempo real</h2>

        <form method="GET">
            <label for="sensor">Sensor:</label>

         <select name="sensor" id="sensor" onchange="this.form.submit()">

    <?php while ($sensor = $resultadoSensores->fetch_assoc()): ?>

        <option
            value="<?= htmlspecialchars($sensor["nome_sensor"]) ?>"
            <?= $sensorSelecionado == $sensor["nome_sensor"] ? "selected" : "" ?>
        >
            <?= htmlspecialchars($sensor["nome_sensor"]) ?>
        </option>

    <?php endwhile; ?>

</select>  
        </form>
    </section>

    <section>
        <article>
            <h3>Velocidade atual</h3>

            <p><?= htmlspecialchars($sensorSelecionado) ?></p>

<p><?= $sensores[$sensorSelecionado]["velocidade"] ?></p>

<p>Status operacional: <?= $sensores[$sensorSelecionado]["status"] ?></p>

<p><?= $sensores[$sensorSelecionado]["mensagem"] ?></p>
        </article>

        <article>
            <h3>Temperatura atual</h3>

          <p><?= htmlspecialchars($sensorSelecionado) ?></p>

<p><?= $sensores[$sensorSelecionado]["temperatura"] ?></p>

<p>Status operacional: <?= $sensores[$sensorSelecionado]["status"] ?></p>

<p>Temperatura estável</p>
        </article>
    </section>

    <section>
        <h2>Localização</h2>

        <p><?= htmlspecialchars($sensorSelecionado) ?></p>
    </section>

    <section>
        <h2>Falhas operacionais</h2>

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
