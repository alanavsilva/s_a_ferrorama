<?php
// CADASTRAR TREM. O formulário envia os dados pelo método POST.
// O PHP valida e salva. A consulta da lista vai para o arquivo visualizacao_trens.php.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Quem fez login pode ver a tabela. Somente administradores podem enviar o cadastro.
exigir_login();

$administrador = $_SESSION['tipo'] === 'Administrador';
$mensagem = $_SESSION['mensagem_trem'] ?? '';
unset($_SESSION['mensagem_trem']);

// Cadastro. Recebe o formulário e faz o INSERT no banco.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    $nome = trim($_POST['nome'] ?? '');
    $velocidade = $_POST['velocidade'] ?? '';
    $id_usuario = (int) ($_POST['id_usuario'] ?? 0);

    $sql = "SELECT id_usuario FROM usuarios WHERE id_usuario = $id_usuario";
    $responsavel = mysqli_query($conn, $sql);

    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
        $mensagem = 'O nome deve ter entre 3 e 100 caracteres.';
    } elseif (!is_numeric($velocidade)) {
        $mensagem = 'Informe a velocidade média em km/h.';
    } elseif (mysqli_num_rows($responsavel) === 0) {
        $mensagem = 'Selecione um responsável cadastrado.';
    } else {
        $sql = "INSERT INTO trens (nome, velocidade_media, id_usuario) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        // Os tipos seguem a ordem dos valores enviados ao banco: s para texto, d para decimal e i para inteiro.
        mysqli_stmt_bind_param($stmt, 'sdi', $nome, $velocidade, $id_usuario);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_trem'] = 'Trem cadastrado com sucesso!';
            header('Location: cadastrar_trens.php');
            exit();
        }

        $mensagem = 'Não foi possível cadastrar o trem.';
    }
}

// Listagem. Busca os dados para a tabela.
$busca = trim($_GET['buscar'] ?? '');
$usuarios = mysqli_query($conn, "SELECT id_usuario, nome FROM usuarios ORDER BY nome");
$trens = mysqli_query($conn, "SELECT * FROM trens ORDER BY nome");
// Se o cadastro tiver erro, o formulário continua aberto para a pessoa ver o aviso.
$cadastro_aberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trens | TremTech</title>
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
            <a class="ativo" href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
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
            <h1>Controle de trens cadastrados</h1>
            <h2>Trens</h2>

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
                    

                    <form method="POST" class="form_cadastro_user">
                        <div class="campo_user">
                            <label for="nome">Nome do trem</label>
                            <input placeholder="Nome do trem" type="text" id="nome" name="nome" minlength="3" maxlength="100" required>
                        </div>

                        <div class="campo_user">
                            <label for="velocidade">Velocidade média</label>
                            <input placeholder="Velocidade média" type="number" id="velocidade" name="velocidade"
                                   step="any" required>
                        </div>

                        <div class="campo_user campo_responsavel_trem">
                            <label for="id_usuario">Usuário responsável</label>
                            <select id="id_usuario" name="id_usuario" required>
                                <option value="">Selecione o responsável</option>
    
                                <?php while ($usuario = mysqli_fetch_assoc($usuarios)) { ?>
                                    <option value="<?php echo $usuario['id_usuario']; ?>">
                                        <?php echo escapar($usuario['nome']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <button class="btn_cadastrar_form" type="submit">CADASTRAR NOVO TREM</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Nesta área, Buscar filtra a lista e o botão Cadastrar abre o formulário. -->
            <section class="buscar_box_user buscar_usuarios_linha">
                <form method="GET">
                    <label for="buscar">Buscar</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome ou responsável"
                           value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($administrador) { ?>
                    <button class="btn_cadastrar_user btn_cadastrar_user_compacto"
                            id="botao_abrir_cadastro" type="button" aria-controls="cadastro">
                        Cadastrar trem
                    </button>
                <?php } ?>
            </section>

            <?php include 'visualizacao_trens.php'; ?>
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