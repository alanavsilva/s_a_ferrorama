<?php
// EDITAR TREM. O link Editar manda o ID no endereço e o PHP busca o trem no banco.
// O formulário abre preenchido. Ao salvar, este mesmo arquivo recebe os campos e faz a atualização.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();

// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) ($_GET['id'] ?? 0);
$sql = "SELECT * FROM trens WHERE id_trem = $id";
$resultado = mysqli_query($conn, $sql);
$trem = mysqli_fetch_assoc($resultado);

if (!$trem) {
    exit('Trem não encontrado.');
}

$nome = $trem['nome'];
$velocidade = $trem['velocidade_media'];
$id_usuario = $trem['id_usuario'];
$mensagem = '';

// Ao enviar o formulário, este mesmo arquivo salva o trem indicado na URL.
// Quando a página é só aberta, este bloco é pulado. Ele roda quando a pessoa clica em Salvar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $sql = "UPDATE trens SET nome = ?, velocidade_media = ?, id_usuario = ?
                WHERE id_trem = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sdii', $nome, $velocidade, $id_usuario, $id);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_trem'] = 'Trem atualizado com sucesso!';
            header('Location: cadastrar_trens.php');
            exit();
        }

        $mensagem = 'Não foi possível atualizar o trem.';
    }
}

$usuarios = mysqli_query($conn, "SELECT id_usuario, nome FROM usuarios ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar trem | TremTech</title>
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
        <h1>Editar trem</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro_usuario ativo">
            <form method="POST" class="form_cadastro_user">
                <label for="nome">Nome do trem</label>
                <input type="text" id="nome" name="nome" minlength="3" maxlength="100"
                       value="<?php echo escapar($nome); ?>" required>

                <label for="velocidade">Velocidade média em km/h</label>
                <input type="number" id="velocidade" name="velocidade"
                       step="any" value="<?php echo escapar($velocidade); ?>" required>

                <label for="id_usuario">Usuário responsável</label>
                <select id="id_usuario" name="id_usuario" required>
                    <option value="">Selecione o responsável</option>

                    <?php while ($usuario = mysqli_fetch_assoc($usuarios)) { ?>
                        <option value="<?php echo $usuario['id_usuario']; ?>"
                            <?php if ($usuario['id_usuario'] == $id_usuario) { echo 'selected'; } ?>>
                            <?php echo escapar($usuario['nome']); ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit" class="btn_cadastrar_form">Salvar alterações</button>
                <a href="cadastrar_trens.php">Cancelar</a>
            </form>
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