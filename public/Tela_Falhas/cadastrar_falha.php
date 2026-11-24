<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Funcionários podem consultar as falhas. Só administradores podem alterar os dados.
exigir_login();

$ehAdministrador = ($_SESSION['tipo'] === 'Administrador');
$mensagem = '';
$filtro = 'todas';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'resolvidas' || $_GET['status'] === 'nao_resolvidas') {
        $filtro = $_GET['status'];
    }
}

// O envio pode ser um cadastro novo ou um pedido para resolver ou reabrir uma falha.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A função do auth.php permite que só o administrador faça alterações.
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

        header('Location: cadastrar_falha.php?status=' . $filtro . '#falhas');
        exit();
    }

    $descricao = trim($_POST['descricao']);
    $idSensor = (int) $_POST['id_sensor'];

    if ($descricao === '' || $idSensor < 1) {
        $mensagem = 'Preencha a descrição e selecione um sensor.';
    } else {
        $sql = "SELECT id_sensor FROM sensores
                WHERE id_sensor = $idSensor AND tipo_dado = 'Status'";
        $resultado = mysqli_query($conn, $sql);

        if (mysqli_num_rows($resultado) === 0) {
            $mensagem = 'Selecione um sensor do tipo Status.';
        } else {
            // O banco preenche a data e a origem com os valores padrão.
            $sql = "INSERT INTO falhas_operacionais (id_sensor, descricao, situacao)
                    VALUES (?, ?, 'Não resolvida')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'is', $idSensor, $descricao);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header('Location: cadastrar_falha.php#falhas');
            exit();
        }
    }
}

$sql = "SELECT id_sensor, nome_sensor FROM sensores
        WHERE tipo_dado = 'Status' ORDER BY nome_sensor";
$sensores = mysqli_query($conn, $sql);

// mysqli_num_rows conta quantos registros a consulta encontrou.
$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Resolvida'";
$resultado = mysqli_query($conn, $sql);
$totalResolvidas = mysqli_num_rows($resultado);

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Não resolvida'";
$resultado = mysqli_query($conn, $sql);
$totalNaoResolvidas = mysqli_num_rows($resultado);

$sql = "SELECT * FROM falhas_operacionais";

if ($filtro === 'resolvidas') {
    $sql .= " WHERE situacao = 'Resolvida'";
} elseif ($filtro === 'nao_resolvidas') {
    $sql .= " WHERE situacao = 'Não resolvida'";
}

// DESC coloca as falhas mais recentes primeiro.
$sql .= " ORDER BY data_registro DESC";
$falhas = mysqli_query($conn, $sql);
// Mantém o formulário aberto para mostrar um erro de cadastro.
$cadastroAberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falhas operacionais | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=cadastro-3">
</head>

<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user">
            <p class="painel-user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de falhas operacionais</h1>
            <?php if ($ehAdministrador) { ?>
                <h2>Cadastro e acompanhamento</h2>
            <?php } else { ?>
                <h2>Acompanhamento de falhas</h2>
            <?php } ?>

            <div class="admin-badge-user card-recuado">
                <div class="avatar-user"></div>
                <div>
                    <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($ehAdministrador) { ?>
                <section id="cadastro" class="cadastro-usuario<?php if ($cadastroAberto) { echo ' ativo'; } ?>"
                    <?php if (!$cadastroAberto) { echo 'hidden'; } ?>>

                    <?php if ($mensagem !== '') { ?>
                        <p><?php echo htmlspecialchars($mensagem); ?></p>
                    <?php } ?>

                    <form method="POST" class="form-cadastro-user">
                        <div class="campo-user">
                            <label for="id_sensor">Sensor de status relacionado</label>
                            <select name="id_sensor" id="id_sensor" required>
                                <option value="">Selecione um sensor</option>
    
                                <?php while ($sensor = mysqli_fetch_assoc($sensores)) { ?>
                                    <option value="<?php echo $sensor['id_sensor']; ?>">
                                        <?php echo htmlspecialchars($sensor['nome_sensor']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="campo-user">
                            <label for="descricao">Descrição da falha</label>
                            <input type="text" name="descricao" id="descricao" placeholder="Descreva a falha observada" maxlength="200" required>
                        </div>

                        <button type="submit" class="btn-cadastrar-form">CADASTRAR FALHA</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Lista de falhas com filtro, contadores e botões de mudança de situação. -->
            <section class="home_caixa_falhas" id="falhas">
                <div class="falhas_cabecalho">
                    <div class="falhas-cabecalho-acoes">
                        <h2 class="home_titulo_tabela">Falhas operacionais</h2>
                        <?php if ($ehAdministrador) { ?>
                            <button type="button" id="btnAbrirCadastro"
                                    class="btn-cadastrar-user btn-cadastrar-user-compacto" aria-controls="cadastro" aria-expanded="<?php echo $cadastroAberto ? 'true' : 'false'; ?>">
                                Cadastrar falha
                            </button>
                        <?php } ?>
                    </div>

                <form method="GET" class="falhas_filtro">
                    <label for="status">Status</label>
                    <select name="status" id="status" data-filtrar-falhas>
                        <option value="todas">Todas</option>
                        <option value="resolvidas"
                            <?php if ($filtro === 'resolvidas') { echo 'selected'; } ?>>
                            Resolvidas
                        </option>
                        <option value="nao_resolvidas"
                            <?php if ($filtro === 'nao_resolvidas') { echo 'selected'; } ?>>
                            Não resolvidas
                        </option>
                    </select>
                </form>
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
                        <p>Nenhuma falha encontrada.</p>
                    <?php } ?>

                    <?php
                    while ($falha = mysqli_fetch_assoc($falhas)) {
                        // A falha guarda o ID do sensor.
                        $idSensor = (int) $falha['id_sensor'];
                        $sqlSensor = "SELECT nome_sensor, id_rotas FROM sensores
                                      WHERE id_sensor = $idSensor";
                        $resultadoSensor = mysqli_query($conn, $sqlSensor);
                        $sensorDaFalha = mysqli_fetch_assoc($resultadoSensor);

                        // O sensor guarda o ID da rota, e a rota guarda o ID do trem.
                        $idRota = (int) $sensorDaFalha['id_rotas'];
                        $sqlRota = "SELECT id_trem FROM rotas WHERE id_rotas = $idRota";
                        $resultadoRota = mysqli_query($conn, $sqlRota);
                        $rotaDaFalha = mysqli_fetch_assoc($resultadoRota);

                        $idTrem = (int) $rotaDaFalha['id_trem'];
                        $sqlTrem = "SELECT nome FROM trens WHERE id_trem = $idTrem";
                        $resultadoTrem = mysqli_query($conn, $sqlTrem);
                        $tremDaFalha = mysqli_fetch_assoc($resultadoTrem);

                        // Formata a data do banco como dia/mês/ano.
                        $data = date('d/m/Y', strtotime($falha['data_registro']));
                    ?>
                        <article class="falha_item">
                            <div class="falha_dados">
                                <h3><?php echo htmlspecialchars($falha['descricao']); ?></h3>
                                <p>Sensor: <?php echo htmlspecialchars($sensorDaFalha['nome_sensor']); ?> - Trem: <?php echo htmlspecialchars($tremDaFalha['nome']); ?> - <?php echo $data; ?></p>
                            </div>

                            <?php if ($ehAdministrador) { ?>
                                <form method="POST" class="falha_acao">
                                    <!-- Este campo envia o ID da falha escolhida ao PHP. -->
                                    <input type="hidden" name="id_falha" value="<?php echo $falha['id_falha']; ?>">

                                    <?php if ($falha['situacao'] === 'Resolvida') { ?>
                                        <button type="submit" name="reabrir" class="botao_reabrir">
                                            Reabrir
                                        </button>
                                    <?php } else { ?>
                                        <button type="submit" name="resolver" class="botao_resolver">
                                            Marcar como resolvida
                                        </button>
                                    <?php } ?>
                                </form>
                            <?php } ?>
                        </article>
                    <?php } ?>
                </div>
            </section>
        </main>
    </div>

<script src="../../script/validacao.js?v=cadastro-3"></script>
</body>
</html>