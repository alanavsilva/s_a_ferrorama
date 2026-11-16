<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_login();

$ehAdministrador = strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;
$sensores = [];
$relatorio = [];
$mensagem = '';
$relatorioGerado = false;
$sensorSelecionado = filter_input(INPUT_GET, 'sensor_relatorios', FILTER_VALIDATE_INT) ?: 0;
$periodoInformado = $_GET['periodo_relatorios'] ?? date('Y-m-d');
$periodo = is_string($periodoInformado) ? trim($periodoInformado) : '';
$sensorEscolhido = null;
$totalResolvidas = 0;
$totalAbertas = 0;

$resultadoSensores = $conn->query(
    'SELECT s.id_sensor, s.nome_sensor, s.tipo_dado, r.nome AS nome_rota,
            t.nome AS nome_trem, t.velocidade_media, u.nome AS nome_responsavel
     FROM sensores s
     INNER JOIN rotas r ON r.id_rotas = s.id_rotas
     INNER JOIN trens t ON t.id_trem = r.id_trem
     LEFT JOIN usuarios u ON u.id_usuario = t.id_usuario
     ORDER BY s.nome_sensor'
);

if ($resultadoSensores) {
    $indiceTemperatura = 0;
    while ($sensor = $resultadoSensores->fetch_assoc()) {
        if ($sensor['tipo_dado'] === 'Temperatura') {
            $sensor['temperatura'] = 35 + ($indiceTemperatura % 4);
            $indiceTemperatura++;
        }
        $sensores[] = $sensor;
    }
} else {
    error_log('Erro ao carregar sensores para relatório: ' . $conn->error);
    $mensagem = 'Não foi possível carregar os sensores.';
}

$partesPeriodo = explode('-', $periodo);
$periodoValido = count($partesPeriodo) === 3
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodo) === 1
    && checkdate((int) ($partesPeriodo[1] ?? 0), (int) ($partesPeriodo[2] ?? 0), (int) ($partesPeriodo[0] ?? 0));
$sensorValido = false;

foreach ($sensores as $sensor) {
    if ((int) $sensor['id_sensor'] === $sensorSelecionado) {
        $sensorValido = true;
        $sensorEscolhido = $sensor;
        break;
    }
}

if (isset($_GET['gerar'])) {
    if (!$sensorValido || !$periodoValido) {
        $mensagem = $sensorValido ? 'Selecione uma data válida.' : 'Selecione um sensor cadastrado.';
    } else {
        $inicio = DateTimeImmutable::createFromFormat('!Y-m-d', $periodo);

        if (!$inicio) {
            $mensagem = 'O período informado é inválido.';
        } elseif ($sensorEscolhido['tipo_dado'] !== 'Status') {
            $relatorioGerado = true;
        } else {
            $fim = $inicio->modify('+1 day');
            $inicioSql = $inicio->format('Y-m-d H:i:s');
            $fimSql = $fim->format('Y-m-d H:i:s');

            $stmt = $conn->prepare(
                'SELECT f.descricao, f.data_registro, f.resolvida,
                        s.nome_sensor, s.tipo_dado, t.nome AS nome_trem
                 FROM falhas_operacionais f
                 INNER JOIN sensores s ON s.id_sensor = f.id_sensor
                 INNER JOIN rotas r ON r.id_rotas = s.id_rotas
                 INNER JOIN trens t ON t.id_trem = r.id_trem
                 WHERE f.id_sensor = ?
                   AND f.data_registro >= ?
                   AND f.data_registro < ?
                 ORDER BY f.data_registro DESC'
            );

            if ($stmt) {
                $stmt->bind_param('iss', $sensorSelecionado, $inicioSql, $fimSql);

                try {
                    if ($stmt->execute()) {
                        $resultado = $stmt->get_result();
                        while ($falha = $resultado->fetch_assoc()) {
                            $relatorio[] = $falha;
                            if ((int) $falha['resolvida'] === 1) {
                                $totalResolvidas++;
                            } else {
                                $totalAbertas++;
                            }
                        }
                        $relatorioGerado = true;
                    } else {
                        $mensagem = 'Não foi possível gerar o relatório.';
                        error_log('Erro ao consultar relatório: ' . $stmt->error);
                    }
                } catch (mysqli_sql_exception $e) {
                    error_log('Erro ao gerar relatório: ' . $e->getMessage());
                    $mensagem = 'Não foi possível gerar o relatório.';
                }

                $stmt->close();
            } else {
                error_log('Erro ao preparar relatório: ' . $conn->error);
                $mensagem = 'Não foi possível preparar o relatório.';
            }
        }
    }
}

$historicoRelatorios = $_SESSION['historico_relatorios'] ?? [];
if (!is_array($historicoRelatorios)) $historicoRelatorios = [];
$assinaturaAtual = '';
if ($relatorioGerado && $sensorEscolhido) {
    $assinaturaAtual = hash('sha256', $sensorSelecionado . '|' . $periodo . '|' . $sensorEscolhido['tipo_dado']);
    $ultimoRelatorio = $historicoRelatorios[0] ?? null;
    if (!is_array($ultimoRelatorio) || ($ultimoRelatorio['assinatura'] ?? '') !== $assinaturaAtual) {
        array_unshift($historicoRelatorios, [
            'assinatura' => $assinaturaAtual,
            'sensor' => $sensorEscolhido['nome_sensor'],
            'tipo' => $sensorEscolhido['tipo_dado'],
            'trem' => $sensorEscolhido['nome_trem'],
            'rota' => $sensorEscolhido['nome_rota'],
            'responsavel' => $sensorEscolhido['nome_responsavel'] ?? 'Não definido',
            'data' => $periodo,
            'gerado_em' => date('Y-m-d H:i:s'),
            'velocidade' => (float) ($sensorEscolhido['velocidade_media'] ?? 0),
            'temperatura' => (int) ($sensorEscolhido['temperatura'] ?? 0),
            'total_falhas' => count($relatorio),
            'resolvidas' => $totalResolvidas,
            'abertas' => $totalAbertas
        ]);
        $historicoRelatorios = array_slice($historicoRelatorios, 0, 20);
        $_SESSION['historico_relatorios'] = $historicoRelatorios;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - TREMTECH</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261115-ui">
</head>
<body class="pagina-relatorios">
<div class="pagina">
    <aside class="menu">
        <img class="logo-trem" src="https://img.icons8.com/ios/100/701015/train.png" alt="Logo TremTech">
        <p class="marca">SISTEMA TREMTECH</p>
        <a href="../Tela_Home/home.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/home.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>Home</a>
        <a href="../Tela_Usuario/cadastrar_usuario.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/user.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>Usuários</a>
        <a href="../Tela_Sensores/cadastrar_sensores.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>Sensores</a>
        <a href="../Tela_Trens/cadastrar_trens.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/train.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>Trens</a>
        <a href="../Tela_Rotas/cadastrar_rotas.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/map.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>Rotas</a>
        <a href="../Tela_Monitoramento/monitoramento.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/visible.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>Monitoramento</a>
        <a href="../Tela_Falhas/cadastrar_falha.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/701015/error.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/701015/error.png');"></span>Falhas</a>
        <a class="ativo" href="relatorios.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/report-card.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>Relatórios</a>
        <a class="logout" href="../../logout.php" data-confirmar-logout><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');"></span>Logout</a>
    </aside>

    <main class="content-user conteudo-relatorios">
        <header class="cabecalho-relatorios">
            <div class="titulo-area-relatorios">
                <p class="sobretitulo-relatorios">PAINEL DE RELATÓRIOS</p>
                <h1>Relatórios do sistema</h1>
            </div>
            <div class="admin-badge-user card-recuado">
                <div class="avatar-user"></div>
                <div><strong><?= $ehAdministrador ? 'Administrador' : 'Funcionário' ?></strong><span><?= escapar($_SESSION['nome'] ?? '') ?></span></div>
            </div>
        </header>

        <section class="area-relatorios">
            <form class="formulario-relatorios" action="relatorios.php" method="GET">
                <h2>Gerar relatório</h2>
                <div class="campo-relatorios">
                    <label for="sensor-relatorios">Sensor</label>
                    <select name="sensor_relatorios" id="sensor-relatorios" required>
                        <option value="">Selecione um sensor</option>
                        <?php foreach ($sensores as $sensor): ?>
                            <option value="<?= (int) $sensor['id_sensor'] ?>" <?= $sensorSelecionado === (int) $sensor['id_sensor'] ? 'selected' : '' ?>>
                                <?= escapar($sensor['nome_sensor']) ?> - <?= escapar($sensor['nome_trem']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo-relatorios">
                    <label for="periodo-relatorios">Dia do relatório</label>
                    <input type="date" name="periodo_relatorios" id="periodo-relatorios" value="<?= escapar($periodoValido ? $periodo : date('Y-m-d')) ?>" required>
                </div>
                <button type="submit" name="gerar" value="1" class="botao-relatorios" <?= count($sensores) === 0 ? 'disabled' : '' ?>>Gerar relatório</button>
                <?php if ($mensagem !== ''): ?><p class="mensagem-relatorios" role="alert"><?= escapar($mensagem) ?></p><?php endif; ?>
                <?php if (count($sensores) === 0): ?><p class="mensagem-relatorios">Cadastre um sensor antes de gerar o relatório.</p><?php endif; ?>
            </form>

            <section class="resultado-relatorios" aria-live="polite">
                <?php if ($relatorioGerado): ?>
                    <div class="resumo-relatorio">
                        <h2>Relatório gerado</h2>
                        <p>Responsável: <strong><?= escapar($sensorEscolhido['nome_responsavel'] ?? 'Não definido') ?></strong></p>
                        <p>Sensor: <strong><?= escapar($sensorEscolhido['nome_sensor']) ?></strong> (<?= escapar($sensorEscolhido['tipo_dado']) ?>)</p>
                        <p>Trem: <strong><?= escapar($sensorEscolhido['nome_trem']) ?></strong></p>
                        <p>Rota: <strong><?= escapar($sensorEscolhido['nome_rota']) ?></strong></p>
                        <p>Data consultada: <strong><?= escapar(date('d/m/Y', strtotime($periodo))) ?></strong></p>
                        <?php if ($sensorEscolhido['tipo_dado'] === 'Status'): ?>
                            <p>Falhas registradas: <strong><?= count($relatorio) ?></strong></p>
                            <p>Resolvidas: <strong><?= $totalResolvidas ?></strong> · Em aberto: <strong><?= $totalAbertas ?></strong></p>
                        <?php elseif ($sensorEscolhido['tipo_dado'] === 'Velocidade'): ?>
                            <p>Velocidade média atual: <strong><?= escapar(number_format((float) $sensorEscolhido['velocidade_media'], 0, ',', '.')) ?> km/h</strong></p>
                            <p>Essa leitura corresponde ao valor atual disponível para o trem vinculado.</p>
                        <?php else: ?>
                            <p>Temperatura exibida: <strong><?= (int) $sensorEscolhido['temperatura'] ?> °C</strong></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($sensorEscolhido['tipo_dado'] === 'Status' && $relatorio): ?>
                        <section class="lista-falhas-relatorio">
                            <h3>Falhas do dia</h3>
                            <?php foreach ($relatorio as $falha): ?>
                                <article class="relatorio-falha-linha">
                                    <strong>Descrição da falha</strong>
                                    <p><?= escapar($falha['descricao']) ?></p>
                                    <div>
                                        <span>Data: <?= escapar(date('d/m/Y H:i', strtotime($falha['data_registro']))) ?></span>
                                        <span>Status: <?= (int) $falha['resolvida'] === 1 ? 'Resolvida' : 'Em aberto' ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </section>
                    <?php elseif ($sensorEscolhido['tipo_dado'] === 'Status'): ?>
                        <p class="relatorio-sem-falhas">Nenhuma falha foi registrada para este sensor na data selecionada.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="estado-vazio-relatorios">
                        <h2>Relatório</h2>
                        <p>Selecione um sensor para consultar a informação correspondente ao tipo de leitura.</p>
                    </div>
                <?php endif; ?>
            </section>

            <?php $relatoriosAnteriores = array_values(array_filter($historicoRelatorios, static fn($item) => ($item['assinatura'] ?? '') !== $assinaturaAtual)); ?>
            <?php if ($relatoriosAnteriores): ?>
                <section class="historico-relatorios">
                    <h2>Histórico de relatórios</h2>
                    <?php foreach ($relatoriosAnteriores as $item): ?>
                        <article class="item-historico-relatorio">
                            <div><strong>Responsável: <?= escapar($item['responsavel']) ?></strong>
                                <span><?= escapar($item['sensor']) ?> - <?= escapar($item['tipo']) ?></span>
                                <span><?= escapar($item['trem']) ?> - <?= escapar($item['rota']) ?></span>
                            </div>
                            <div><span>Data: <?= escapar(date('d/m/Y', strtotime($item['data']))) ?></span>
                                <span>Gerado em: <?= escapar(date('d/m/Y H:i', strtotime($item['gerado_em']))) ?></span>
                            </div>
                            <strong class="resumo-historico-relatorio">
                                <?php if ($item['tipo'] === 'Status'): ?><?= (int) $item['total_falhas'] ?> falha(s)<?php elseif ($item['tipo'] === 'Velocidade'): ?><?= escapar(number_format((float) $item['velocidade'], 0, ',', '.')) ?> km/h<?php else: ?><?= (int) $item['temperatura'] ?> °C<?php endif; ?>
                            </strong>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </section>
    </main>
</div>
<script src="../../script/confirmar_logout.js?v=20261107"></script>
</body>
</html>
