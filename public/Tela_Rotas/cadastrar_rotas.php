<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Quem fez login pode ver a tabela. Somente administradores podem enviar o cadastro.
exigir_login();

$administrador = $_SESSION['tipo'] === 'Administrador';
$mensagem = $_SESSION['mensagem_rota'] ?? '';
unset($_SESSION['mensagem_rota']);

// Cadastro. Recebe o formulário e faz o INSERT no banco.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $extensao = (float) ($_POST['extensao'] ?? 0);
    $tempo = (int) ($_POST['tempo'] ?? 0);
    $id_trem = (int) ($_POST['id_trem'] ?? 0);

    $sql = "SELECT id_trem FROM trens WHERE id_trem = $id_trem";
    $trem_encontrado = mysqli_query($conn, $sql);

    if ($nome === '' || $descricao === '' || $extensao <= 0 || $tempo <= 0) {
        $mensagem = 'Preencha os campos com valores válidos.';
    } elseif (mysqli_num_rows($trem_encontrado) === 0) {
        $mensagem = 'Selecione um trem cadastrado.';
    } else {
        $sql = "INSERT INTO rotas (nome, descricao, extensao, tempo, id_trem)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssdii', $nome, $descricao, $extensao, $tempo, $id_trem);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_rota'] = 'Rota cadastrada com sucesso!';    
            header('Location: cadastrar_rotas.php');    
            exit();
        }

        $mensagem = "Não foi possível concluir o cadastro.";
    }
}

// Listagem. Busca os dados para a tabela.
$busca = trim($_GET['buscar'] ?? '');

$trens = mysqli_query($conn, "SELECT id_trem, nome FROM trens ORDER BY nome");
$rotas = mysqli_query($conn, "SELECT * FROM rotas ORDER BY nome");
// Se o cadastro tiver erro, o formulário continua aberto para a pessoa ver o aviso.
$cadastro_aberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rotas | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=nomes-underscore-1">
</head>

<body class="body_geral">
    <div class="pagina">
        <!-- Menu desta página. A classe ativo destaca a seção aberta. -->
        <aside class="menu">
            <img class="logo_trem" src="https://img.icons8.com/ios/100/701015/train.png" alt="Logo TremTech">
            <p class="marca">SISTEMA TREMTECH</p>

            <a href="../Tela_Home/home.php"><img src="https://img.icons8.com/ios-filled/18/701015/home.png" alt=""> Home</a>
            <a href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a class="ativo" href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
            <a class="logout" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
        </aside>


        <main class="content_user">
            <p class="painel_user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de rotas cadastradas</h1>
            <h2>Rotas do sistema</h2>

            <div class="admin_badge_user card_recuado">
                <div class="avatar_user"></div>
                <div>
                    <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($mensagem !== '') { ?>
                <p><?php echo escapar($mensagem); ?></p>
            <?php } ?>

            <?php if ($administrador) { ?>
                <section id="cadastro" class="cadastro_usuario ativo"
                    <?php if (!$cadastro_aberto) { echo 'hidden'; } ?>>
                    <h2>Cadastrar nova rota</h2>

                    <form method="POST" class="form_cadastro_user">
                        <div class="campo_user">
                            <label for="nome">Nome da rota</label>
                            <input placeholder="Nome da rota" type="text" id="nome" name="nome" minlength="3" maxlength="100" required>
                        </div>

                        <div class="campo_user">
                            <label for="id_trem">Trem vinculado</label>
                            <select id="id_trem" name="id_trem" required>
                                <option value="">Selecione um trem</option>
    
                                <?php while ($trem = mysqli_fetch_assoc($trens)) { ?>
                                    <option value="<?php echo $trem['id_trem']; ?>">
                                        <?php echo escapar($trem['nome']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="campo_user campo_descricao_rota">
                            <label for="descricao">Descrição</label>
                            <textarea rows="2" id="descricao" name="descricao" maxlength="255" required></textarea>
                        </div>

                        <div class="campo_user">
                            <label for="extensao">Extensão(km)</label>
                            <input placeholder="Ex:45" type="number" id="extensao" name="extensao" min="0.01" step="0.01" required>
                        </div>

                        <div class="campo_user">
                            <label for="tempo">Tempo estimado(min)</label>
                            <input placeholder="Ex:30" type="number" id="tempo" name="tempo" min="1" required>
                        </div>

                        <button type="submit" class="btn_cadastrar_form">CADASTRAR NOVA ROTA</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Nesta área, Buscar filtra a lista e o botão Cadastrar abre o formulário. -->
            <section class="buscar_box_user buscar_usuarios_linha">
                <form method="GET">
                    <label for="buscar">Buscar rota</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome da rota" value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($administrador) { ?>
                    <button class="btn_cadastrar_user btn_cadastrar_user_compacto"
                            id="botao_abrir_cadastro" type="button" aria-controls="cadastro">
                        Cadastrar rota
                    </button>
                <?php } ?>
            </section>

            <?php include 'visualizacao_rotas.php'; ?>
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