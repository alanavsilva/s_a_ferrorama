
<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_trens.php";

exigir_login();

$ehAdministrador = strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$mensagemSucesso = $_SESSION['mensagem_cadastro'] ?? '';
unset($_SESSION['mensagem_cadastro']);

$avisoExclusao = $_SESSION['aviso_exclusao'] ?? '';
unset($_SESSION['aviso_exclusao']);

if (isset($_GET['editado'])) {
    $mensagemSucesso = 'Trem atualizado com sucesso!';
} elseif (isset($_GET['excluido'])) {
    $mensagemSucesso = 'Trem excluído com sucesso!';
}

$mensagem = '';
$mensagemAcesso = '';

$nome = '';
$velocidade = '';
$id_usuario = '';
$erros = [];
$idEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT) ?: 0;
$tremEdicao = null;

$usuarios = [];

$resultadoUsuarios = $conn->query(
    "SELECT id_usuario, nome FROM usuarios ORDER BY nome"
);

if ($resultadoUsuarios) {
    while ($usuario = $resultadoUsuarios->fetch_assoc()) {
        $usuarios[] = $usuario;
    }
}

if ($idEdicao > 0 && $ehAdministrador) {
    $stmtEdicao = $conn->prepare('SELECT id_trem, nome, velocidade_media, id_usuario FROM trens WHERE id_trem = ?');
    $stmtEdicao->bind_param('i', $idEdicao);
    $stmtEdicao->execute();
    $tremEdicao = $stmtEdicao->get_result()->fetch_assoc();
    $stmtEdicao->close();
    if ($tremEdicao) {
        $nome = $tremEdicao['nome'];
        $velocidade = (string) $tremEdicao['velocidade_media'];
        $id_usuario = (string) $tremEdicao['id_usuario'];
    } else {
        $idEdicao = 0;
        $mensagemAcesso = 'Trem não encontrado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar trens.';

    } else {

        verificar_csrf();

        $acao = $_POST['acao'] ?? 'criar';
        validar_acao_crud($conn, $acao, 'trens', 'id_trem', 'id_trem');
        $idAtualizar = filter_input(INPUT_POST, 'id_trem', FILTER_VALIDATE_INT) ?: 0;

        $nome = trim($_POST['nome'] ?? '');
        $velocidade = trim($_POST['velocidade'] ?? '');
        $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT) ?: '';

        if ($nome === '' || $velocidade === '' || $id_usuario === '') {
            $mensagem = "Preencha todos os campos.";

        } elseif (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
            $erros['nome'] = 'O nome deve ter entre 3 e 100 caracteres.';

        } elseif (!is_numeric($velocidade) || $velocidade < 1 || $velocidade > 600) {
            $erros['velocidade'] = 'Use um valor entre 1 e 600 km/h.';

        } else {

            $usuarioValido = false;

            foreach ($usuarios as $usuario) {
                if ((int)$usuario['id_usuario'] === (int)$id_usuario) {
                    $usuarioValido = true;
                    break;
                }
            }

            if (!$usuarioValido) {
                $mensagem = 'Selecione um responsável válido.';

            } else {

                $velocidadeBanco = (float)$velocidade;
                $idUsuarioBanco = (int)$id_usuario;

                $sql = $acao === 'editar'
                    ? 'UPDATE trens SET nome = ?, velocidade_media = ?, id_usuario = ? WHERE id_trem = ?'
                    : 'INSERT INTO trens (nome, velocidade_media, id_usuario) VALUES (?, ?, ?)';

                $stmt = $conn->prepare($sql);

                if (!$stmt) {
                    $mensagem = "Não foi possível concluir o cadastro.";

                } else {

                    if ($acao === 'editar') {
                        $stmt->bind_param('sdii', $nome, $velocidadeBanco, $idUsuarioBanco, $idAtualizar);
                    } else {
                        $stmt->bind_param('sdi', $nome, $velocidadeBanco, $idUsuarioBanco);
                    }

                    try {

                        if ($stmt->execute()) {
                            $_SESSION['mensagem_cadastro'] = $acao === 'editar' ? 'Trem atualizado com sucesso!' : 'Trem cadastrado com sucesso!';
                            $stmt->close();

                            header('Location: cadastrar_trens.php');
                            exit;

                        } else {
                            error_log('Erro ao cadastrar trem: ' . $stmt->error);
                            $mensagem = 'Não foi possível concluir o cadastro.';
                        }

                    } catch (mysqli_sql_exception $e) {
                        error_log('Erro ao cadastrar trem: ' . $e->getMessage());
                        $mensagem = 'Não foi possível concluir o cadastro.';
                    }

                    $stmt->close();
                }
            }
        }
    }
}

$manterFormularioAberto = $_SERVER['REQUEST_METHOD'] === 'POST' || $idEdicao > 0;

$busca = trim($_GET['buscar'] ?? '');
$textoBusca = "%" . $busca . "%";

$sql = "SELECT
            t.id_trem,
            t.nome,
            t.velocidade_media,
            t.id_usuario,
            u.nome AS nome_responsavel
        FROM trens t
        LEFT JOIN usuarios u ON u.id_usuario = t.id_usuario
        WHERE t.nome LIKE ?
           OR u.nome LIKE ?
        ORDER BY t.nome";

$stmtBusca = $conn->prepare($sql);

if ($stmtBusca) {

    $stmtBusca->bind_param("ss", $textoBusca, $textoBusca);

    if ($stmtBusca->execute()) {
        $resultado = $stmtBusca->get_result();

    } else {
        error_log("Erro ao consultar trens: " . $stmtBusca->error);
        $resultado = false;
        $mensagemAcesso = 'Não foi possível carregar os trens.';
    }

    $stmtBusca->close();

} else {

    error_log("Erro ao preparar consulta: " . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível carregar os trens.';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro Trens</title>

    <link rel="stylesheet" href="../../assets/style/style.css?v=20261115-ui">
</head>

<body class="body_geral">

<div class="pagina">

    <aside class="menu">

        <img
            class="logo-trem"
            src="https://img.icons8.com/ios/100/701015/train.png"
            alt="Logo TremTech"
        >

        <p class="marca">SISTEMA TREMTECH</p>

        <a href="../../public/Tela_Home/home.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
            Home
        </a>

        <a href="../../public/Tela_Usuario/cadastrar_usuario.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
            Usuários
        </a>

        <a href="../../public/Tela_Sensores/cadastrar_sensores.php">
            <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
            Sensores
        </a>

        <a class="ativo" href="../../public/Tela_Trens/cadastrar_trens.php">
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

        <h1>Controle de trens cadastrados</h1>
        <h2>Trens</h2>

        <?php if ($mensagemSucesso !== ''): ?>
            <p class="mensagem-sucesso" role="status">
                <?= escapar($mensagemSucesso) ?>
            </p>
        <?php endif; ?>

        <?php if ($mensagemAcesso !== ''): ?>
            <p role="alert">
                <?= escapar($mensagemAcesso) ?>
            </p>
        <?php endif; ?>

        <?php foreach ($erros as $erroCampo): ?>
            <p role="alert">
                <?= escapar($erroCampo) ?>
            </p>
        <?php endforeach; ?>
        <div class="admin-badge-user card-recuado">

            <div class="avatar-user"></div>

            <div>
                <strong>
                    <?= $ehAdministrador ? 'Administrador' : 'Funcionário' ?>
                </strong>

                <span>
                    <?= escapar($_SESSION['nome'] ?? '') ?>
                </span>
            </div>

        </div>

        <?php if ($ehAdministrador): ?>

            <section
                class="cadastro-usuario <?= $manterFormularioAberto ? 'ativo' : '' ?>"
                id="cadastro"
                <?= $manterFormularioAberto ? '' : 'hidden' ?>
            >

                <form class="form-cadastro-user" method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapar(token_csrf()) ?>"
                    >

                    <div class="campo-user">

                        <label for="nome">Nome do trem</label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            placeholder="Nome do trem"
                            value="<?= escapar($nome) ?>"
                            required
                        >

                    </div>

                    <div class="campo-user">

                        <label for="velocidade">Velocidade média</label>

                        <input
                            type="number"
                            id="velocidade"
                            name="velocidade"
                            placeholder="Velocidade média"
                            min="1"
                            max="600"
                            step="any"
                            value="<?= escapar($velocidade) ?>"
                            required
                        >

                    </div>

                    <div class="campo-user campo-responsavel-trem">

                        <label for="id_usuario">Usuário responsável</label>

                        <select id="id_usuario" name="id_usuario" required>

                            <option value="">Selecione o responsável</option>

                            <?php foreach ($usuarios as $usuario): ?>

                                <option
                                    value="<?= (int)$usuario['id_usuario'] ?>"
                                    <?= (string)$id_usuario === (string)$usuario['id_usuario'] ? 'selected' : '' ?>
                                >
                                    <?= escapar($usuario['nome']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <?php if ($mensagem !== ''): ?>
                        <p class="mensagem-cadastro" role="alert">
                            <?= escapar($mensagem) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($idEdicao > 0): ?>
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id_trem" value="<?= (int) $idEdicao ?>">
                        <div class="acoes-form-edicao modo-edicao">
                            <button class="btn-cadastrar-form" type="submit">SALVAR ALTERAÇÕES</button>
                            <a class="btn-cancelar-form" href="cadastrar_trens.php">Cancelar</a>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="acao" value="criar">
                        <button class="btn-cadastrar-form" type="submit">CADASTRAR NOVO TREM</button>
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
                    placeholder="Nome ou responsável"
                    value="<?= escapar($busca) ?>"
                >

                <button type="submit">Buscar</button>

            </form>

            <?php if ($ehAdministrador && $idEdicao === 0): ?>
                <button class="btn-cadastrar-user btn-cadastrar-user-compacto" id="btnAbrirCadastro"
                        type="button" aria-expanded="<?= $manterFormularioAberto ? 'true' : 'false' ?>"
                        aria-controls="cadastro">
                    Cadastrar trem
                </button>
            <?php endif; ?>

        </section>

        <?php mostrar_trens($resultado); ?>

        <?php if ($avisoExclusao !== ''): ?>

            <div id="modalErroExclusao" class="modal-overlay">

                <div class="modal-conteudo">

                    <div class="icone-alerta">!</div>

                    <h2>Não foi possível excluir o trem</h2>

                    <p><?= escapar($avisoExclusao) ?></p>

                    <button
                        type="button"
                        onclick="document.getElementById('modalErroExclusao').remove()"
                    >
                        Voltar
                    </button>

                </div>

            </div>

        <?php endif; ?>

    </main>

</div>

<script src="../../script/validacao.js"></script>

<script src="../../script/confirmar_logout.js?v=20261107"></script>
</body>
</html>
