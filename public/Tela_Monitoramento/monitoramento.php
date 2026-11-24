<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_login();

$ehAdministrador = ($_SESSION['tipo'] === 'Administrador');
$mensagem = $_SESSION['mensagem_leitura'] ?? '';
unset($_SESSION['mensagem_leitura']);

$idVelocidade = (int) ($_GET['velocidade'] ?? 0);
$idTemperatura = (int) ($_GET['temperatura'] ?? 0);
$idRota = (int) ($_GET['rota'] ?? 0);
$filtro = 'Todas';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'Resolvida' || $_GET['status'] === 'Não resolvida') {
        $filtro = $_GET['status'];
    }
}

// Atualiza a situação da falha quando o administrador usa um botão da lista.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    if (isset($_POST['resolver']) || isset($_POST['reabrir'])) {
        $idFalha = (int) $_POST['id_falha'];

        if (isset($_POST['resolver'])) {
            $situacao = 'Resolvida';
        } else {
            $situacao = 'Não resolvida';
        }

        $sql = "UPDATE falhas_operacionais SET situacao = ? WHERE id_falha = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $situacao, $idFalha);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // urlencode prepara o texto da situação para ir na URL.
    header('Location: monitoramento.php?velocidade=' . $idVelocidade
        . '&temperatura=' . $idTemperatura . '&rota=' . $idRota
        . '&status=' . urlencode($filtro) . '#falhas');
    exit();
}

// Busca o sensor escolhido. Se ele não existir, usa o primeiro do tipo.
function buscarSensor($conn, $tipo, $id)
{
    $sql = "SELECT * FROM sensores WHERE tipo_dado = '$tipo' AND id_sensor = $id";
    $resultado = mysqli_query($conn, $sql);
    $sensor = mysqli_fetch_assoc($resultado);

    if (!$sensor) {
        $sql = "SELECT * FROM sensores WHERE tipo_dado = '$tipo'
                ORDER BY nome_sensor LIMIT 1";
        $resultado = mysqli_query($conn, $sql);
        $sensor = mysqli_fetch_assoc($resultado);
    }

    return $sensor;
}

// A rota guarda o ID do trem. Usamos esse ID para buscar o nome dele.
function buscarTremDaRota($conn, $idRota)
{
    $sql = "SELECT id_trem FROM rotas WHERE id_rotas = $idRota";
    $resultado = mysqli_query($conn, $sql);
    $rota = mysqli_fetch_assoc($resultado);

    if (!$rota) {
        return 'Trem não definido';
    }

    $idTrem = (int) $rota['id_trem'];
    $sql = "SELECT nome FROM trens WHERE id_trem = $idTrem";
    $resultado = mysqli_query($conn, $sql);
    $trem = mysqli_fetch_assoc($resultado);

    if (!$trem) {
        return 'Trem não definido';
    }

    return $trem['nome'];
}

// Preenche o cartão com o valor e a data da última leitura do sensor.
function mostrarLeitura($conn, $sensor, $unidade, $casasDecimais)
{
    $nome = '-';
    $valor = '- ' . $unidade;
    $data = '-';

    if ($sensor) {
        $idSensor = (int) $sensor['id_sensor'];
        $idRota = (int) $sensor['id_rotas'];
        $nome = $sensor['nome_sensor'] . ' - ' . buscarTremDaRota($conn, $idRota);

        // DESC e LIMIT 1 pegam só a leitura mais recente.
        // Se duas leituras tiverem a mesma data, vale o maior ID.
        $sql = "SELECT valor, data_leitura FROM leituras_sensores
                WHERE id_sensor = $idSensor
                ORDER BY data_leitura DESC, id_leitura DESC LIMIT 1";
        $resultado = mysqli_query($conn, $sql);
        $leitura = mysqli_fetch_assoc($resultado);

        if ($leitura) {
            $valor = number_format($leitura['valor'], $casasDecimais, '.', '') . ' ' . $unidade;
            $data = 'Leitura cadastrada em ' . date('d/m/Y', strtotime($leitura['data_leitura']));
        }
    }
    ?>
    <p><?php echo escapar($nome); ?></p>
    <strong><?php echo escapar($valor); ?></strong>
    <p><?php echo escapar($data); ?></p>
    <?php
}

$sensorVelocidade = buscarSensor($conn, 'Velocidade', $idVelocidade);
$sensorTemperatura = buscarSensor($conn, 'Temperatura', $idTemperatura);

if ($sensorVelocidade) {
    $idVelocidade = (int) $sensorVelocidade['id_sensor'];
}

if ($sensorTemperatura) {
    $idTemperatura = (int) $sensorTemperatura['id_sensor'];
}

$sql = "SELECT id_sensor, nome_sensor FROM sensores
        WHERE tipo_dado = 'Velocidade' ORDER BY nome_sensor";
$sensoresVelocidade = mysqli_query($conn, $sql);

$sql = "SELECT id_sensor, nome_sensor FROM sensores
        WHERE tipo_dado = 'Temperatura' ORDER BY nome_sensor";
$sensoresTemperatura = mysqli_query($conn, $sql);

$sql = "SELECT id_rotas, nome FROM rotas WHERE id_rotas = $idRota";
$resultado = mysqli_query($conn, $sql);
$rotaEscolhida = mysqli_fetch_assoc($resultado);

if (!$rotaEscolhida) {
    $resultado = mysqli_query($conn, "SELECT id_rotas, nome FROM rotas ORDER BY nome LIMIT 1");
    $rotaEscolhida = mysqli_fetch_assoc($resultado);
}

$nomeRota = 'Nenhuma rota cadastrada';

if ($rotaEscolhida) {
    $idRota = (int) $rotaEscolhida['id_rotas'];
    $nomeRota = $rotaEscolhida['nome'] . ' - ' . buscarTremDaRota($conn, $idRota);
}

$rotas = mysqli_query($conn, "SELECT id_rotas, nome FROM rotas ORDER BY nome");

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Resolvida'";
$totalResolvidas = mysqli_num_rows(mysqli_query($conn, $sql));

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Não resolvida'";
$totalNaoResolvidas = mysqli_num_rows(mysqli_query($conn, $sql));

$sql = "SELECT * FROM falhas_operacionais";

if ($filtro === 'Resolvida') {
    $sql .= " WHERE situacao = 'Resolvida'";
} elseif ($filtro === 'Não resolvida') {
    $sql .= " WHERE situacao = 'Não resolvida'";
}

$sql .= " ORDER BY data_registro DESC";
$falhas = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-topo-2">
</head>

<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user">
            <p class="painel-user">PAINEL DE MONITORAMENTO</p>
            <h1>Monitoramento ferroviário</h1>

            <div class="admin-badge-user">
                <div class="avatar-user"></div>
                <div>
                    <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($mensagem !== '') { ?>
                <p class="mensagem-sucesso" role="status"><?php echo escapar($mensagem); ?></p>
            <?php } ?>

            <!-- Os campos usam o mesmo formulário mesmo quando estão em cartões separados. -->
            <form method="GET" id="filtros-monitoramento"></form>

            <section class="monitoramento-cartoes">
                <div class="monitoramento-grupo-cartao">
                    <div class="monitoramento-filtro">
                        <label for="velocidade">Sensor de velocidade</label>
                        <select id="velocidade" name="velocidade" form="filtros-monitoramento">
                            <?php if (mysqli_num_rows($sensoresVelocidade) === 0) { ?>
                                <option value="0">Nenhum sensor de velocidade cadastrado</option>
                            <?php } ?>

                            <?php while ($sensor = mysqli_fetch_assoc($sensoresVelocidade)) { ?>
                                <option value="<?php echo $sensor['id_sensor']; ?>"
                                    <?php if ($idVelocidade == $sensor['id_sensor']) { echo 'selected'; } ?>>
                                    <?php echo escapar($sensor['nome_sensor']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <article class="monitoramento-cartao">
                        <h2>Velocidade atual</h2>
                        <?php mostrarLeitura($conn, $sensorVelocidade, 'km/h', 0); ?>
                    </article>
                </div>

                <div class="monitoramento-grupo-cartao">
                    <div class="monitoramento-filtro">
                        <label for="temperatura">Sensor de temperatura</label>
                        <select id="temperatura" name="temperatura" form="filtros-monitoramento">
                            <?php if (mysqli_num_rows($sensoresTemperatura) === 0) { ?>
                                <option value="0">Nenhum sensor de temperatura cadastrado</option>
                            <?php } ?>

                            <?php while ($sensor = mysqli_fetch_assoc($sensoresTemperatura)) { ?>
                                <option value="<?php echo $sensor['id_sensor']; ?>"
                                    <?php if ($idTemperatura == $sensor['id_sensor']) { echo 'selected'; } ?>>
                                    <?php echo escapar($sensor['nome_sensor']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <article class="monitoramento-cartao">
                        <h2>Temperatura atual</h2>
                        <?php mostrarLeitura($conn, $sensorTemperatura, '°C', 1); ?>
                    </article>
                </div>
            </section>

            <section class="monitoramento-localizacao">
                <h2>Localização</h2>

                <div class="monitoramento-filtro monitoramento-filtro-rota">
                    <label for="rota">Rota</label>
                    <select id="rota" name="rota" form="filtros-monitoramento">
                        <?php if (mysqli_num_rows($rotas) === 0) { ?>
                            <option value="0">Nenhuma rota cadastrada</option>
                        <?php } ?>

                        <?php while ($rota = mysqli_fetch_assoc($rotas)) { ?>
                            <option value="<?php echo $rota['id_rotas']; ?>"
                                <?php if ($idRota == $rota['id_rotas']) { echo 'selected'; } ?>>
                                <?php echo escapar($rota['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <p id="nomeRota"><?php echo escapar($nomeRota); ?></p>

                <!-- As linhas do SVG desenham o percurso e os desvios do mapa. -->
                <!-- Desenho do percurso que o JavaScript usa para mover o marcador. -->
                <svg class="mapa-trilhos" viewBox="0 0 760 330"
                     role="img" aria-label="Trajeto ferroviário com ponte e desvios">
                    <defs>
                        <pattern id="gradeMapa" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M40 0H0V40" fill="none" stroke="#d8d8d8" stroke-width="2"/>
                        </pattern>
                    </defs>

                    <rect width="760" height="330" fill="url(#gradeMapa)"/>
                    <polyline class="trilho" points="70,175 80,115 150,75 250,60 360,65 470,90 580,120 670,165 690,220 640,265 540,290 420,300 300,290 190,260 100,225 70,175"/>
                    <polyline class="trilho" points="70,175 140,185 250,165 360,160 470,175 545,200 640,230"/>
                    <polyline class="trilho" points="250,165 270,235 320,285"/>
                    <polyline class="trilho" points="360,160 420,130 480,130 580,120"/>
                    <polyline class="trilho" points="545,200 580,235 640,265"/>
                    <polyline class="trilho-ponte" points="250,60 360,65"/>
                    <text x="275" y="45" class="rotulo-ponte">Ponte</text>

                    <g id="marcadorTrem" transform="translate(70 175)">
                        <circle r="12" class="marcador-trem"/>
                        <circle r="4" class="marcador-centro"/>
                    </g>
                </svg>
            </section>

            <section class="home_caixa_falhas" id="falhas">
                <div class="falhas_cabecalho">
                    <h2 class="home_titulo_tabela">Falhas operacionais</h2>

                    <div class="falhas_filtro">
                        <label for="status">Status</label>
                        <select id="status" name="status" form="filtros-monitoramento">
                            <option value="Todas">Todas</option>
                            <option value="Resolvida"
                                <?php if ($filtro === 'Resolvida') { echo 'selected'; } ?>>Resolvidas</option>
                            <option value="Não resolvida"
                                <?php if ($filtro === 'Não resolvida') { echo 'selected'; } ?>>Não resolvidas</option>
                        </select>
                    </div>
                </div>

                <div class="falhas_resumo">
                    <div class="falha_contador">
                        <strong><?php echo $totalResolvidas; ?></strong>
                        <span>Resolvidas</span>
                    </div>
                    <div class="falha_contador">
                        <strong><?php echo $totalNaoResolvidas; ?></strong>
                        <span>Não resolvidas</span>
                    </div>
                </div>

                <div class="falhas_lista">
                    <?php if (mysqli_num_rows($falhas) === 0) { ?>
                        <p class="falhas_vazias">Nenhuma falha encontrada.</p>
                    <?php } ?>

                    <?php
                    while ($falha = mysqli_fetch_assoc($falhas)) {
                        $idSensor = (int) $falha['id_sensor'];
                        $sql = "SELECT nome_sensor, id_rotas FROM sensores WHERE id_sensor = $idSensor";
                        $resultado = mysqli_query($conn, $sql);
                        $sensor = mysqli_fetch_assoc($resultado);
                        $nomeTrem = buscarTremDaRota($conn, (int) $sensor['id_rotas']);
                    ?>
                        <article class="falha_item">
                            <div class="falha_dados">
                                <h3><?php echo escapar($falha['descricao']); ?></h3>
                                <p>
                                    Sensor: <?php echo escapar($sensor['nome_sensor']); ?>
                                    - Trem: <?php echo escapar($nomeTrem); ?>
                                    - <?php echo date('d/m/Y', strtotime($falha['data_registro'])); ?>
                                </p>
                            </div>

                            <?php if ($ehAdministrador) { ?>
                                <form method="POST" class="falha_acao">
                                    <input type="hidden" name="id_falha" value="<?php echo $falha['id_falha']; ?>">

                                    <?php if ($falha['situacao'] === 'Resolvida') { ?>
                                        <button type="submit" name="reabrir" class="botao_reabrir">Reabrir</button>
                                    <?php } else { ?>
                                        <button type="submit" name="resolver" class="botao_resolver">Marcar como resolvida</button>
                                    <?php } ?>
                                </form>
                            <?php } ?>
                        </article>
                    <?php } ?>
                </div>
            </section>

            <?php if ($ehAdministrador) { ?>
                <div class="acao-leitura-monitoramento">
                    <a class="botao-leitura-monitoramento" href="registrar_leitura.php">
                        Registrar leitura ou falha
                    </a>
                </div>
            <?php } ?>
        </main>
    </div>

<script src="../../script/validacao.js"></script>
</body>
</html>