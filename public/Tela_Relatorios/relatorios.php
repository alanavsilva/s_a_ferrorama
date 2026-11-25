<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Os dois perfis podem consultar relatórios depois de entrar no sistema.
exigir_login();

$sensorSelecionado = (int) ($_GET['sensor_relatorios'] ?? 0);
$data = $_GET['periodo_relatorios'] ?? date('Y-m-d');
$mensagem = '';
$relatorioGerado = false;

$registros = [];
$totalResolvidas = 0;
$totalFalhas = 0;
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
        // Busca a rota pelo ID que está no sensor. Depois busca o trem pelo ID que está na rota.
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

        // A consulta traz leituras OU falhas. Guardamos o resultado em uma lista só.
        while ($linha = mysqli_fetch_assoc($resultado)) {
            $registros[] = $linha;
            if ($tipo === 'Status' && $linha['situacao'] === 'Resolvida') {
                $totalResolvidas++;
            }
        }
        mysqli_stmt_close($stmt);

        if ($tipo === 'Status') {
            $totalFalhas = count($registros);
        }
        $relatorioGerado = true;

        // Junta o sensor e o dia para reconhecer se a última consulta do histórico é a mesma.
        $chaveAtual = $sensorSelecionado . '|' . $data;
        $ultimaChave = $historico[0]['chave'] ?? '';

        if ($chaveAtual !== $ultimaChave) {
            // A primeira leitura é a mais recente. Um relatório de falhas não tem esse valor.
            $valorLeitura = $registros[0]['valor'] ?? null;

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
                'total_falhas' => $totalFalhas
            ];

            // Coloca o relatório novo no começo e guarda só os 20 mais recentes.
            array_unshift($historico, $item);
            $historico = array_slice($historico, 0, 20);
            $_SESSION['historico_relatorios'] = $historico;
        }
    }
}

// Prepara a lista do histórico sem mostrar de novo o relatório que já está aberto acima.
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
    <link rel="stylesheet" href="../../assets/style/style.css?v=nomes-underscore-1">
</head>

<body class="pagina_relatorios">
    <div class="pagina">
        <!-- Menu desta página. A classe ativo destaca a seção aberta. -->
        <aside class="menu">
            <img class="logo_trem" src="https://img.icons8.com/ios/100/701015/train.png" alt="Logo TremTech">
            <p class="marca">SISTEMA TREMTECH</p>

            <a href="../Tela_Home/home.php"><img src="https://img.icons8.com/ios-filled/18/701015/home.png" alt=""> Home</a>
            <a href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a class="ativo" href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
            <a class="logout" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
        </aside>


        <main class="content_user conteudo_relatorios">
            <header class="cabecalho_relatorios">
                <div class="titulo_area_relatorios">
                    <p class="sobretitulo_relatorios">PAINEL DE RELATÓRIOS</p>
                    <h1>Relatórios do sistema</h1>
                </div>

                <div class="admin_badge_user card_recuado">
                    <div class="avatar_user"></div>
                    <div>
                        <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                        <span><?php echo escapar($_SESSION['nome']); ?></span>
                    </div>
                </div>
            </header>

            <section class="area_relatorios">
                <form class="formulario_relatorios" method="GET">
                    <h2>Gerar relatório</h2>

                    <div class="campo_relatorios">
                        <label for="sensor_relatorios">Sensor</label>
                        <select name="sensor_relatorios" id="sensor_relatorios" required>
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

                    <div class="campo_relatorios">
                        <label for="periodo_relatorios">Dia do relatório</label>
                        <input type="date" name="periodo_relatorios" id="periodo_relatorios"
                               value="<?php echo escapar($data); ?>" required>
                    </div>

                    <button type="submit" name="gerar" value="1" class="botao_relatorios"
                        <?php if (mysqli_num_rows($sensores) === 0) { echo 'disabled'; } ?>>
                        Gerar relatório
                    </button>

                    <?php if ($mensagem !== '') { ?>
                        <p class="mensagem_relatorios" role="alert"><?php echo escapar($mensagem); ?></p>
                    <?php } ?>

                    <?php if (mysqli_num_rows($sensores) === 0) { ?>
                        <p class="mensagem_relatorios">Cadastre um sensor antes de gerar o relatório.</p>
                    <?php } ?>
                </form>

                <section class="resultado_relatorios">
                    <?php if ($relatorioGerado) { ?>
                        <div class="resumo_relatorio">
                            <h2>Relatório gerado</h2>
                            <p>Responsável: <strong><?php echo escapar($responsavel); ?></strong></p>
                            <p>Sensor: <strong><?php echo escapar($sensorEscolhido['nome_sensor']); ?></strong>
                                (<?php echo escapar($tipo); ?>)</p>
                            <p>Trem: <strong><?php echo escapar($trem['nome']); ?></strong></p>
                            <p>Rota: <strong><?php echo escapar($rota['nome']); ?></strong></p>
                            <p>Data consultada: <strong><?php echo date('d/m/Y', $dataConvertida); ?></strong></p>

                            <?php if ($tipo === 'Status') { ?>
                                <p>Falhas registradas: <strong><?php echo $totalFalhas; ?></strong></p>
                                <p>Resolvidas: <strong><?php echo $totalResolvidas; ?></strong>
                                    · Em aberto: <strong><?php echo $totalFalhas - $totalResolvidas; ?></strong></p>
                            <?php } else { ?>
                                <h3>Leituras de <?php echo escapar(strtolower($tipo)); ?></h3>

                                <?php if (count($registros) === 0) { ?>
                                    <p>Nenhuma leitura registrada para este sensor na data selecionada.</p>
                                <?php } ?>

                                <?php foreach ($registros as $leitura) { ?>
                                    <p><strong><?php echo number_format($leitura['valor'], 2, ',', '.'); ?>
                                            <?php echo $unidade; ?></strong></p>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <?php if ($tipo === 'Status' && $totalFalhas > 0) { ?>
                            <section class="lista_falhas_relatorio">
                                <h3>Falhas do dia</h3>

                                <?php foreach ($registros as $falha) { ?>
                                    <article class="relatorio_falha_linha">
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
                            <p class="relatorio_sem_falhas">Nenhuma falha registrada para este sensor na data selecionada.</p>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="estado_vazio_relatorios">
                            <h2>Relatório</h2>
                            <p>Selecione um sensor e uma data para consultar o relatório.</p>
                        </div>
                    <?php } ?>
                </section>

                <?php if (count($relatoriosAnteriores) > 0) { ?>
                    <section class="historico_relatorios">
                        <h2>Histórico de relatórios</h2>

                        <?php foreach ($relatoriosAnteriores as $item) { ?>
                            <article class="item_historico_relatorio">
                                <div>
                                    <strong>Responsável: <?php echo escapar($item['responsavel']); ?></strong>
                                    <span><?php echo escapar($item['sensor']); ?> - <?php echo escapar($item['tipo']); ?></span>
                                    <span><?php echo escapar($item['trem']); ?> - <?php echo escapar($item['rota']); ?></span>
                                </div>

                                <div>
                                    <span>Data: <?php echo date('d/m/Y', strtotime($item['data'])); ?></span>
                                    <span>Gerado em: <?php echo date('d/m/Y H:i', strtotime($item['gerado_em'])); ?></span>
                                </div>

                                <strong class="resumo_historico_relatorio">
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

    <!-- A janela fica fechada até a pessoa clicar em Sair. -->
    <dialog class="modal_confirmacao" id="janela_saida" aria-labelledby="tituloConfirmarSaida">
        <div class="modal_confirmacao_icone" aria-hidden="true">?</div>
        <h2 id="tituloConfirmarSaida">Sair do sistema?</h2>
        <p class="modal_confirmacao_mensagem">Tem certeza de que deseja sair da sua conta?</p>
        <div class="modal_confirmacao_acoes">
            <button type="button" class="modal_btn_cancelar" id="cancelar_saida">Continuar no sistema</button>
            <button type="button" class="modal_btn_sair" id="confirmar_saida">Sair</button>
        </div>
    </dialog>

<script src="../../script/validacao.js?v=simples-7"></script>
</body>
</html>