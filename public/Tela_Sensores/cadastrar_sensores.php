
<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_sensores.php";

exigir_login();

$ehAdministrador =
    strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$mensagemSucesso = $_SESSION['mensagem_cadastro'] ?? '';
unset($_SESSION['mensagem_cadastro']);

$avisoExclusao = $_SESSION['aviso_exclusao'] ?? '';
unset($_SESSION['aviso_exclusao']);

$mensagem = '';
$mensagemAcesso = '';
$busca = trim($_GET['buscar'] ?? '');
$idEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT) ?: 0;
$sensorEdicao = null;
$nomeSensor = '';
$tipoDado = '';
$idRotaEdicao = '';

if ($idEdicao > 0 && $ehAdministrador) {
    $carregarEdicao = $conn->prepare('SELECT id_sensor, nome_sensor, tipo_dado, id_rotas FROM sensores WHERE id_sensor = ?');
    $carregarEdicao->bind_param('i', $idEdicao);
    $carregarEdicao->execute();
    $sensorEdicao = $carregarEdicao->get_result()->fetch_assoc();
    $carregarEdicao->close();
    if (!$sensorEdicao) {
        $idEdicao = 0;
        $mensagemAcesso = 'Sensor não encontrado.';
    } else {
        $nomeSensor = $sensorEdicao['nome_sensor'];
        $tipoDado = $sensorEdicao['tipo_dado'];
        $idRotaEdicao = (string) $sensorEdicao['id_rotas'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar sensores.';
    } else {
        verificar_csrf();

        $acao = $_POST['acao'] ?? 'criar';
        validar_acao_crud($conn, $acao, 'sensores', 'id_sensor', 'id_sensor');
        $nomeSensor = trim($_POST['nome_sensor'] ?? '');
        $tipoDado = trim($_POST['tipo_dado'] ?? '');
        $idRota = filter_input(INPUT_POST, 'id_rotas', FILTER_VALIDATE_INT);
        $idRotaEdicao = (string) ($idRota ?: '');
        $idAtualizar = filter_input(INPUT_POST, 'id_sensor', FILTER_VALIDATE_INT) ?: 0;

        $tiposPermitidos = ['Velocidade', 'Temperatura', 'Status'];

        if (
            mb_strlen($nomeSensor) < 3 ||
            mb_strlen($nomeSensor) > 100 ||
            !in_array($tipoDado, $tiposPermitidos, true) ||
            !$idRota
        ) {
            $mensagem = 'Preencha todos os campos corretamente.';
        } else {
            $checkRota = $conn->prepare('SELECT id_rotas FROM rotas WHERE id_rotas = ?');
            $checkRota->bind_param('i', $idRota);
            $checkRota->execute();
            $rotaExiste = $checkRota->get_result()->num_rows === 1;
            $checkRota->close();

            if (!$rotaExiste) {
                $mensagem = 'Selecione uma rota cadastrada.';
            } else {
            $sql = $acao === 'editar'
                ? 'UPDATE sensores SET nome_sensor = ?, tipo_dado = ?, id_rotas = ? WHERE id_sensor = ?'
                : 'INSERT INTO sensores (nome_sensor, tipo_dado, id_rotas) VALUES (?, ?, ?)';
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                error_log('Erro ao preparar cadastro de sensor: ' . $conn->error);
                $mensagem = 'Não foi possível concluir o cadastro.';
            } else {
                if ($acao === 'editar') {
                    $stmt->bind_param('ssii', $nomeSensor, $tipoDado, $idRota, $idAtualizar);
                } else {
                    $stmt->bind_param('ssi', $nomeSensor, $tipoDado, $idRota);
                }

                try {
                    if ($stmt->execute()) {
                        $_SESSION['mensagem_cadastro'] = $acao === 'editar' ? 'Sensor atualizado com sucesso!' : 'Sensor cadastrado com sucesso!';
                        header('Location: cadastrar_sensores.php');
                        exit;
                    }
                } catch (mysqli_sql_exception $e) {
                    error_log('Erro ao cadastrar sensor: ' . $e->getMessage());
                }

                $mensagem = 'Não foi possível concluir o cadastro. Confira os dados selecionados.';
                $stmt->close();
            }
            }
        }
    }
}

// Buscar rotas
$sqlRotas = "SELECT id_rotas, nome FROM rotas ORDER BY nome";
$resultadoRotas = $conn->query($sqlRotas);

if (!$resultadoRotas) {
    error_log('Erro ao carregar rotas: ' . $conn->error);
    $mensagemAcesso = 'Não foi possível carregar as rotas.';
}

// Pesquisa de sensores
$textoBusca = '%' . $busca . '%';

$sql = "
    SELECT
        s.id_sensor,
        s.nome_sensor,
        s.tipo_dado,
        r.nome AS rota_vinculada
    FROM sensores AS s
    LEFT JOIN rotas AS r ON r.id_rotas = s.id_rotas
    WHERE s.nome_sensor LIKE ?
       OR s.tipo_dado LIKE ?
       OR r.nome LIKE ?
    ORDER BY s.id_sensor
";

$stmtBusca = $conn->prepare($sql);

if ($stmtBusca) {
    $stmtBusca->bind_param('sss', $textoBusca, $textoBusca, $textoBusca);

    if ($stmtBusca->execute()) {
        $resultado = $stmtBusca->get_result();
    } else {
        error_log('Erro ao consultar sensores: ' . $stmtBusca->error);
        $resultado = false;
        $mensagemAcesso = 'Não foi possível carregar os sensores.';
    }

    $stmtBusca->close();
} else {
    error_log('Erro ao preparar consulta dos sensores: ' . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível preparar a consulta dos sensores.';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro Sensores</title>

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

        <a class="ativo" href="../../public/Tela_Sensores/cadastrar_sensores.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
            Sensores
        </a>

        <a href="../../public/Tela_Trens/cadastrar_trens.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>
            Trens
        </a>

        <a href="../../public/Tela_Rotas/cadastrar_rotas.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>
            Rotas
        </a>

        <a href="../../public/Tela_Monitoramento/monitoramento.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>
            Monitoramento
        </a>

        <a href="../../public/Tela_Falhas/cadastrar_falha.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png');"></span>
            Falhas
        </a>

        <a href="../../public/Tela_Relatorios/relatorios.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>
            Relatórios
        </a>


        <a class="logout" href="../../logout.php" data-confirmar-logout><span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');"></span>Logout</a>
    </aside>

    <main class="content-user">

        <p class="painel-user">PAINEL ADMINISTRATIVO</p>

        <h1>Controle de sensores cadastrados</h1>
        <h2>Sensores do sistema</h2>

        <?php if ($mensagemSucesso !== ''): ?>
            <p class="mensagem-sucesso" role="status">
                <?= escapar($mensagemSucesso) ?>
            </p>
        <?php endif; ?>

        <?php if ($mensagemAcesso !== ''): ?>
            <p role="alert"><?= escapar($mensagemAcesso) ?></p>
        <?php endif; ?>
        <div class="admin-badge-user">

            <div class="avatar-user"></div>

            <div>
                <strong>
                    <?= $ehAdministrador ? 'Administrador' : 'Funcionário' ?>
                </strong>

                <span><?= escapar($_SESSION['nome'] ?? '') ?></span>
            </div>

        </div>

        <?php if ($ehAdministrador): ?>

            <section class="cadastro-usuario <?= $idEdicao > 0 ? 'ativo' : '' ?>" id="cadastro" <?= $idEdicao > 0 ? '' : 'hidden' ?>>
                <h2><?= $idEdicao > 0 ? 'Editar sensor' : 'Cadastrar novo sensor' ?></h2>

                <form
                    class="form-cadastro-user"
                    method="POST"
                    onsubmit="return validar_sensor()">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapar(token_csrf()) ?>">
                    <input type="hidden" name="acao" value="<?= $idEdicao > 0 ? 'editar' : 'criar' ?>">
                    <?php if ($idEdicao > 0): ?><input type="hidden" name="id_sensor" value="<?= (int) $idEdicao ?>"><?php endif; ?>

                    <div class="campo-user">

                        <label for="nome_sensor">Nome do sensor</label>

                        <input
                            type="text"
                            id="nome_sensor"
                            name="nome_sensor"
                            placeholder="Nome do sensor"
                            maxlength="100"
                            value="<?= escapar($nomeSensor) ?>"
                            required>

                    </div>

                    <div class="campo-user">

                        <label for="tipo_dado">Tipo de dado coletado</label>

                        <select id="tipo_dado" name="tipo_dado" required>

                            <option value="">Selecione</option>
                            <option value="Velocidade" <?= $tipoDado === 'Velocidade' ? 'selected' : '' ?>>Velocidade</option>
                            <option value="Temperatura" <?= $tipoDado === 'Temperatura' ? 'selected' : '' ?>>Temperatura</option>
                            <option value="Status" <?= $tipoDado === 'Status' ? 'selected' : '' ?>>Status</option>

                        </select>

                    </div>

                    <div class="campo-user campo-rota-sensor">

                            <label for="id_rotas">Rota vinculada</label>

                        <select id="id_rotas" name="id_rotas" required>

                            <option value="">Selecione uma rota</option>

                            <?php if ($resultadoRotas): ?>

                                <?php while ($rota = $resultadoRotas->fetch_assoc()): ?>

                                    <option value="<?= (int) $rota['id_rotas'] ?>" <?= (string) $rota['id_rotas'] === $idRotaEdicao ? 'selected' : '' ?>>
                                        <?= escapar($rota['nome']) ?>
                                    </option>

                                <?php endwhile; ?>

                            <?php endif; ?>

                        </select>

                    </div>

                    <?php if ($mensagem !== ''): ?>

                        <p role="alert"><?= escapar($mensagem) ?></p>

                    <?php endif; ?>

                    <?php if ($idEdicao > 0): ?>
                        <div class="acoes-form-edicao modo-edicao">
                            <button class="btn-cadastrar-form" type="submit">SALVAR ALTERAÇÕES</button>
                            <a class="btn-cancelar-form" href="cadastrar_sensores.php">Cancelar</a>
                        </div>
                    <?php else: ?>
                        <button class="btn-cadastrar-form" type="submit">CADASTRAR NOVO SENSOR</button>
                    <?php endif; ?>

                </form>

            </section>

        <?php endif; ?>

        <section class="buscar-box-user buscar-usuarios-linha">

            <form method="GET">

                <label for="buscar">Buscar</label>

                <input
                    type="text"
                    id="buscar"
                    name="buscar"
                    placeholder="Nome, tipo ou rota"
                    value="<?= escapar($busca) ?>">

                <button type="submit">Buscar</button>

            </form>

            <?php if ($ehAdministrador): ?>
                <?php if ($idEdicao === 0): ?><button class="btn-cadastrar-user btn-cadastrar-user-compacto" id="btnAbrirCadastro"
                        type="button" aria-expanded="false" aria-controls="cadastro">
                    Cadastrar sensor
                </button><?php endif; ?>
            <?php endif; ?>

        </section>

        <?php mostrar_sensores($resultado); ?>

    </main>

</div>

<?php if ($avisoExclusao !== ''): ?>

    <div class="modal-aviso" id="modalAviso">

        <div class="conteudo-aviso">

            <div class="icone-aviso">!</div>

            <h2>Não foi possível excluir o sensor</h2>

            <p><?= escapar($avisoExclusao) ?></p>

            <button type="button" onclick="fecharAviso()">
                Voltar
            </button>

        </div>

    </div>

<?php endif; ?>

<script>

function fecharAviso() {
    const modal = document.getElementById("modalAviso");

    if (modal) {
        modal.remove();
    }
}

</script>

<script src="../../script/validacao.js"></script>

<script src="../../script/confirmar_logout.js?v=20261107"></script>
</body>
</html>

