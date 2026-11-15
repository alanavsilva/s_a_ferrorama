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
$periodoInformado = $_GET['periodo_relatorios'] ?? date('Y-m');
$periodo = is_string($periodoInformado) ? trim($periodoInformado) : '';
$sensorEscolhido = null;
$totalResolvidas = 0;
$totalAbertas = 0;

$resultadoSensores = $conn->query(
    'SELECT s.id_sensor, s.nome_sensor, s.tipo_dado, t.nome AS nome_trem, r.nome AS nome_rota
     FROM sensores s
     INNER JOIN trens t ON t.id_trem = s.id_trem
     INNER JOIN rotas r ON r.id_rotas = s.id_rotas
     ORDER BY s.nome_sensor'
);

if ($resultadoSensores) {
    while ($sensor = $resultadoSensores->fetch_assoc()) {
        $sensores[] = $sensor;
    }
} else {
    error_log('Erro ao carregar sensores para relatório: ' . $conn->error);
    $mensagem = 'Não foi possível carregar os sensores.';
}

$periodoValido = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periodo) === 1;
$sensorValido = false;

foreach ($sensores as $sensor) {
    if ((int) $sensor['id_sensor'] === $sensorSelecionado) {
        $sensorValido = true;
        $sensorEscolhido = $sensor;
        break;
    }
}

if (isset($_GET['gerar'])) {
    if (!$periodoValido || !$sensorValido) {
        $mensagem = 'Selecione um sensor cadastrado e um mês válido.';
    } else {
        $inicio = DateTimeImmutable::createFromFormat('!Y-m', $periodo);

        if (!$inicio) {
            $mensagem = 'O período informado é inválido.';
        } else {
            $fim = $inicio->modify('+1 month');
            $inicioSql = $inicio->format('Y-m-d H:i:s');
            $fimSql = $fim->format('Y-m-d H:i:s');

            $stmt = $conn->prepare(
                'SELECT f.descricao, f.data_registro, f.resolvida,
                        s.nome_sensor, s.tipo_dado, t.nome AS nome_trem, r.nome AS nome_rota
                 FROM falhas_operacionais f
                 INNER JOIN sensores s ON s.id_sensor = f.id_sensor
                 INNER JOIN trens t ON t.id_trem = s.id_trem
                 INNER JOIN rotas r ON r.id_rotas = s.id_rotas
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
                <h2>Relatório de falhas operacionais</h2>
                <div class="campo-relatorios">
                    <label for="sensor-relatorios">Sensor</label>
                    <select name="sensor_relatorios" id="sensor-relatorios" required>
                        <option value="">Selecione um sensor</option>
                        <?php foreach ($sensores as $sensor): ?>
                            <option value="<?= (int) $sensor['id_sensor'] ?>" <?= $sensorSelecionado === (int) $sensor['id_sensor'] ? 'selected' : '' ?>>
                                <?= escapar($sensor['nome_sensor']) ?> — <?= escapar($sensor['nome_trem']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo-relatorios">
                    <label for="periodo-relatorios">Mês</label>
                    <input type="month" name="periodo_relatorios" id="periodo-relatorios" value="<?= escapar($periodoValido ? $periodo : date('Y-m')) ?>" required>
                </div>
                <button type="submit" name="gerar" value="1" class="botao-relatorios" <?= count($sensores) === 0 ? 'disabled' : '' ?>>Gerar relatório</button>
                <?php if ($mensagem !== ''): ?><p class="mensagem-relatorios" role="alert"><?= escapar($mensagem) ?></p><?php endif; ?>
                <?php if (count($sensores) === 0): ?><p class="mensagem-relatorios">Cadastre um sensor antes de gerar o relatório.</p><?php endif; ?>
            </form>

            <section class="resultado-relatorios" aria-live="polite">
                <?php if ($relatorioGerado): ?>
                    <div class="estado-relatorios">
                        <h2>Resultado do período</h2>
                        <p>Sensor: <strong><?= escapar($sensorEscolhido['nome_sensor']) ?></strong> (<?= escapar($sensorEscolhido['tipo_dado']) ?>)</p>
                        <p>Trem: <strong><?= escapar($sensorEscolhido['nome_trem']) ?></strong></p>
                        <p>Rota: <strong><?= escapar($sensorEscolhido['nome_rota']) ?></strong></p>
                        <p>Mês consultado: <strong><?= escapar(date('m/Y', strtotime($periodo . '-01'))) ?></strong></p>
                        <p>Falhas registradas: <strong><?= count($relatorio) ?></strong></p>
                        <p>Resolvidas: <strong><?= $totalResolvidas ?></strong> · Em aberto: <strong><?= $totalAbertas ?></strong></p>
                    </div>
                    <?php if ($relatorio): ?>
                        <table>
                            <thead><tr><th>Falha</th><th>Data</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($relatorio as $falha): ?>
                                <tr>
                                    <td><?= escapar($falha['descricao']) ?></td>
                                    <td><?= escapar(date('d/m/Y H:i', strtotime($falha['data_registro']))) ?></td>
                                    <td><?= (int) $falha['resolvida'] === 1 ? 'Resolvida' : 'Em aberto' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="estado-vazio-relatorios">Nenhuma falha foi registrada para este sensor no mês selecionado.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="estado-vazio-relatorios">
                        <h2>Relatório</h2>
                        <p>Selecione um sensor e um mês para consultar as falhas registradas no sistema.</p>
                    </div>
                <?php endif; ?>
            </section>
        </section>
    </main>
</div>
<script src="../../script/confirmar_logout.js?v=20261107"></script>
</body>
</html>
