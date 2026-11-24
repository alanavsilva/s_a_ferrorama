<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Os dois perfis podem consultar relatórios depois de entrar no sistema.
exigir_login();

$sensorSelecionado = (int) ($_GET['sensor_relatorios'] ?? 0);
$data = $_GET['periodo_relatorios'] ?? date('Y-m-d');
$mensagem = '';
$relatorioGerado = false;

$leituras = [];
$falhas = [];
$totalResolvidas = 0;
$totalAbertas = 0;
$unidade = '';
$chaveAtual = '';

$historico = $_SESSION['historico_relatorios'] ?? [];

$sql = "SELECT * FROM sensores ORDER BY nome_sensor";
$sensores = mysqli_query($conn, $sql);

// Gera o relatório somente quando o formulário envia um sensor e uma data.
if (isset($_GET['gerar'])) {
    $sql = "SELECT * FROM sensores WHERE id_sensor = $sensorSelecionado";
    $resultado = mysqli_query($conn, $sql);
    $sensorEscolhido = mysqli_fetch_assoc($resultado);

    // Converte a data e compara com o texto original para rejeitar datas inválidas.
    $dataConvertida = strtotime($data);

    if (!$sensorEscolhido) {
        $mensagem = 'Selecione um sensor cadastrado.';
    } elseif ($dataConvertida === false || date('Y-m-d', $dataConvertida) !== $data) {
        $mensagem = 'Selecione uma data válida.';
        $data = date('Y-m-d');
    } else {
        // O sensor aponta para a rota, e a rota aponta para o trem.
        $idRota = (int) $sensorEscolhido['id_rotas'];
        $sql = "SELECT * FROM rotas WHERE id_rotas = $idRota";
        $resultado = mysqli_query($conn, $sql);
        $rota = mysqli_fetch_assoc($resultado);

        $idTrem = (int) $rota['id_trem'];
        $sql = "SELECT * FROM trens WHERE id_trem = $idTrem";
        $resultado = mysqli_query($conn, $sql);
        $trem = mysqli_fetch_assoc($resultado);

        $responsavel = 'Não definido';

        if (isset($trem['id_usuario'])) {
            $idUsuario = (int) $trem['id_usuario'];
            $sql = "SELECT nome FROM usuarios WHERE id_usuario = $idUsuario";
            $resultado = mysqli_query($conn, $sql);
            $usuario = mysqli_fetch_assoc($resultado);

            if ($usuario) {
                $responsavel = $usuario['nome'];
            }
        }

        $tipo = $sensorEscolhido['tipo_dado'];

        // DATE() usa só o dia do registro, sem levar em conta a hora.
        if ($tipo === 'Status') {
            $sql = "SELECT descricao, data_registro, situacao
                    FROM falhas_operacionais
                    WHERE id_sensor = $sensorSelecionado AND DATE(data_registro) = ?
                    ORDER BY data_registro DESC";
        } else {
            $sql = "SELECT valor, data_leitura FROM leituras_sensores
                    WHERE id_sensor = $sensorSelecionado AND DATE(data_leitura) = ?
                    ORDER BY data_leitura DESC, id_leitura DESC";

            if ($tipo === 'Velocidade') {
                $unidade = 'km/h';
            } else {
                $unidade = '°C';
            }
        }

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $data);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        while ($linha = mysqli_fetch_assoc($resultado)) {
            if ($tipo === 'Status') {
                $falhas[] = $linha;

                if ($linha['situacao'] === 'Resolvida') {
                    $totalResolvidas++;
                } else {
                    $totalAbertas++;
                }
            } else {
                $leituras[] = $linha;
            }
        }

        mysqli_stmt_close($stmt);
        $relatorioGerado = true;

        // O ID do sensor e a data identificam a consulta no histórico.
        $chaveAtual = $sensorSelecionado . '|' . $data;
        $ultimaChave = $historico[0]['chave'] ?? '';

        if ($chaveAtual !== $ultimaChave) {
            $valorLeitura = null;

            if (isset($leituras[0])) {
                $valorLeitura = $leituras[0]['valor'];
            }

            $item = [
                'chave' => $chaveAtual,
                'sensor' => $sensorEscolhido['nome_sensor'],
                'tipo' => $tipo,
                'trem' => $trem['nome'],
                'rota' => $rota['nome'],
                'responsavel' => $responsavel,
                'data' => $data,
                'gerado_em' => date('Y-m-d H:i:s'),
                'valor_leitura' => $valorLeitura,
                'total_falhas' => count($falhas)
            ];

            // Coloca o relatório novo no começo e guarda só os 20 mais recentes.
            array_unshift($historico, $item);
            $historico = array_slice($historico, 0, 20);
            $_SESSION['historico_relatorios'] = $historico;
        }
    }
}

// Separa os itens que vão aparecer abaixo do relatório aberto.
$relatoriosAnteriores = [];

foreach ($historico as $item) {
    if ($chaveAtual === '' || ($item['chave'] ?? '') !== $chaveAtual) {
        $relatoriosAnteriores[] = $item;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-topo-2">
</head>

<body class="pagina-relatorios">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user conteudo-relatorios">
            <header class="cabecalho-relatorios">
                <div class="titulo-area-relatorios">
                    <p class="sobretitulo-relatorios">PAINEL DE RELATÓRIOS</p>
                    <h1>Relatórios do sistema</h1>
                </div>

                <div class="admin-badge-user card-recuado">
                    <div class="avatar-user"></div>
                    <div>
                        <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                        <span><?php echo escapar($_SESSION['nome']); ?></span>
                    </div>
                </div>
            </header>

            <section class="area-relatorios">
                <form class="formulario-relatorios" method="GET">
                    <h2>Gerar relatório</h2>

                    <div class="campo-relatorios">
                        <label for="sensor-relatorios">Sensor</label>
                        <select name="sensor_relatorios" id="sensor-relatorios" required>
                            <option value="">Selecione um sensor</option>

                            <?php
                            while ($sensor = mysqli_fetch_assoc($sensores)) {
                                // Busca o trem para mostrar o nome dele ao lado do sensor.
                                $idRota = (int) $sensor['id_rotas'];
                                $sql = "SELECT id_trem FROM rotas WHERE id_rotas = $idRota";
                                $resultado = mysqli_query($conn, $sql);
                                $rotaDoSensor = mysqli_fetch_assoc($resultado);

                                $idTrem = (int) $rotaDoSensor['id_trem'];
                                $sql = "SELECT nome FROM trens WHERE id_trem = $idTrem";
                                $resultado = mysqli_query($conn, $sql);
                                $tremDoSensor = mysqli_fetch_assoc($resultado);
                            ?>
                                <option value="<?php echo $sensor['id_sensor']; ?>"
                                    <?php if ($sensorSelecionado == $sensor['id_sensor']) { echo 'selected'; } ?>>
                                    <?php echo escapar($sensor['nome_sensor']); ?>
                                    - <?php echo escapar($tremDoSensor['nome']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo-relatorios">
                        <label for="periodo-relatorios">Dia do relatório</label>
                        <input type="date" name="periodo_relatorios" id="periodo-relatorios"
                               value="<?php echo escapar($data); ?>" required>
                    </div>

                    <button type="submit" name="gerar" value="1" class="botao-relatorios"
                        <?php if (mysqli_num_rows($sensores) === 0) { echo 'disabled'; } ?>>
                        Gerar relatório
                    </button>

                    <?php if ($mensagem !== '') { ?>
                        <p class="mensagem-relatorios" role="alert"><?php echo escapar($mensagem); ?></p>
                    <?php } ?>

                    <?php if (mysqli_num_rows($sensores) === 0) { ?>
                        <p class="mensagem-relatorios">Cadastre um sensor antes de gerar o relatório.</p>
                    <?php } ?>
                </form>

                <section class="resultado-relatorios">
                    <?php if ($relatorioGerado) { ?>
                        <div class="resumo-relatorio">
                            <h2>Relatório gerado</h2>
                            <p>Responsável: <strong><?php echo escapar($responsavel); ?></strong></p>
                            <p>Sensor: <strong><?php echo escapar($sensorEscolhido['nome_sensor']); ?></strong>
                                (<?php echo escapar($tipo); ?>)</p>
                            <p>Trem: <strong><?php echo escapar($trem['nome']); ?></strong></p>
                            <p>Rota: <strong><?php echo escapar($rota['nome']); ?></strong></p>
                            <p>Data consultada: <strong><?php echo date('d/m/Y', $dataConvertida); ?></strong></p>

                            <?php if ($tipo === 'Status') { ?>
                                <p>Falhas registradas: <strong><?php echo count($falhas); ?></strong></p>
                                <p>Resolvidas: <strong><?php echo $totalResolvidas; ?></strong>
                                    · Em aberto: <strong><?php echo $totalAbertas; ?></strong></p>
                            <?php } else { ?>
                                <h3>Leituras de <?php echo escapar(strtolower($tipo)); ?></h3>

                                <?php if (count($leituras) === 0) { ?>
                                    <p>Nenhuma leitura registrada para este sensor na data selecionada.</p>
                                <?php } ?>

                                <?php foreach ($leituras as $leitura) { ?>
                                    <p><strong><?php echo number_format($leitura['valor'], 2, ',', '.'); ?>
                                            <?php echo $unidade; ?></strong></p>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <?php if ($tipo === 'Status' && count($falhas) > 0) { ?>
                            <section class="lista-falhas-relatorio">
                                <h3>Falhas do dia</h3>

                                <?php foreach ($falhas as $falha) { ?>
                                    <article class="relatorio-falha-linha">
                                        <strong>Descrição da falha</strong>
                                        <p><?php echo escapar($falha['descricao']); ?></p>
                                        <div>
                                            <span>Data: <?php echo date('d/m/Y', strtotime($falha['data_registro'])); ?></span>
                                            <span>Situação: <?php echo escapar($falha['situacao']); ?></span>
                                        </div>
                                    </article>
                                <?php } ?>
                            </section>
                        <?php } elseif ($tipo === 'Status') { ?>
                            <p class="relatorio-sem-falhas">Nenhuma falha registrada para este sensor na data selecionada.</p>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="estado-vazio-relatorios">
                            <h2>Relatório</h2>
                            <p>Selecione um sensor e uma data para consultar o relatório.</p>
                        </div>
                    <?php } ?>
                </section>

                <?php if (count($relatoriosAnteriores) > 0) { ?>
                    <section class="historico-relatorios">
                        <h2>Histórico de relatórios</h2>

                        <?php foreach ($relatoriosAnteriores as $item) { ?>
                            <article class="item-historico-relatorio">
                                <div>
                                    <strong>Responsável: <?php echo escapar($item['responsavel']); ?></strong>
                                    <span><?php echo escapar($item['sensor']); ?> - <?php echo escapar($item['tipo']); ?></span>
                                    <span><?php echo escapar($item['trem']); ?> - <?php echo escapar($item['rota']); ?></span>
                                </div>

                                <div>
                                    <span>Data: <?php echo date('d/m/Y', strtotime($item['data'])); ?></span>
                                    <span>Gerado em: <?php echo date('d/m/Y H:i', strtotime($item['gerado_em'])); ?></span>
                                </div>

                                <strong class="resumo-historico-relatorio">
                                    <?php if ($item['tipo'] === 'Status') { ?>
                                        <?php echo $item['total_falhas']; ?> falha(s)
                                    <?php } elseif (isset($item['valor_leitura'])) { ?>
                                        <?php echo number_format($item['valor_leitura'], 2, ',', '.'); ?>
                                        <?php
                                        if ($item['tipo'] === 'Velocidade') {
                                            echo 'km/h';
                                        } else {
                                            echo '°C';
                                        }
                                        ?>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </strong>
                            </article>
                        <?php } ?>
                    </section>
                <?php } ?>
            </section>
        </main>
    </div>

<script src="../../script/validacao.js"></script>
</body>
</html>