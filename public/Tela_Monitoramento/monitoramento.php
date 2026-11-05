
<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_login();
$ehAdministrador = strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;
$resultadoSensores = $conn->query("SELECT id_sensor, nome_sensor FROM sensores ORDER BY nome_sensor");

$filtro = $_GET["status"] ?? "Todas";
if (!in_array($filtro, ['Todas', 'Resolvida', 'Não resolvida'], true)) {
    $filtro = 'Todas';
}
$sensorSelecionado = $_GET["sensor"] ?? "Sensor Norte";


    $dadosSensor = [
    "velocidade" => "98 km/h",
    "temperatura" => "35º C",
    "status" => "Alerta",
    "mensagem" => "Velocidade acima da média"
];
    
$falhas = [];

$sql = "SELECT 
            f.id_falha,
            f.descricao,
            f.data_registro,
            f.resolvida,
            s.nome_sensor
        FROM falhas_operacionais f
        INNER JOIN sensores s ON f.id_sensor = s.id_sensor
        ORDER BY f.data_registro DESC";

$resultadoFalhas = $conn->query($sql);

while ($falha = $resultadoFalhas->fetch_assoc()) {

    $falhas[] = [
        "id" => $falha["id_falha"],
        "titulo" => $falha["descricao"],
        "sensor" => $falha["nome_sensor"],
        "data" => date("d/m/Y", strtotime($falha["data_registro"])),
        "status" => $falha["resolvida"] ? "Resolvida" : "Não resolvida"
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'], $_POST['id'])) {
    exigir_administrador();
    verificar_csrf();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $acao = $_POST['acao'];

    if ($id && $id > 0 && in_array($acao, ['resolver', 'reabrir'], true)) {
        $resolvida = $acao === 'resolver' ? 1 : 0;
        $stmt = $conn->prepare('UPDATE falhas_operacionais SET resolvida = ? WHERE id_falha = ?');
        if ($stmt) {
            $stmt->bind_param('ii', $resolvida, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: monitoramento.php?status=' . urlencode($filtro) . '&sensor=' . urlencode($sensorSelecionado));
    exit;
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
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261105-2">
</head>
<body class="body_geral">
<div class="pagina">
    <aside class="menu">
        <img class="logo-trem" src="https://img.icons8.com/ios/100/ffffff/train.png" alt="Logo TremTech">
        <p class="marca">SISTEMA TREMTECH</p>
        <a href="../Tela_Home/home.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>Home</a>
        <a href="../Tela_Usuario/cadastrar_usuario.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>Usuários</a>
        <a href="../Tela_Sensores/cadastrar_sensores.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>Sensores</a>
        <a href="../Tela_Trens/cadastrar_trens.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>Trens</a>
        <a href="../Tela_Rotas/cadastrar_rotas.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>Rotas</a>
        <a href="../Tela_Falhas/cadastrar_falha.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png');"></span>Falhas</a>
        <a class="ativo" href="monitoramento.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>Monitoramento</a>
        <a href="../Tela_Relatorios/relatorios.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>Relatórios</a>
        <a class="logout" href="../../logout.php"><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png');"></span>Logout</a>
    </aside>

<main class="content-user">

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
<p><?= $dadosSensor["velocidade"] ?></p>

<p>Status operacional: <?= $dadosSensor["status"] ?></p>

<p><?= $dadosSensor["mensagem"] ?></p>

        </article>

        <article>
            <h3>Temperatura atual</h3>

          <p><?= htmlspecialchars($sensorSelecionado) ?></p>

<p><?= $dadosSensor["temperatura"] ?></p>

<p>Status operacional: <?= $dadosSensor["status"] ?></p>

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
    </section>

    <section>
        <h2><?= $resolvidas ?></h2>
        <p>Falhas resolvidas</p>

        <h2><?= $naoResolvidas ?></h2>
        <p>Falhas não resolvidas</p>
    </section>

    <section>
        <?php
        $encontrou = false;

        foreach ($falhas as $falha):
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

                <?php if ($ehAdministrador): ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $falha['id'] ?>">
                        <?php if ($falha['status'] === 'Não resolvida'): ?>
                            <button type="submit" name="acao" value="resolver">Marcar como resolvida</button>
                        <?php else: ?>
                            <button type="submit" name="acao" value="reabrir">Reabrir falha</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </article>

        <?php endforeach; ?>

        <?php if (!$encontrou): ?>
            <p>Nenhuma falha encontrada.</p>
        <?php endif; ?>
    </section>

</main>
</div>

</body>
</html>
