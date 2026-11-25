<?php
// EDITAR USUÁRIO. O link Editar manda o ID no endereço e o PHP busca o usuário no banco.
// O formulário abre preenchido. Ao salvar, este mesmo arquivo recebe os campos e faz a atualização.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();

// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) ($_GET['id'] ?? 0);
$sql = "SELECT nome, email, tipo FROM usuarios WHERE id_usuario = $id";
$resultado = mysqli_query($conn, $sql);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    exit('Usuário não encontrado.');
}

$nome = $usuario['nome'];
$email = $usuario['email'];
$tipo = $usuario['tipo'];
$mensagem = '';

// Quando a página é só aberta, este bloco é pulado. Ele roda quando a pessoa clica em Salvar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if ($nome === '' || mb_strlen($nome) > 100) {
        $mensagem = 'Informe um nome de até 100 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $mensagem = 'Informe um e-mail válido de até 100 caracteres.';
    } elseif ($tipo !== 'Administrador' && $tipo !== 'Funcionário') {
        $mensagem = 'Selecione um tipo de usuário válido.';
    } else {
        // Só atualiza a senha se a pessoa preencher o campo Nova senha.
        if ($senha === '') {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id_usuario = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssi', $nome, $email, $tipo, $id);
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ?, senha = ? WHERE id_usuario = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $email, $tipo, $senha_hash, $id);
        }

        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }

        mysqli_stmt_close($stmt);

        if ($salvou) {
            // Se editou a própria conta, atualiza também os dados da sessão.
            if ($id == $_SESSION['id_usuario']) {
                $_SESSION['nome'] = $nome;
                $_SESSION['email'] = $email;
                $_SESSION['tipo'] = $tipo;
            }

            $_SESSION['mensagem_usuario'] = 'Usuário atualizado com sucesso!';
            header('Location: cadastrar_usuario.php');
            exit();
        }

        $mensagem = 'Não foi possível salvar. Confira se o e-mail já está em uso.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuário | TremTech</title>
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
            <a class="ativo" href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
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
        <h1>Editar usuário</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro_usuario ativo">
            <form method="POST" class="form_cadastro_user">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" maxlength="100"
                       value="<?php echo escapar($nome); ?>" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" maxlength="100"
                       value="<?php echo escapar($email); ?>" required>

                <label for="senha">Nova senha</label>
                <input type="password" id="senha" name="senha"
                       placeholder="Deixe em branco para manter a senha">

                <label for="tipo">Tipo de usuário</label>
                <select id="tipo" name="tipo">
                    <option <?php if ($tipo === 'Funcionário') { echo 'selected'; } ?>>Funcionário</option>
                    <option <?php if ($tipo === 'Administrador') { echo 'selected'; } ?>>Administrador</option>
                </select>

                <button class="btn_cadastrar_form" type="submit">Salvar</button>
                <a href="cadastrar_usuario.php">Cancelar</a>
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