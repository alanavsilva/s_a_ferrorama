<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Usuários logados podem consultar a lista. O cadastro exige perfil de administrador.
exigir_login();

$ehAdministrador = $_SESSION['tipo'] === 'Administrador';
$mensagem = '';

if (isset($_SESSION['mensagem_rota'])) {
    $mensagem = $_SESSION['mensagem_rota'];
    unset($_SESSION['mensagem_rota']);
}

// Recebe os dados do formulário, confere os campos e cadastra o registro.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $extensao = (float) $_POST['extensao'];
    $tempo = (int) $_POST['tempo'];
    $idTrem = (int) $_POST['id_trem'];

    $sql = "SELECT id_trem FROM trens WHERE id_trem = $idTrem";
    $tremEncontrado = mysqli_query($conn, $sql);

    if ($nome === '' || $descricao === '' || $extensao <= 0 || $tempo <= 0) {
        $mensagem = 'Preencha os campos com valores válidos.';
    } elseif (mysqli_num_rows($tremEncontrado) === 0) {
        $mensagem = 'Selecione um trem cadastrado.';
    } else {
        $sql = "INSERT INTO rotas (nome, descricao, extensao, tempo, id_trem)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssdii', $nome, $descricao, $extensao, $tempo, $idTrem);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['mensagem_rota'] = 'Rota cadastrada com sucesso!';
        header('Location: cadastrar_rotas.php');
        exit();
    }
}

// Lê o texto digitado na busca antes de mostrar a tabela.
$busca = '';

if (isset($_GET['buscar'])) {
    $busca = trim($_GET['buscar']);
}

$trens = mysqli_query($conn, "SELECT id_trem, nome FROM trens ORDER BY nome");
$rotas = mysqli_query($conn, "SELECT * FROM rotas ORDER BY nome");
// Mantém o formulário aberto para mostrar um erro de cadastro.
$cadastroAberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rotas | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=cadastro-3">
</head>

<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user">
            <p class="painel-user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de rotas cadastradas</h1>
            <h2>Rotas do sistema</h2>

            <div class="admin-badge-user card-recuado">
                <div class="avatar-user"></div>
                <div>
                    <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($mensagem !== '') { ?>
                <p><?php echo escapar($mensagem); ?></p>
            <?php } ?>

            <?php if ($ehAdministrador) { ?>
                <section id="cadastro" class="cadastro-usuario<?php if ($cadastroAberto) { echo ' ativo'; } ?>"
                    <?php if (!$cadastroAberto) { echo 'hidden'; } ?>>
                    <h2>Cadastrar nova rota</h2>

                    <form method="POST" class="form-cadastro-user">
                        <div class="campo-user">
                            <label for="nome">Nome da rota</label>
                            <input placeholder="Nome da rota" type="text" id="nome" name="nome" minlength="3" maxlength="100" required>
                        </div>

                        <div class="campo-user">
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

                        <div class="campo-user campo-descricao-rota">
                            <label for="descricao">Descrição</label>
                            <textarea rows="2" id="descricao" name="descricao" maxlength="255" required></textarea>
                        </div>

                        <div class="campo-user">
                            <label for="extensao">Extensão(km)</label>
                            <input placeholder="Ex:45" type="number" id="extensao" name="extensao" min="0.01" step="0.01" required>
                        </div>

                        <div class="campo-user">
                            <label for="tempo">Tempo estimado(min)</label>
                            <input placeholder="Ex:30" type="number" id="tempo" name="tempo" min="1" required>
                        </div>

                        <button type="submit" class="btn-cadastrar-form">CADASTRAR NOVA ROTA</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Área de busca e botão para abrir o formulário de cadastro. -->
            <section class="buscar-box-user buscar-usuarios-linha">
                <form method="GET">
                    <label for="buscar">Buscar rota</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome da rota" value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($ehAdministrador) { ?>
                    <button class="btn-cadastrar-user btn-cadastrar-user-compacto"
                            id="btnAbrirCadastro" type="button" aria-controls="cadastro" aria-expanded="<?php echo $cadastroAberto ? 'true' : 'false'; ?>">
                        Cadastrar rota
                    </button>
                <?php } ?>
            </section>

            <?php include 'visualizacao_rotas.php'; ?>
        </main>
    </div>

<script src="../../script/validacao.js?v=cadastro-3"></script>
</body>
</html>