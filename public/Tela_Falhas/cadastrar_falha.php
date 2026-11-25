<?php
// FALHAS. Funcionários consultam a lista. Administradores também registram, resolvem e reabrem falhas.
// O formulário envia a ação ao PHP. Depois, a página busca os registros para montar a lista.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Funcionários podem consultar as falhas. Só administradores podem alterar os dados.
exigir_login();

$eh_administrador = ($_SESSION['tipo'] === 'Administrador');
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
        $id_falha = (int) $_POST['id_falha'];

        if (isset($_POST['resolver'])) {
            $situacao = 'Resolvida';
        } else {
            $situacao = 'Não resolvida';
        }

        $sql = "UPDATE falhas_operacionais SET situacao = ? WHERE id_falha = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $situacao, $id_falha);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header('Location: cadastrar_falha.php?status=' . $filtro . '#falhas');
        exit();
    }

    $descricao = trim($_POST['descricao']);
    $id_sensor = (int) $_POST['id_sensor'];

    if ($descricao === '' || $id_sensor < 1) {
        $mensagem = 'Preencha a descrição e selecione um sensor.';
    } else {
        $sql = "SELECT id_sensor FROM sensores
                WHERE id_sensor = $id_sensor AND tipo_dado = 'Status'";
        $resultado = mysqli_query($conn, $sql);

        if (mysqli_num_rows($resultado) === 0) {
            $mensagem = 'Selecione um sensor do tipo Status.';
        } else {
            // O INSERT manda sensor, descrição e situação. O banco põe a data atual e a origem sensor.
            $sql = "INSERT INTO falhas_operacionais (id_sensor, descricao, situacao)
                    VALUES (?, ?, 'Não resolvida')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'is', $id_sensor, $descricao);
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
$total_resolvidas = mysqli_num_rows($resultado);

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Não resolvida'";
$resultado = mysqli_query($conn, $sql);
$total_nao_resolvidas = mysqli_num_rows($resultado);

$sql = "SELECT * FROM falhas_operacionais";

if ($filtro === 'resolvidas') {
    $sql .= " WHERE situacao = 'Resolvida'";
} elseif ($filtro === 'nao_resolvidas') {
    $sql .= " WHERE situacao = 'Não resolvida'";
}

// DESC coloca as falhas mais recentes primeiro.
$sql .= " ORDER BY data_registro DESC";
$falhas = mysqli_query($conn, $sql);
// Se o cadastro tiver erro, o formulário continua aberto para a pessoa ver o aviso.
$cadastro_aberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falhas operacionais | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-direto-topo-1">
</head>

<body class="body_geral">
    <!-- A classe ativo destaca o link da tela aberta. -->
    <nav class="menu_topo" aria-label="Navegação do TremTech">
        <a class="menu_topo_marca" href="../Tela_Home/home.php">
            <img src="https://img.icons8.com/ios/100/701015/train.png" alt="">
            <span>SISTEMA TREMTECH</span>
        </a>

        <div class="menu_topo_links">
            <a href="../Tela_Home/home.php"><img src="https://img.icons8.com/ios-filled/18/701015/home.png" alt=""> Home</a>
            <a href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a class="ativo" href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
        </div>

        <a class="menu_topo_sair" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
    </nav>
    <div class="pagina">


        <main class="content_user">
            <p class="painel_user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de falhas operacionais</h1>
            <?php if ($eh_administrador) { ?>
                <h2>Cadastro e acompanhamento</h2>
            <?php } else { ?>
                <h2>Acompanhamento de falhas</h2>
            <?php } ?>

            <div class="admin_badge_user card_recuado">
                <div class="avatar_user"></div>
                <div>
                    <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($eh_administrador) { ?>
                <section id="cadastro" class="cadastro_usuario ativo"
                    <?php if (!$cadastro_aberto) { echo 'hidden'; } ?>>

                    <?php if ($mensagem !== '') { ?>
                        <p><?php echo htmlspecialchars($mensagem); ?></p>
                    <?php } ?>

                    <form method="POST" class="form_cadastro_user">
                        <div class="campo_user">
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

                        <div class="campo_user">
                            <label for="descricao">Descrição da falha</label>
                            <input type="text" name="descricao" id="descricao" placeholder="Descreva a falha observada" maxlength="200" required>
                        </div>

                        <button type="submit" class="btn_cadastrar_form">CADASTRAR FALHA</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Lista de falhas com filtro, contadores e botões de mudança de situação. -->
            <section class="home_caixa_falhas" id="falhas">
                <div class="falhas_cabecalho">
                    <div class="falhas_cabecalho_acoes">
                        <h2 class="home_titulo_tabela">Falhas operacionais</h2>
                        <?php if ($eh_administrador) { ?>
                            <button type="button" id="botao_abrir_cadastro"
                                    class="btn_cadastrar_user btn_cadastrar_user_compacto" aria-controls="cadastro">
                                Cadastrar falha
                            </button>
                        <?php } ?>
                    </div>

                <form method="GET" id="filtro_falhas" class="falhas_filtro">
                    <label for="status">Status</label>
                    <select name="status" id="status">
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
                        <strong><?php echo $total_resolvidas; ?></strong>
                        <span>Resolvidas</span>
                    </div>
                    <div class="falha_contador">
                        <strong><?php echo $total_nao_resolvidas; ?></strong>
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
                        $id_sensor = (int) $falha['id_sensor'];
                        $sql_sensor = "SELECT nome_sensor, id_rotas FROM sensores
                                      WHERE id_sensor = $id_sensor";
                        $resultado_sensor = mysqli_query($conn, $sql_sensor);
                        $sensor_da_falha = mysqli_fetch_assoc($resultado_sensor);

                        // O sensor guarda o ID da rota, e a rota guarda o ID do trem.
                        $id_rota = (int) $sensor_da_falha['id_rotas'];
                        $sql_rota = "SELECT id_trem FROM rotas WHERE id_rotas = $id_rota";
                        $resultado_rota = mysqli_query($conn, $sql_rota);
                        $rota_da_falha = mysqli_fetch_assoc($resultado_rota);

                        $id_trem = (int) $rota_da_falha['id_trem'];
                        $sql_trem = "SELECT nome FROM trens WHERE id_trem = $id_trem";
                        $resultado_trem = mysqli_query($conn, $sql_trem);
                        $trem_da_falha = mysqli_fetch_assoc($resultado_trem);

                        // Formata a data do banco como dia/mês/ano.
                        $data = date('d/m/Y', strtotime($falha['data_registro']));
                    ?>
                        <article class="falha_item">
                            <div class="falha_dados">
                                <h3><?php echo htmlspecialchars($falha['descricao']); ?></h3>
                                <p>Sensor: <?php echo htmlspecialchars($sensor_da_falha['nome_sensor']); ?> - Trem: <?php echo htmlspecialchars($trem_da_falha['nome']); ?> - <?php echo $data; ?></p>
                            </div>

                            <?php if ($eh_administrador) { ?>
                                <form method="POST" class="falha_acao">
                                    <!-- O campo fica escondido na tela, mas envia o número da falha que será resolvida ou reaberta. -->
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