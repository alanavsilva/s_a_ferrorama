<?php
// O PHP valida e salva o formulário, e a tabela mostra os registros do banco.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Quem fez login pode ver a tabela. Somente administradores podem enviar o cadastro.
exigir_login();

$administrador = $_SESSION['tipo'] === 'Administrador';
$tipos = ['Velocidade', 'Temperatura', 'Status'];
$mensagem = $_SESSION['mensagem_sensor'] ?? '';
// A sessão guarda o recado até a página abrir. Depois, o recado é limpo.
$sucesso = $_SESSION['sucesso_sensor'] ?? false;
unset($_SESSION['mensagem_sensor'], $_SESSION['sucesso_sensor']);

// O ID no endereço indica que o formulário está em modo de edição.
$editando = isset($_GET['editar']);

if (!$editando && $_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    $nome = trim($_POST['nome_sensor'] ?? '');
    $tipo = $_POST['tipo_dado'] ?? '';
    $id_rota = (int) ($_POST['id_rotas'] ?? 0);

    $sql = "SELECT id_rotas FROM rotas WHERE id_rotas = $id_rota";
    $rota_encontrada = mysqli_query($conn, $sql);

    // in_array confere se o tipo enviado está na lista de tipos do banco.
    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100 || !in_array($tipo, $tipos)) {
        $mensagem = 'Informe um nome válido e selecione o tipo do sensor.';
    } elseif (mysqli_num_rows($rota_encontrada) === 0) {
        $mensagem = 'Selecione uma rota cadastrada.';
    } else {
        $sql = "INSERT INTO sensores (nome_sensor, tipo_dado, id_rotas) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssi', $nome, $tipo, $id_rota);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_sensor'] = 'Sensor cadastrado com sucesso!';
    
            $_SESSION['sucesso_sensor'] = true;
            header('Location: cadastrar_sensores.php');
    
            exit();
        }

        $mensagem = "Não foi possível concluir o cadastro.";
    }
}

// Listagem. Busca os dados para a tabela.
$busca = trim($_GET['buscar'] ?? '');
$rotas = mysqli_query($conn, "SELECT id_rotas, nome FROM rotas ORDER BY nome");
$sensores = mysqli_query($conn, "SELECT * FROM sensores ORDER BY id_sensor");
// O PHP carrega ou salva a edição e mantém o formulário aberto se houver erro.
if ($editando) {
    $sucesso = false;
    require 'editar_s.php';
}

$cadastro_aberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensores | TremTech</title>
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
            <a class="ativo" href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
        </div>

        <a class="menu_topo_sair" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
    </nav>
    <div class="pagina">


        <main class="content_user">
            <p class="painel_user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de sensores cadastrados</h1>
            <h2>Sensores do sistema</h2>

            <div class="admin_badge_user card_recuado">
                <div class="avatar_user"></div>
                <div>
                    <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($mensagem !== '') { ?>
                <?php if ($sucesso) { ?>
                    <p class="mensagem_sucesso"><?php echo escapar($mensagem); ?></p>
                <?php } else { ?>
                    <p class="mensagem_erro" role="alert"><?php echo escapar($mensagem); ?></p>
                <?php } ?>
            <?php } ?>

            <?php if ($editando) { ?>
                <section id="cadastro" class="cadastro_usuario ativo">
                    <h2>Editar sensor</h2>
                    <!-- Cada campo junta o rótulo e a caixa de entrada. O CSS usa esse grupo para colocar o rótulo em cima. -->
                    <form method="POST" class="form_cadastro_user">
                        <div class="campo_user">
                            <label for="nome_sensor">Nome do sensor</label>
                            <input type="text" id="nome_sensor" name="nome_sensor" minlength="3" maxlength="100"
                                   value="<?php echo escapar($sensor['nome_sensor']); ?>" required>
                        </div>

                        <div class="campo_user">
                            <label for="tipo_dado">Tipo de dado</label>
                            <select id="tipo_dado" name="tipo_dado" required>
                                <?php foreach ($tipos as $tipo) { ?>
                                    <option value="<?php echo $tipo; ?>"
                                        <?php if ($tipo === $sensor['tipo_dado']) { echo 'selected'; } ?>>
                                        <?php echo $tipo; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="campo_user campo_rota_sensor">
                            <label for="id_rotas">Rota vinculada</label>
                            <select id="id_rotas" name="id_rotas" required>
                                <?php while ($rota = mysqli_fetch_assoc($rotas)) { ?>
                                    <option value="<?php echo $rota['id_rotas']; ?>"
                                        <?php if ($rota['id_rotas'] == $sensor['id_rotas']) { echo 'selected'; } ?>>
                                        <?php echo escapar($rota['nome']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="acoes_form_edicao modo_edicao">
                            <button type="submit" class="btn_cadastrar_form">Salvar alterações</button>
                            <a class="btn_cancelar_form" href="cadastrar_sensores.php">Cancelar</a>
                        </div>
                    </form>
                </section>
            <?php } elseif ($administrador) { ?>
                <section id="cadastro" class="cadastro_usuario ativo"
                    <?php if (!$cadastro_aberto) { echo 'hidden'; } ?>>
                    <h2>Cadastrar novo sensor</h2>

                    <form method="POST" class="form_cadastro_user">
                        <div class="campo_user">
                            <label for="nome_sensor">Nome do sensor</label>
                            <input placeholder="Nome do sensor" type="text" id="nome_sensor" name="nome_sensor"
                                   minlength="3" maxlength="100" required>
                        </div>

                        <div class="campo_user">
                            <label for="tipo_dado">Tipo de dado coletado</label>
                            <select id="tipo_dado" name="tipo_dado" required>
                                <option value="">Selecione</option>
                                <?php foreach ($tipos as $tipo) { ?>
                                    <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="campo_user campo_rota_sensor">
                            <label for="id_rotas">Rota vinculada</label>
                            <select id="id_rotas" name="id_rotas" required>
                                <option value="">Selecione uma rota</option>
                                <?php while ($rota = mysqli_fetch_assoc($rotas)) { ?>
                                    <option value="<?php echo $rota['id_rotas']; ?>">
                                        <?php echo escapar($rota['nome']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <button type="submit" class="btn_cadastrar_form">CADASTRAR NOVO SENSOR</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Nesta área, Buscar filtra a lista e o botão Cadastrar abre o formulário. -->
            <section class="buscar_box_user buscar_usuarios_linha">
                <form method="GET">
                    <label for="buscar">Buscar</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome, tipo ou rota"
                           value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($administrador && !$editando) { ?>
                    <button class="btn_cadastrar_user btn_cadastrar_user_compacto"
                            id="botao_abrir_cadastro" type="button" aria-controls="cadastro">
                        Cadastrar sensor
                    </button>
                <?php } ?>
            </section>

            <?php include 'visualizacao_sensores.php'; ?>
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

    <!-- A janela só abre ao clicar na lixeira. Os botões usam o mesmo estilo da confirmação de saída. -->
    <dialog class="modal_confirmacao" id="janela_exclusao" aria-labelledby="titulo_exclusao">
        <div class="modal_confirmacao_icone" aria-hidden="true">?</div>
        <h2 id="titulo_exclusao">Excluir registro?</h2>
        <p class="modal_confirmacao_mensagem">Tem certeza de que deseja excluir este registro?</p>
        <div class="modal_confirmacao_acoes">
            <button type="button" class="modal_btn_cancelar" id="cancelar_exclusao">Cancelar</button>
            <button type="button" class="modal_btn_sair" id="confirmar_exclusao">Excluir</button>
        </div>
    </dialog>

<script src="../../script/validacao.js?v=simples-8"></script>
</body>
</html>