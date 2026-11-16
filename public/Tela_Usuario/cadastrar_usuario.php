
<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_usuario.php";

exigir_login();

$ehAdministrador =
    strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$mensagemSucesso = $_SESSION['mensagem_cadastro'] ?? '';
unset($_SESSION['mensagem_cadastro']);

$avisoExclusao = $_SESSION['aviso_exclusao'] ?? '';
unset($_SESSION['aviso_exclusao']);

$mensagem = '';
$mensagemAcesso = '';
$usuarioEdicao = null;
$idEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

if ($idEdicao) {
    if (!$ehAdministrador) {
        http_response_code(403);
        exit('Acesso negado.');
    }

    $stmtEdicao = $conn->prepare(
        'SELECT id_usuario, nome, email, tipo FROM usuarios WHERE id_usuario = ?'
    );
    $stmtEdicao->bind_param('i', $idEdicao);
    $stmtEdicao->execute();
    $usuarioEdicao = $stmtEdicao->get_result()->fetch_assoc();
    $stmtEdicao->close();

    if (!$usuarioEdicao) {
        http_response_code(404);
        exit('Usuário não encontrado.');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar usuários.';

    } else {

        verificar_csrf();

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipoRecebido = $_POST['tipo'] ?? '';
        $acaoFormulario = $_POST['acao'] ?? 'criar';
        validar_acao_crud($conn, $acaoFormulario, 'usuarios', 'id_usuario', 'id_usuario');

        if (!in_array($acaoFormulario, ['criar', 'editar'], true)) {
            $mensagem = 'Ação inválida.';
        } elseif ($acaoFormulario === 'editar') {
            $idEdicao = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
            if (!$idEdicao || $idEdicao < 1) {
                http_response_code(400);
                exit('Usuário inválido.');
            }

            $usuarioEdicao = [
                'id_usuario' => $idEdicao,
                'nome' => $nome,
                'email' => $email,
                'tipo' => $tipoRecebido
            ];

            if ($nome === '' || $email === '' || mb_strlen($nome) > 100 || mb_strlen($email) > 100) {
                $mensagem = 'Preencha os campos obrigatórios. Nome e e-mail devem ter até 100 caracteres.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mensagem = 'Informe um email válido.';
            } elseif ($senha !== '' && strlen($senha) < 8) {
                $mensagem = 'A senha deve ter pelo menos 8 caracteres.';
            } elseif (!in_array($tipoRecebido, ['Funcionário', 'Administrador'], true)) {
                $mensagem = 'Tipo de usuário inválido.';
            } else {
                if ($senha !== '') {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare(
                        'UPDATE usuarios SET nome = ?, email = ?, senha = ?, tipo = ? WHERE id_usuario = ?'
                    );
                    $stmt->bind_param('ssssi', $nome, $email, $senhaHash, $tipoRecebido, $idEdicao);
                } else {
                    $stmt = $conn->prepare(
                        'UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id_usuario = ?'
                    );
                    $stmt->bind_param('sssi', $nome, $email, $tipoRecebido, $idEdicao);
                }

                try {
                    if ($stmt->execute()) {
                        $_SESSION['mensagem_cadastro'] = 'Usuário atualizado com sucesso!';
                        header('Location: cadastrar_usuario.php');
                        exit;
                    }
                } catch (mysqli_sql_exception $e) {
                    if ((int) $e->getCode() === 1062) {
                        $mensagem = 'Este email já está cadastrado.';
                    } else {
                        error_log('Erro ao atualizar usuário: ' . $e->getMessage());
                        $mensagem = 'Não foi possível atualizar o usuário.';
                    }
                }

                if ($mensagem === '') {
                    $mensagem = 'Não foi possível atualizar o usuário.';
                }
                $stmt->close();
            }
        } elseif ($nome === '' || $email === '' || $senha === '' || mb_strlen($nome) > 100 || mb_strlen($email) > 100) {
            $mensagem = "Preencha os campos. Nome e e-mail devem ter até 100 caracteres.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem = "Informe um email válido.";

        } elseif (strlen($senha) < 8) {
            $mensagem = "A senha deve ter pelo menos 8 caracteres.";

        } elseif (!in_array($tipoRecebido, ['Funcionário', 'Administrador'], true)) {
            $mensagem = "Tipo de usuário inválido.";

        } else {

            $tipo = $tipoRecebido;
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, senha, tipo)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                $mensagem = "Não foi possível concluir o cadastro.";

            } else {

                $stmt->bind_param("ssss", $nome, $email, $senhaHash, $tipo);

                try {

                    if ($stmt->execute()) {
                        $_SESSION['mensagem_cadastro'] = 'Usuário cadastrado com sucesso!';
                        header('Location: cadastrar_usuario.php');
                        exit;
                    }

                } catch (mysqli_sql_exception $e) {

                    if ((int) $e->getCode() === 1062) {
                        $mensagem = 'Este email já está cadastrado.';

                    } else {
                        error_log('Erro ao cadastrar usuário: ' . $e->getMessage());
                        $mensagem = 'Não foi possível concluir o cadastro.';
                    }
                }

                $stmt->close();
            }
        }
    }
}

$manterFormularioAberto = $_SERVER['REQUEST_METHOD'] === 'POST' || $usuarioEdicao !== null;
$modoEdicao = $usuarioEdicao !== null;
$valorNome = $usuarioEdicao['nome'] ?? ($nome ?? '');
$valorEmail = $usuarioEdicao['email'] ?? ($email ?? '');
$valorTipo = $usuarioEdicao['tipo'] ?? ($tipoRecebido ?? 'Funcionário');

$busca = trim($_GET['buscar'] ?? '');
$textoBusca = "%" . $busca . "%";

$sql = "SELECT id_usuario, nome, email, tipo
        FROM usuarios
        WHERE nome LIKE ? OR email LIKE ?
        ORDER BY nome";

$stmtBusca = $conn->prepare($sql);

if ($stmtBusca) {

    $stmtBusca->bind_param("ss", $textoBusca, $textoBusca);

    if ($stmtBusca->execute()) {
        $resultado = $stmtBusca->get_result();

    } else {
        error_log("Erro ao consultar usuários: " . $stmtBusca->error);
        $resultado = false;
        $mensagemAcesso = 'Não foi possível carregar os usuários.';
    }

    $stmtBusca->close();

} else {

    error_log("Erro ao preparar consulta: " . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível carregar os usuários.';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro Usuário</title>

    <link rel="stylesheet" href="../../assets/style/style.css?v=20261115-usuario-edicao2">

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

        <a class="ativo" href="../../public/Tela_Usuario/cadastrar_usuario.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
            Usuários
        </a>

        <a href="../../public/Tela_Sensores/cadastrar_sensores.php">
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

        <h1>Controle de usuários cadastrados</h1>
        <h2>Administradores e Funcionários</h2>

          <?php if ($mensagemSucesso !== ''): ?>
            <p class="mensagem-sucesso" role="status">
                <?= escapar($mensagemSucesso) ?>
            </p>
        <?php endif; ?>

        <?php if ($mensagemAcesso !== ''): ?>
            <p role="alert"><?= escapar($mensagemAcesso) ?></p>
        <?php endif; ?>
        <div class="admin-badge-user card-recuado">

            <div class="avatar-user"></div>

            <div>
                <strong>
                    <?= $ehAdministrador ? 'Administrador' : 'Funcionário' ?>
                </strong>

                <span><?= escapar($_SESSION['nome'] ?? '') ?></span>
            </div>

        </div>

        <?php if ($ehAdministrador): ?>

            <section class="cadastro-usuario <?= $manterFormularioAberto ? 'ativo' : '' ?>" id="cadastro" <?= $manterFormularioAberto ? '' : 'hidden' ?>>
                <h2><?= $modoEdicao ? 'Editar usuário' : 'Cadastrar novo usuário' ?></h2>

                <form
                    class="form-cadastro-user"
                    method="POST"
                    novalidate>

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapar(token_csrf()) ?>">
                    <input type="hidden" name="acao" value="<?= $modoEdicao ? 'editar' : 'criar' ?>">
                    <?php if ($modoEdicao): ?>
                        <input type="hidden" name="id_usuario" value="<?= (int) $usuarioEdicao['id_usuario'] ?>">
                    <?php endif; ?>

                    <div class="campo-user">

                        <label for="nome">Nome de usuário</label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            placeholder="Nome completo"
                            value="<?= escapar($valorNome) ?>"
                            required>

                    </div>

                    <div class="campo-user">

                        <label for="email">Email</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Email"
                            value="<?= escapar($valorEmail) ?>"
                            required>

                    </div>

                    <div class="campo-user">

                        <label for="senha">Senha</label>

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="<?= $modoEdicao ? 'Deixe em branco para manter a senha atual' : 'Senha com pelo menos 8 caracteres' ?>"
                            <?= $modoEdicao ? '' : 'required' ?>>

                    </div>

                    <div class="campo-user">

                        <label for="tipo">Tipo de usuário</label>

                        <select id="tipo" name="tipo">

                            <option value="Funcionário" <?= $valorTipo === 'Funcionário' ? 'selected' : '' ?>>Funcionário</option>
                            <option value="Administrador" <?= $valorTipo === 'Administrador' ? 'selected' : '' ?>>Administrador</option>

                        </select>

                    </div>

                    <?php if ($mensagem !== ''): ?>

                        <p class="mensagem-cadastro" role="alert">
                            <?= escapar($mensagem) ?>
                        </p>

                    <?php endif; ?>

                    <div class="acoes-form-edicao <?= $modoEdicao ? 'modo-edicao' : 'modo-cadastro' ?>">
                        <button class="btn-cadastrar-form" type="submit">
                            <?= $modoEdicao ? 'SALVAR ALTERAÇÕES' : 'CADASTRAR NOVO USUÁRIO' ?>
                        </button>
                        <?php if ($modoEdicao): ?>
                            <a class="btn-cancelar-form" href="cadastrar_usuario.php">Cancelar</a>
                        <?php endif; ?>
                    </div>

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
                    placeholder="Nome ou email"
                    value="<?= escapar($busca) ?>">

                <button type="submit">Buscar</button>

            </form>

            <?php if ($ehAdministrador && !$modoEdicao): ?>
                <button
                    class="btn-cadastrar-user btn-cadastrar-user-compacto"
                    id="btnAbrirCadastro"
                    type="button"
                    aria-expanded="<?= $manterFormularioAberto ? 'true' : 'false' ?>"
                    aria-controls="cadastro">
                    Cadastrar usuário
                </button>
            <?php endif; ?>

        </section>

        <?php mostrar_usuarios($resultado); ?>

    </main>

</div>

<?php if ($avisoExclusao !== ''): ?>

    <div class="modal-aviso" id="modalAviso">

        <div class="conteudo-aviso">

            <div class="icone-aviso">!</div>

            <h2>Não é possível excluir este usuário</h2>

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

<script src="../../script/validacao.js?v=20261115-senha"></script>

<script src="../../script/confirmar_logout.js?v=20261107"></script>
</body>
</html>
