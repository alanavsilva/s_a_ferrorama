<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_administrador();

$mensagemErro = '';
$valor = '';
$descricaoFalha = '';
$sensorSelecionado = 0;
$dataLeitura = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();

    $sensorSelecionado = filter_input(INPUT_POST, 'id_sensor', FILTER_VALIDATE_INT) ?: 0;
    $valorInformado = trim($_POST['valor'] ?? '');
    $dataLeitura = trim($_POST['data_leitura'] ?? '');
    $descricaoFalha = trim($_POST['descricao_falha'] ?? '');

    $stmtSensor = $conn->prepare("SELECT id_sensor, tipo_dado FROM sensores WHERE id_sensor = ? AND tipo_dado IN ('Velocidade', 'Temperatura', 'Status')");
    $stmtSensor->bind_param('i', $sensorSelecionado);
    $stmtSensor->execute();
    $sensorEscolhido = $stmtSensor->get_result()->fetch_assoc();
    $stmtSensor->close();

    $dataConvertida = DateTime::createFromFormat('!Y-m-d', $dataLeitura);
    $errosData = DateTime::getLastErrors();
    $dataValida = $dataConvertida
        && ($errosData === false || ($errosData['warning_count'] === 0 && $errosData['error_count'] === 0))
        && $dataConvertida->format('Y-m-d') === $dataLeitura;

    if (!$sensorEscolhido) {
        $mensagemErro = 'Selecione um sensor cadastrado.';
    } elseif (!$dataValida) {
        $mensagemErro = 'Informe uma data válida.';
    } elseif ($sensorEscolhido['tipo_dado'] === 'Status' && (mb_strlen($descricaoFalha) < 5 || mb_strlen($descricaoFalha) > 200)) {
        $mensagemErro = 'Descreva a falha com pelo menos 5 caracteres.';
    } elseif ($sensorEscolhido['tipo_dado'] !== 'Status' && ($valorInformado === '' || !is_numeric($valorInformado))) {
        $mensagemErro = 'Informe um valor numérico.';
    } else {
        $dataSql = $dataConvertida->format('Y-m-d H:i:s');

        if ($sensorEscolhido['tipo_dado'] === 'Status') {
            $stmt = $conn->prepare("INSERT INTO falhas_operacionais (id_sensor, descricao, data_registro, resolvida, origem, id_usuario) VALUES (?, ?, ?, 0, 'sensor', NULL)");
            if (!$stmt) {
                error_log('Erro ao preparar registro de falha do sensor: ' . $conn->error);
                $mensagemErro = 'Não foi possível registrar a falha.';
            } else {
                $stmt->bind_param('iss', $sensorSelecionado, $descricaoFalha, $dataSql);
                try {
                    if ($stmt->execute()) {
                        $_SESSION['mensagem_leitura'] = 'Falha registrada com sucesso!';
                        $stmt->close();
                        header('Location: monitoramento.php');
                        exit;
                    }
                    $mensagemErro = 'Não foi possível registrar a falha.';
                } catch (mysqli_sql_exception $e) {
                    error_log('Erro ao registrar falha do sensor: ' . $e->getMessage());
                    $mensagemErro = 'Não foi possível registrar a falha.';
                }
                $stmt->close();
            }
        } else {
            $valorNumero = (float) $valorInformado;
            $limiteSuperior = $sensorEscolhido['tipo_dado'] === 'Velocidade' ? 600 : 150;

            if ($valorNumero < 0 || $valorNumero > $limiteSuperior) {
                $mensagemErro = $sensorEscolhido['tipo_dado'] === 'Velocidade'
                    ? 'A velocidade deve ficar entre 0 e 600 km/h.'
                    : 'A temperatura deve ficar entre 0 e 150 °C.';
            } else {
                $stmt = $conn->prepare('INSERT INTO leituras_sensores (id_sensor, valor, data_leitura) VALUES (?, ?, ?)');
                if (!$stmt) {
                    error_log('Erro ao preparar leitura do sensor: ' . $conn->error);
                    $mensagemErro = 'Não foi possível salvar a leitura.';
                } else {
                    $stmt->bind_param('ids', $sensorSelecionado, $valorNumero, $dataSql);
                    try {
                        if ($stmt->execute()) {
                            $_SESSION['mensagem_leitura'] = 'Leitura cadastrada com sucesso!';
                            $stmt->close();
                            $filtroMonitoramento = $sensorEscolhido['tipo_dado'] === 'Velocidade'
                                ? 'velocidade'
                                : 'temperatura';
                            header('Location: monitoramento.php?'
                                . $filtroMonitoramento . '=' . $sensorSelecionado);
                            exit;
                        }
                        $mensagemErro = 'Não foi possível salvar a leitura.';
                    } catch (mysqli_sql_exception $e) {
                        error_log('Erro ao salvar leitura do sensor: ' . $e->getMessage());
                        $mensagemErro = 'Não foi possível salvar a leitura.';
                    }
                    $stmt->close();
                }
            }
        }
    }

    $valor = $valorInformado;
}

$sensores = $conn->query("SELECT id_sensor, nome_sensor, tipo_dado FROM sensores ORDER BY nome_sensor");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de leituras | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261115-leitura-status">
</head>
<body class="body_geral">
<div class="pagina">
    <aside class="menu">
        <img class="logo-trem" src="https://img.icons8.com/ios/100/701015/train.png" alt="Logo TremTech">
        <p class="marca">SISTEMA TREMTECH</p>
        <a href="../Tela_Home/home.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/home.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>Home</a>
        <a href="../Tela_Usuario/cadastrar_usuario.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/user.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>Usuários</a>
        <a href="../Tela_Sensores/cadastrar_sensores.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>Sensores</a>
        <a href="../Tela_Trens/cadastrar_trens.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/train.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>Trens</a>
        <a href="../Tela_Rotas/cadastrar_rotas.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/map.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>Rotas</a>
        <a class="ativo" href="monitoramento.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/visible.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>Monitoramento</a>
        <a href="../Tela_Falhas/cadastrar_falha.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/701015/error.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/701015/error.png');"></span>Falhas</a>
        <a href="../Tela_Relatorios/relatorios.php"><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/000000/report-card.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>Relatórios</a>
        <a class="logout" href="../../logout.php" data-confirmar-logout><span class="icone" style="mask-image:url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');-webkit-mask-image:url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');"></span>Logout</a>
    </aside>

    <main class="content-user">
        <p class="painel-user">PAINEL DE MONITORAMENTO</p>
        <h1>Registrar leitura ou falha</h1>
        <h2>Informações dos sensores</h2>

        <div class="admin-badge-user">
            <div class="avatar-user"></div>
            <div><strong>Administrador</strong><span><?= escapar($_SESSION['nome'] ?? '') ?></span></div>
        </div>

        <?php if ($mensagemErro !== ''): ?><p class="leitura-mensagem-erro" role="alert"><?= escapar($mensagemErro) ?></p><?php endif; ?>

        <section class="cadastro-usuario ativo">
            <h2>Nova leitura ou falha</h2>
            <?php if ($sensores && $sensores->num_rows > 0): ?>
                <form class="form-cadastro-user" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
                    <div class="campo-user">
                        <label for="id_sensor">Sensor vinculado</label>
                        <select id="id_sensor" name="id_sensor" required>
                            <option value="">Selecione um sensor</option>
                            <?php while ($sensor = $sensores->fetch_assoc()): ?>
                                <option value="<?= (int) $sensor['id_sensor'] ?>" data-tipo="<?= escapar($sensor['tipo_dado']) ?>" <?= $sensorSelecionado === (int) $sensor['id_sensor'] ? 'selected' : '' ?>><?= escapar($sensor['nome_sensor']) ?> (<?= escapar($sensor['tipo_dado']) ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="campo-user">
                        <label for="valor">Valor <span id="unidade">- selecione o sensor -</span></label>
                        <input type="number" id="valor" name="valor" min="0" step="0.01" value="<?= escapar($valor) ?>" required>
                    </div>
                    <div class="campo-user campo-descricao-status" id="campo-descricao-status" hidden>
                        <label for="descricao_falha">Descrição da falha</label>
                        <input type="text" id="descricao_falha" name="descricao_falha" minlength="5" maxlength="200" placeholder="Descreva a falha observada" value="<?= escapar($descricaoFalha) ?>">
                    </div>
                    <div class="campo-user campo-responsavel-trem">
                        <label for="data_leitura">Data da leitura ou ocorrência</label>
                        <input type="date" id="data_leitura" name="data_leitura" value="<?= escapar($dataLeitura) ?>" required>
                    </div>
                    <button class="btn-cadastrar-form" id="botao-salvar-informacao" type="submit">SALVAR LEITURA</button>
                </form>
            <?php else: ?>
                <p class="leitura-mensagem-erro" role="alert">Cadastre um sensor para registrar uma leitura ou falha.</p>
            <?php endif; ?>
        </section>

    </main>
</div>
<script>
(() => {
    const sensor = document.getElementById('id_sensor');
    const valor = document.getElementById('valor');
    const unidade = document.getElementById('unidade');
    const campoDescricao = document.getElementById('campo-descricao-status');
    const descricao = document.getElementById('descricao_falha');
    const botaoSalvar = document.getElementById('botao-salvar-informacao');
    if (!sensor || !valor || !unidade || !campoDescricao || !descricao || !botaoSalvar) return;
    const atualizarCampo = () => {
        const tipo = sensor.options[sensor.selectedIndex]?.dataset.tipo || '';
        if (tipo === 'Status') {
            unidade.textContent = '';
            valor.closest('.campo-user').hidden = true;
            valor.required = false;
            campoDescricao.hidden = false;
            descricao.required = true;
            botaoSalvar.textContent = 'CADASTRAR FALHA';
        } else if (tipo === 'Velocidade') {
            valor.closest('.campo-user').hidden = false;
            valor.required = true;
            campoDescricao.hidden = true;
            descricao.required = false;
            unidade.textContent = '(km/h)';
            valor.max = '600';
            botaoSalvar.textContent = 'SALVAR LEITURA';
        } else if (tipo === 'Temperatura') {
            valor.closest('.campo-user').hidden = false;
            valor.required = true;
            campoDescricao.hidden = true;
            descricao.required = false;
            unidade.textContent = '(°C)';
            valor.max = '150';
            botaoSalvar.textContent = 'SALVAR LEITURA';
        } else {
            valor.closest('.campo-user').hidden = false;
            valor.required = true;
            campoDescricao.hidden = true;
            descricao.required = false;
            unidade.textContent = '- selecione o sensor -';
            valor.removeAttribute('max');
            botaoSalvar.textContent = 'SALVAR LEITURA';
        }
    };
    sensor.addEventListener('change', atualizarCampo);
    atualizarCampo();
})();
</script>
<script src="../../script/confirmar_logout.js?v=20261107"></script>
</body>
</html>
