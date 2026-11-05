<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_login();

$ehAdministrador = strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;
$nome = $_SESSION['nome'] ?? 'Usuário';
$mensagem = '';
$mensagemSucesso = $_SESSION['mensagem_cadastro'] ?? '';
unset($_SESSION['mensagem_cadastro']);
$descricao = '';
$idSensor = '';
$statusFiltro = $_GET['status'] ?? 'todas';

if (!in_array($statusFiltro, ['todas', 'resolvidas', 'nao_resolvidas'], true)) {
    $statusFiltro = 'todas';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_falha'])) {
    exigir_administrador();
    verificar_csrf();
    $idFalha = filter_input(INPUT_POST, 'id_falha', FILTER_VALIDATE_INT);
    $acaoFalha = $_POST['acao_falha'];

    if ($idFalha && $idFalha > 0 && in_array($acaoFalha, ['resolver', 'reabrir'], true)) {
        $resolvida = $acaoFalha === 'resolver' ? 1 : 0;
        $stmt = $conn->prepare('UPDATE falhas_operacionais SET resolvida = ? WHERE id_falha = ?');
        if ($stmt) {
            $stmt->bind_param('ii', $resolvida, $idFalha);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: cadastrar_falha.php?status=' . urlencode($statusFiltro) . '#falhas');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$ehAdministrador) {
        http_response_code(403);
        $mensagem = 'Você não tem permissão para cadastrar falhas.';
    } else {
        verificar_csrf();
        $descricao = trim($_POST['descricao'] ?? '');
        $idSensor = filter_var($_POST['id_sensor'] ?? '', FILTER_VALIDATE_INT);

        if ($descricao === '' || mb_strlen($descricao) < 5 || mb_strlen($descricao) > 200 || !$idSensor || $idSensor < 1) {
            $mensagem = 'Informe uma descrição válida e selecione um sensor.';
        } else {
            $check = $conn->prepare('SELECT id_sensor FROM sensores WHERE id_sensor = ?');
            if (!$check) {
                error_log('Erro ao preparar validação de sensor: ' . $conn->error);
                $mensagem = 'Não foi possível validar o sensor.';
            } else {
                $check->bind_param('i', $idSensor);
                $check->execute();
                $sensorExiste = $check->get_result()->num_rows === 1;
                $check->close();

                if (!$sensorExiste) {
                    $mensagem = 'Selecione um sensor cadastrado.';
                } else {
                    $stmt = $conn->prepare(
                        'INSERT INTO falhas_operacionais (id_sensor, descricao, resolvida)
                         VALUES (?, ?, 0)'
                    );
                    if (!$stmt) {
                        error_log('Erro ao preparar cadastro de falha: ' . $conn->error);
                        $mensagem = 'Não foi possível cadastrar a falha.';
                    } else {
                        $stmt->bind_param('is', $idSensor, $descricao);
                        try {
                            if ($stmt->execute()) {
                                $_SESSION['mensagem_cadastro'] = 'Falha cadastrada com sucesso!';
                                $stmt->close();
                                header('Location: cadastrar_falha.php?status=todas#falhas');
                                exit;
                            }
                            error_log('Erro ao cadastrar falha: ' . $stmt->error);
                            $mensagem = 'Não foi possível cadastrar a falha.';
                        } catch (mysqli_sql_exception $e) {
                            error_log('Erro ao cadastrar falha: ' . $e->getMessage());
                            $mensagem = 'Não foi possível cadastrar a falha.';
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
}

$manterFormularioAberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
$sensores = $conn->query('SELECT id_sensor, nome_sensor FROM sensores ORDER BY nome_sensor');
if (!$sensores) {
    error_log('Erro ao carregar sensores para cadastro de falha: ' . $conn->error);
    $mensagem = 'Não foi possível carregar os sensores.';
}

$totalResolvidas = (int) ($conn->query('SELECT COUNT(*) AS total FROM falhas_operacionais WHERE resolvida = 1')->fetch_assoc()['total'] ?? 0);
$totalNaoResolvidas = (int) ($conn->query('SELECT COUNT(*) AS total FROM falhas_operacionais WHERE resolvida = 0')->fetch_assoc()['total'] ?? 0);
$sqlFalhas = 'SELECT f.id_falha, f.descricao, f.data_registro, f.resolvida,
                     s.nome_sensor, t.nome AS nome_trem
              FROM falhas_operacionais AS f
              LEFT JOIN sensores AS s ON f.id_sensor = s.id_sensor
              LEFT JOIN trens AS t ON s.id_trem = t.id_trem';
if ($statusFiltro === 'resolvidas') {
    $sqlFalhas .= ' WHERE f.resolvida = 1';
} elseif ($statusFiltro === 'nao_resolvidas') {
    $sqlFalhas .= ' WHERE f.resolvida = 0';
}
$sqlFalhas .= ' ORDER BY f.data_registro DESC';
$faltas = $conn->query($sqlFalhas);
if (!$faltas) {
    error_log('Erro ao consultar falhas: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falhas operacionais | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261105-2">
</head>
<body class="body_geral">
<div class="pagina">
    <aside class="menu">
        <img class="logo-trem" src="https://img.icons8.com/ios/100/ffffff/train.png" alt="Logo TremTech">
        <p class="marca">SISTEMA TREMTECH</p>
        <a href="../Tela_Home/home.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
            Home
        </a>
        <a href="../Tela_Usuario/cadastrar_usuario.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
            Usuários
        </a>
        <a href="../Tela_Sensores/cadastrar_sensores.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
            Sensores
        </a>
        <a href="../Tela_Trens/cadastrar_trens.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>
            Trens
        </a>
        <a href="../Tela_Rotas/cadastrar_rotas.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>
            Rotas
        </a>
        <a href="../Tela_Monitoramento/monitoramento.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>
            Monitoramento
        </a>
        <a class="ativo" href="cadastrar_falha.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/error.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/error.png');"></span>
            Falhas
        </a>
        <a href="../Tela_Relatorios/relatorios.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>
            Relatórios
        </a>
        <a class="logout" href="../../logout.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png');"></span>
            Logout
        </a>
    </aside>

    <main class="content-user">
        <p class="painel-user">PAINEL ADMINISTRATIVO</p>
        <h1>Controle de falhas operacionais</h1>
        <h2><?= $ehAdministrador ? 'Cadastro e acompanhamento' : 'Acompanhamento de falhas' ?></h2>

        <?php if ($mensagemSucesso !== ''): ?>
            <p class="mensagem-sucesso" role="status"><?= escapar($mensagemSucesso) ?></p>
        <?php endif; ?>
        <?php if ($mensagem !== ''): ?>
            <p role="alert"><?= escapar($mensagem) ?></p>
        <?php endif; ?>

        <div class="admin-badge-user">
            <div class="avatar-user"></div>
            <div>
                <strong><?= $ehAdministrador ? 'Administrador' : 'Funcionário' ?></strong>
                <span><?= escapar($nome) ?></span>
            </div>
        </div>

        <?php if ($ehAdministrador): ?>
            <button class="btn-cadastrar-user" id="btnAbrirCadastro" type="button"
                    aria-expanded="<?= $manterFormularioAberto ? 'true' : 'false' ?>" aria-controls="cadastro">
                CADASTRAR NOVA FALHA
            </button>
            <section class="cadastro-usuario <?= $manterFormularioAberto ? 'ativo' : '' ?>" id="cadastro" <?= $manterFormularioAberto ? '' : 'hidden' ?>>
                <form class="form-cadastro-user" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
                    <div class="campo-user">
                        <label for="id_sensor">Sensor relacionado</label>
                        <select id="id_sensor" name="id_sensor" required>
                            <option value="">Selecione um sensor</option>
                            <?php if ($sensores): ?>
                                <?php while ($sensor = $sensores->fetch_assoc()): ?>
                                    <option value="<?= (int)$sensor['id_sensor'] ?>" <?= (string)$idSensor === (string)$sensor['id_sensor'] ? 'selected' : '' ?>><?= escapar($sensor['nome_sensor']) ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="campo-user">
                        <label for="descricao">Descrição da falha</label>
                        <input type="text" id="descricao" name="descricao" minlength="5" maxlength="200"
                               placeholder="Descreva a falha observada" value="<?= escapar($descricao) ?>" required>
                    </div>
                    <button class="btn-cadastrar-form" type="submit">CADASTRAR FALHA</button>
                </form>
            </section>
        <?php endif; ?>

        <section class="home_caixa_falhas" id="falhas">
            <div class="falhas_cabecalho">
                <h2 class="home_titulo_tabela">Falhas operacionais</h2>
                <form method="GET" class="falhas_filtro">
                    <label for="status">Status</label>
                    <select id="status" name="status" onchange="this.form.submit()">
                        <option value="todas" <?= $statusFiltro === 'todas' ? 'selected' : '' ?>>Todas</option>
                        <option value="resolvidas" <?= $statusFiltro === 'resolvidas' ? 'selected' : '' ?>>Resolvidas</option>
                        <option value="nao_resolvidas" <?= $statusFiltro === 'nao_resolvidas' ? 'selected' : '' ?>>Não resolvidas</option>
                    </select>
                </form>
            </div>
            <div class="falhas_resumo">
                <div class="falha_contador"><strong><?= $totalResolvidas ?></strong><span>Resolvidas</span></div>
                <div class="falha_contador"><strong><?= $totalNaoResolvidas ?></strong><span>Não resolvidas</span></div>
            </div>
            <div class="falhas_lista">
                <?php if ($faltas && $faltas->num_rows > 0): ?>
                    <?php while ($falha = $faltas->fetch_assoc()): ?>
                        <article class="falha_item">
                            <div class="falha_dados">
                                <h3><?= escapar($falha['descricao']) ?></h3>
                                <p>Sensor: <?= escapar($falha['nome_sensor'] ?? 'Removido') ?>
                                    — Trem: <?= escapar($falha['nome_trem'] ?? 'Não identificado') ?>
                                    — <?= escapar(date('d/m/Y H:i', strtotime($falha['data_registro']))) ?>
                                </p>
                            </div>
                            <?php if ($ehAdministrador): ?>
                                <form method="POST" class="falha_acao">
                                    <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
                                    <input type="hidden" name="id_falha" value="<?= (int)$falha['id_falha'] ?>">
                                    <?php if ($falha['resolvida']): ?>
                                        <button type="submit" name="acao_falha" value="reabrir" class="botao_reabrir">Reabrir</button>
                                    <?php else: ?>
                                        <button type="submit" name="acao_falha" value="resolver" class="botao_resolver">Marcar como resolvida</button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="falhas_vazias">Nenhuma falha encontrada.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<?php if ($ehAdministrador): ?>
<script>
(() => {
    const botao = document.getElementById('btnAbrirCadastro');
    const formulario = document.getElementById('cadastro');
    if (!botao || !formulario) return;
    botao.addEventListener('click', () => {
        formulario.hidden = !formulario.hidden;
        formulario.classList.toggle('ativo', !formulario.hidden);
        botao.setAttribute('aria-expanded', String(!formulario.hidden));
    });
})();
</script>
<?php endif; ?>
</body>
</html>

