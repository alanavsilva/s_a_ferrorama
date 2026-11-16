
<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_login();

$ehAdministrador =
    strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$filtro = $_GET['status'] ?? 'Todas';

if (!in_array($filtro, ['Todas', 'Resolvida', 'Não resolvida'], true)) {
    $filtro = 'Todas';
}

$velocidadeSelecionada = filter_input(INPUT_GET, 'velocidade', FILTER_VALIDATE_INT) ?: 0;
$temperaturaSelecionada = filter_input(INPUT_GET, 'temperatura', FILTER_VALIDATE_INT) ?: 0;
$rotaSelecionada = filter_input(INPUT_GET, 'rota', FILTER_VALIDATE_INT) ?: 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_falha'])) {
    exigir_administrador();
    verificar_csrf();

    $idFalha = filter_input(INPUT_POST, 'id_falha', FILTER_VALIDATE_INT);
    $acaoFalha = $_POST['acao_falha'];

    if (
        $idFalha && $idFalha > 0
        && in_array($acaoFalha, ['resolver', 'reabrir'], true)
    ) {
        $resolvida = $acaoFalha === 'resolver' ? 1 : 0;

        $stmt = $conn->prepare(
            'UPDATE falhas_operacionais
             SET resolvida = ?
             WHERE id_falha = ?'
        );

        if ($stmt) {
            $stmt->bind_param('ii', $resolvida, $idFalha);
            $stmt->execute();
            $stmt->close();
        }
    }

    $velocidadeRetorno = filter_input(INPUT_POST, 'velocidade', FILTER_VALIDATE_INT) ?: 0;
    $temperaturaRetorno = filter_input(INPUT_POST, 'temperatura', FILTER_VALIDATE_INT) ?: 0;
    $rotaRetorno = filter_input(INPUT_POST, 'rota', FILTER_VALIDATE_INT) ?: 0;

    header(
        'Location: monitoramento.php?status='
        . urlencode($filtro)
        . '&velocidade=' . $velocidadeRetorno
        . '&temperatura=' . $temperaturaRetorno
        . '&rota=' . $rotaRetorno
    );
    exit;
}

$carregarSensores = static function (mysqli $conn, string $tipo): array {
    $stmt = $conn->prepare('SELECT s.id_sensor, s.nome_sensor, r.nome AS nome_rota, t.nome AS nome_trem, t.velocidade_media FROM sensores s LEFT JOIN rotas r ON r.id_rotas = s.id_rotas LEFT JOIN trens t ON t.id_trem = r.id_trem WHERE s.tipo_dado = ? ORDER BY s.nome_sensor');
    if (!$stmt) return [];
    $stmt->bind_param('s', $tipo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $lista = [];
    while ($linha = $resultado->fetch_assoc()) $lista[] = $linha;
    $stmt->close();
    return $lista;
};

$sensoresVelocidade = $carregarSensores($conn, 'Velocidade');
$sensoresTemperatura = $carregarSensores($conn, 'Temperatura');
foreach ($sensoresTemperatura as $indice => &$sensorTemperatura) {
    $sensorTemperatura['temperatura'] = 35 + ($indice % 4);
}
unset($sensorTemperatura);

$resultadoRotas = $conn->query('SELECT r.id_rotas, r.nome, t.nome AS nome_trem FROM rotas r LEFT JOIN trens t ON t.id_trem = r.id_trem ORDER BY r.nome');
$rotasMonitoramento = [];
if ($resultadoRotas) {
    while ($rota = $resultadoRotas->fetch_assoc()) $rotasMonitoramento[] = $rota;
}

$validarSelecao = static function (array $lista, int $selecionado, string $campo): int {
    foreach ($lista as $linha) {
        if ((int) $linha[$campo] === $selecionado) return $selecionado;
    }
    return isset($lista[0][$campo]) ? (int) $lista[0][$campo] : 0;
};
$velocidadeSelecionada = $validarSelecao($sensoresVelocidade, $velocidadeSelecionada, 'id_sensor');
$temperaturaSelecionada = $validarSelecao($sensoresTemperatura, $temperaturaSelecionada, 'id_sensor');
$rotaSelecionada = $validarSelecao($rotasMonitoramento, $rotaSelecionada, 'id_rotas');

$totalResolvidas = (int)(
    $conn->query(
        'SELECT COUNT(*) AS total
         FROM falhas_operacionais
         WHERE resolvida = 1'
    )->fetch_assoc()['total'] ?? 0
);

$totalNaoResolvidas = (int)(
    $conn->query(
        'SELECT COUNT(*) AS total
         FROM falhas_operacionais
         WHERE resolvida = 0'
    )->fetch_assoc()['total'] ?? 0
);

$sqlFalhas = '
    SELECT
        f.id_falha,
        f.descricao,
        f.data_registro,
        f.resolvida,
        s.nome_sensor,
        t.nome AS nome_trem
    FROM falhas_operacionais AS f
    LEFT JOIN sensores AS s ON s.id_sensor = f.id_sensor
    LEFT JOIN rotas AS r ON r.id_rotas = s.id_rotas
    LEFT JOIN trens AS t ON t.id_trem = r.id_trem
';

if ($filtro === 'Resolvida') {
    $sqlFalhas .= ' WHERE f.resolvida = 1';
} elseif ($filtro === 'Não resolvida') {
    $sqlFalhas .= ' WHERE f.resolvida = 0';
}

$sqlFalhas .= ' ORDER BY f.data_registro DESC';
$resultadoFalhas = $conn->query($sqlFalhas);

$falhas = [];

if ($resultadoFalhas) {
    while ($falha = $resultadoFalhas->fetch_assoc()) {
        $falhas[] = $falha;
    }
} else {
    error_log('Erro ao carregar falhas no monitoramento: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261115-ui">
</head>

<body class="body_geral">
   <div class="pagina">

    <aside class="menu">
       <img class="logo-trem"
    src="https://img.icons8.com/ios/100/701015/train.png"
    alt="Logo TremTech">

  <p class="marca">SISTEMA TREMTECH</p>
     <a href="../../public/Tela_Home/home.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
  Home
</a>

<a href="../../public/Tela_Usuario/cadastrar_usuario.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
  Usuários
</a>

<a href="../../public/Tela_Sensores/cadastrar_sensores.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
  Sensores
</a>

<a href="../../public/Tela_Trens/cadastrar_trens.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/train.png');"></span>
  Trens
</a>

<a href="../../public/Tela_Rotas/cadastrar_rotas.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/map.png');"></span>
  Rotas
</a>
<a class="ativo"href="../../public/Tela_Monitoramento/monitoramento.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/visible.png');"></span>
  Monitoramento
</a>
<a href="../../public/Tela_Falhas/cadastrar_falha.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png');"></span>
  Falhas
</a>

<a href="../../public/Tela_Relatorios/relatorios.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/report-card.png');"></span>
  Relatórios
</a>


        <a class="logout" href="../../logout.php" data-confirmar-logout><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');"></span>Logout</a>
    </aside>

    <main class="content-user">
        <p class="painel-user">PAINEL DE MONITORAMENTO</p>
        <h1>Monitoramento ferroviário</h1>
       <div class="admin-badge-user">

            <div class="avatar-user"></div>

            <div>
                <strong>
                    <?= $ehAdministrador ? 'Administrador' : 'Funcionário' ?>
                </strong>

                <span>
                    <?= escapar($_SESSION['nome'] ?? '') ?>
                </span>
            </div>

        </div>

        <section class="monitoramento-cartoes">
            <div class="monitoramento-grupo-cartao">
                <form method="GET" class="monitoramento-filtro">
                    <label for="velocidade">Sensor de velocidade</label>
                    <select id="velocidade" name="velocidade" onchange="this.form.submit()">
                        <?php if (empty($sensoresVelocidade)): ?><option value="0">Nenhum sensor de velocidade cadastrado</option><?php endif; ?>
                        <?php foreach ($sensoresVelocidade as $sensor): ?>
                            <option value="<?= (int)$sensor['id_sensor'] ?>" <?= $velocidadeSelecionada === (int)$sensor['id_sensor'] ? 'selected' : '' ?>><?= escapar($sensor['nome_sensor']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="temperatura" value="<?= (int)$temperaturaSelecionada ?>">
                    <input type="hidden" name="rota" value="<?= (int)$rotaSelecionada ?>">
                    <input type="hidden" name="status" value="<?= escapar($filtro) ?>">
                </form>
            <article class="monitoramento-cartao">
                <h2>Velocidade atual</h2>
                <p id="nomeSensorVelocidade">-</p>
                <strong id="valorVelocidade">- km/h</strong>
                <p>Status operacional: <span id="statusVelocidade">-</span></p>
                <p id="mensagemVelocidade">-</p>
            </article>
            </div>

            <div class="monitoramento-grupo-cartao">
                <form method="GET" class="monitoramento-filtro">
                    <label for="temperatura">Sensor de temperatura</label>
                    <select id="temperatura" name="temperatura" onchange="this.form.submit()">
                        <?php if (empty($sensoresTemperatura)): ?><option value="0">Nenhum sensor de temperatura cadastrado</option><?php endif; ?>
                        <?php foreach ($sensoresTemperatura as $sensor): ?>
                            <option value="<?= (int)$sensor['id_sensor'] ?>" <?= $temperaturaSelecionada === (int)$sensor['id_sensor'] ? 'selected' : '' ?>><?= escapar($sensor['nome_sensor']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="velocidade" value="<?= (int)$velocidadeSelecionada ?>">
                    <input type="hidden" name="rota" value="<?= (int)$rotaSelecionada ?>">
                    <input type="hidden" name="status" value="<?= escapar($filtro) ?>">
                </form>
            <article class="monitoramento-cartao">
                <h2>Temperatura atual</h2>
                <p id="nomeSensorTemperatura">-</p>
                <strong id="valorTemperatura">- °C</strong>
                <p>Status operacional: <span id="statusTemperatura">-</span></p>
                <p id="mensagemTemperatura">-</p>
            </article>
            </div>
        </section>

        <section class="monitoramento-localizacao">
            <h2>Localização</h2>
            <form method="GET" class="monitoramento-filtro monitoramento-filtro-rota">
                <label for="rota">Rota</label>
                <select id="rota" name="rota" onchange="this.form.submit()">
                    <?php if (empty($rotasMonitoramento)): ?><option value="0">Nenhuma rota cadastrada</option><?php endif; ?>
                    <?php foreach ($rotasMonitoramento as $rota): ?>
                        <option value="<?= (int)$rota['id_rotas'] ?>" <?= $rotaSelecionada === (int)$rota['id_rotas'] ? 'selected' : '' ?>><?= escapar($rota['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="velocidade" value="<?= (int)$velocidadeSelecionada ?>">
                <input type="hidden" name="temperatura" value="<?= (int)$temperaturaSelecionada ?>">
                <input type="hidden" name="status" value="<?= escapar($filtro) ?>">
            </form>
            <p id="nomeRota">Percurso ferroviário</p>

            <svg class="mapa-trilhos" viewBox="0 0 760 330"
                 role="img" aria-label="Trajeto ferroviário com ponte e desvios">
                <defs>
                    <pattern id="gradeMapa" width="40" height="40"
                             patternUnits="userSpaceOnUse">
                        <path d="M40 0H0V40" fill="none" stroke="#d8d8d8"
                              stroke-width="2"/>
                    </pattern>
                </defs>

                <rect width="760" height="330" fill="url(#gradeMapa)"/>

                <polyline class="trilho"
                    points="70,175 80,115 150,75 250,60 360,65 470,90 580,120 670,165 690,220 640,265 540,290 420,300 300,290 190,260 100,225 70,175"/>

                <polyline class="trilho"
                    points="70,175 140,185 250,165 360,160 470,175 545,200 640,230"/>
                <polyline class="trilho"
                    points="250,165 270,235 320,285"/>
                <polyline class="trilho"
                    points="360,160 420,130 480,130 580,120"/>
                <polyline class="trilho"
                    points="545,200 580,235 640,265"/>

                <polyline class="trilho-ponte"
                    points="250,60 360,65"/>
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

                <form method="GET" class="falhas_filtro">
                    <label for="status">Status</label>
                    <select id="status" name="status" onchange="this.form.submit()">
                        <option value="Todas" <?= $filtro === 'Todas' ? 'selected' : '' ?>>Todas</option>
                        <option value="Resolvida" <?= $filtro === 'Resolvida' ? 'selected' : '' ?>>Resolvidas</option>
                        <option value="Não resolvida" <?= $filtro === 'Não resolvida' ? 'selected' : '' ?>>Não resolvidas</option>
                    </select>
                    <input type="hidden" name="velocidade" value="<?= (int)$velocidadeSelecionada ?>">
                    <input type="hidden" name="temperatura" value="<?= (int)$temperaturaSelecionada ?>">
                    <input type="hidden" name="rota" value="<?= (int)$rotaSelecionada ?>">
                </form>
            </div>

            <div class="falhas_resumo">
                <div class="falha_contador">
                    <strong><?= $totalResolvidas ?></strong>
                    <span>Resolvidas</span>
                </div>
                <div class="falha_contador">
                    <strong><?= $totalNaoResolvidas ?></strong>
                    <span>Não resolvidas</span>
                </div>
            </div>

            <div class="falhas_lista">
                <?php if (count($falhas) > 0): ?>
                    <?php foreach ($falhas as $falha): ?>
                        <article class="falha_item">
                            <div class="falha_dados">
                                <h3><?= escapar($falha['descricao']) ?></h3>
                                <p>
                                    Sensor: <?= escapar($falha['nome_sensor'] ?? 'Removido') ?>
                                    - Trem: <?= escapar($falha['nome_trem'] ?? 'Não identificado') ?>
                                    - <?= escapar(date('d/m/Y H:i', strtotime($falha['data_registro']))) ?>
                                </p>
                            </div>

                            <?php if ($ehAdministrador): ?>
                                <form method="POST" class="falha_acao">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= escapar(token_csrf()) ?>">
                                    <input type="hidden" name="id_falha"
                                           value="<?= (int)$falha['id_falha'] ?>">
                                    <input type="hidden" name="status"
                                           value="<?= escapar($filtro) ?>">
                                    <input type="hidden" name="velocidade" value="<?= (int)$velocidadeSelecionada ?>">
                                    <input type="hidden" name="temperatura" value="<?= (int)$temperaturaSelecionada ?>">
                                    <input type="hidden" name="rota" value="<?= (int)$rotaSelecionada ?>">

                                    <?php if ((int)$falha['resolvida'] === 1): ?>
                                        <button type="submit" name="acao_falha"
                                                value="reabrir" class="botao_reabrir">
                                            Reabrir
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="acao_falha"
                                                value="resolver" class="botao_resolver">
                                            Marcar como resolvida
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="falhas_vazias">Nenhuma falha encontrada.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<script>
const sensoresVelocidade = <?= json_encode(
    $sensoresVelocidade,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
) ?>;
const sensoresTemperatura = <?= json_encode(
    $sensoresTemperatura,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
) ?>;
const rotasMonitoramento = <?= json_encode(
    $rotasMonitoramento,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
) ?>;

function mostrarVelocidade(sensor) {
    document.getElementById('nomeSensorVelocidade').textContent =
        sensor.nome_sensor + ' - ' + (sensor.nome_trem || 'Trem não definido');
    document.getElementById('valorVelocidade').textContent =
        Number(sensor.velocidade_media || 0).toFixed(0) + ' km/h';
    document.getElementById('statusVelocidade').textContent =
        Number(sensor.velocidade_media || 0) > 100 ? 'Alerta' : 'Normal';
    document.getElementById('mensagemVelocidade').textContent =
        Number(sensor.velocidade_media || 0) > 100
            ? 'Velocidade acima da média'
            : 'Velocidade dentro do limite';
}

function mostrarTemperatura(sensor) {
    document.getElementById('nomeSensorTemperatura').textContent =
        sensor.nome_sensor + ' - ' + (sensor.nome_trem || 'Trem não definido');
    document.getElementById('valorTemperatura').textContent = sensor.temperatura + ' °C';
    const temperatura = Number(sensor.temperatura || 0);
    document.getElementById('statusTemperatura').textContent = temperatura > 40 ? 'Alerta' : 'Normal';
    document.getElementById('mensagemTemperatura').textContent = temperatura > 40
        ? 'Temperatura acima do limite'
        : 'Temperatura dentro do limite';
}

const campoVelocidade = document.getElementById('velocidade');
const campoTemperatura = document.getElementById('temperatura');
const campoRota = document.getElementById('rota');
function atualizarVelocidade() {
    const sensor = sensoresVelocidade.find((item) => Number(item.id_sensor) === Number(campoVelocidade.value)) || sensoresVelocidade[0];
    if (sensor) mostrarVelocidade(sensor);
    else {
        document.getElementById('nomeSensorVelocidade').textContent = 'Sem leitura disponível';
        document.getElementById('valorVelocidade').textContent = '- km/h';
        document.getElementById('statusVelocidade').textContent = '-';
        document.getElementById('mensagemVelocidade').textContent = 'Nenhum sensor de velocidade cadastrado';
    }
}
function atualizarTemperatura() {
    const sensor = sensoresTemperatura.find((item) => Number(item.id_sensor) === Number(campoTemperatura.value)) || sensoresTemperatura[0];
    if (sensor) mostrarTemperatura(sensor);
    else {
        document.getElementById('nomeSensorTemperatura').textContent = 'Sem leitura disponível';
        document.getElementById('valorTemperatura').textContent = '- °C';
        document.getElementById('statusTemperatura').textContent = '-';
        document.getElementById('mensagemTemperatura').textContent = 'Nenhum sensor de temperatura cadastrado';
    }
}
function atualizarRota() {
    const rota = rotasMonitoramento.find((item) => Number(item.id_rotas) === Number(campoRota.value)) || rotasMonitoramento[0];
    document.getElementById('nomeRota').textContent = rota
        ? rota.nome + ' - ' + (rota.nome_trem || 'Trem não definido')
        : 'Nenhuma rota cadastrada';
}
atualizarVelocidade();
atualizarTemperatura();
atualizarRota();
campoVelocidade.addEventListener('change', atualizarVelocidade);
campoTemperatura.addEventListener('change', atualizarTemperatura);
campoRota.addEventListener('change', atualizarRota);

const caminhoDoTrem = [
    [70,175],[80,115],[150,75],[250,60],[360,65],[470,90],
    [580,120],[670,165],[690,220],[640,265],[540,290],[420,300],
    [300,290],[190,260],[100,225],[70,175],
    [140,185],[250,165],[360,160],[470,175],[545,200],
    [580,235],[545,200],[470,175],[360,160],
    [420,130],[480,130],[580,120],[480,130],[420,130],[360,160],
    [270,235],[320,285],[270,235],[360,160],[250,165],[140,185],[70,175]
];

let trechoAtual = 0;
let progressoTrecho = 0;

setInterval(() => {
    if (caminhoDoTrem.length < 2) return;

    progressoTrecho += 0.04;

    if (progressoTrecho >= 1) {
        progressoTrecho = 0;
        trechoAtual = (trechoAtual + 1) % (caminhoDoTrem.length - 1);
    }

    const inicio = caminhoDoTrem[trechoAtual];
    const fim = caminhoDoTrem[trechoAtual + 1];
    const x = inicio[0] + (fim[0] - inicio[0]) * progressoTrecho;
    const y = inicio[1] + (fim[1] - inicio[1]) * progressoTrecho;

    document.getElementById('marcadorTrem')
        .setAttribute('transform', `translate(${x} ${y})`);
}, 50);
</script>
<script src="../../script/confirmar_logout.js?v=20261107"></script>
</body>
</html>

