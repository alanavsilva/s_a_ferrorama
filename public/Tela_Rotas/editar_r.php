<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();

if (!isset($_GET['id'])) {
    exit('Rota inválida.');
}

// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) $_GET['id'];
$sql = "SELECT * FROM rotas WHERE id_rotas = $id";
$resultado = mysqli_query($conn, $sql);
$rota = mysqli_fetch_assoc($resultado);

if (!$rota) {
    exit('Rota não encontrada.');
}

$mensagem = '';

// Ao clicar em Salvar, o formulário volta para este arquivo e o UPDATE grava a rota.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rota['nome'] = trim($_POST['nome'] ?? '');
    $rota['descricao'] = trim($_POST['descricao'] ?? '');
    $rota['extensao'] = (float) ($_POST['extensao'] ?? 0);
    $rota['tempo'] = (int) ($_POST['tempo'] ?? 0);
    $rota['id_trem'] = (int) ($_POST['id_trem'] ?? 0);

    $id_trem = $rota['id_trem'];
    $sql = "SELECT id_trem FROM trens WHERE id_trem = $id_trem";
    $trem_encontrado = mysqli_query($conn, $sql);

    if ($rota['nome'] === '' || $rota['descricao'] === ''
        || $rota['extensao'] <= 0 || $rota['tempo'] <= 0) {
        $mensagem = 'Preencha os campos com valores válidos.';
    } elseif (mysqli_num_rows($trem_encontrado) === 0) {
        $mensagem = 'Selecione um trem cadastrado.';
    } else {
        $sql = "UPDATE rotas SET nome = ?, descricao = ?, extensao = ?, tempo = ?, id_trem = ?
                WHERE id_rotas = ?";
        $stmt = mysqli_prepare($conn, $sql);

        // Os tipos seguem a ordem dos valores enviados ao banco: s para texto, d para decimal e i para inteiro.
        mysqli_stmt_bind_param($stmt, 'ssdiii', $rota['nome'], $rota['descricao'],
            $rota['extensao'], $rota['tempo'], $id_trem, $id);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_rota'] = 'Rota atualizada com sucesso!';    
            header('Location: cadastrar_rotas.php');    
            exit();
        }

        $mensagem = "Não foi possível salvar as alterações.";
    }
}

$trens = mysqli_query($conn, "SELECT id_trem, nome FROM trens ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar rota | TremTech</title>
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
        <h1>Editar rota</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro_usuario ativo">
            <form method="POST" class="form_cadastro_user">
                <label for="nome">Nome da rota</label>
                <input type="text" id="nome" name="nome" minlength="3" maxlength="100"
                       value="<?php echo escapar($rota['nome']); ?>" required>

                <label for="id_trem">Trem vinculado</label>
                <select id="id_trem" name="id_trem" required>
                    <option value="">Selecione um trem</option>

                    <?php while ($trem = mysqli_fetch_assoc($trens)) { ?>
                        <option value="<?php echo $trem['id_trem']; ?>"
                            <?php if ($trem['id_trem'] == $rota['id_trem']) { echo 'selected'; } ?>>
                            <?php echo escapar($trem['nome']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" maxlength="255" required><?php echo escapar($rota['descricao']); ?></textarea>

                <label for="extensao">Extensão em km</label>
                <input type="number" id="extensao" name="extensao" min="0.01" step="0.01"
                       value="<?php echo $rota['extensao']; ?>" required>

                <label for="tempo">Tempo em minutos</label>
                <input type="number" id="tempo" name="tempo" min="1"
                       value="<?php echo $rota['tempo']; ?>" required>

                <button type="submit" class="btn_cadastrar_form">Salvar alterações</button>
                <a href="cadastrar_rotas.php">Cancelar</a>
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