<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Usuários logados podem consultar a lista. O cadastro exige perfil de administrador.
exigir_login();

$ehAdministrador = $_SESSION['tipo'] === 'Administrador';
$tipos = ['Velocidade', 'Temperatura', 'Status'];
$mensagem = $_SESSION['mensagem_sensor'] ?? '';
unset($_SESSION['mensagem_sensor']);

// Recebe os dados do formulário, confere os campos e cadastra o registro.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    $nome = trim($_POST['nome_sensor']);
    $tipo = $_POST['tipo_dado'];
    $idRota = (int) $_POST['id_rotas'];

    $sql = "SELECT id_rotas FROM rotas WHERE id_rotas = $idRota";
    $rotaEncontrada = mysqli_query($conn, $sql);

    // in_array confere se o tipo enviado está na lista de tipos do banco.
    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100 || !in_array($tipo, $tipos)) {
        $mensagem = 'Informe um nome válido e selecione o tipo do sensor.';
    } elseif (mysqli_num_rows($rotaEncontrada) === 0) {
        $mensagem = 'Selecione uma rota cadastrada.';
    } else {
        $sql = "INSERT INTO sensores (nome_sensor, tipo_dado, id_rotas) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssi', $nome, $tipo, $idRota);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['mensagem_sensor'] = 'Sensor cadastrado com sucesso!';
        header('Location: cadastrar_sensores.php');
        exit();
    }
}

// Lê o texto digitado na busca antes de mostrar a tabela.
$busca = trim($_GET['buscar'] ?? '');
$rotas = mysqli_query($conn, "SELECT id_rotas, nome FROM rotas ORDER BY nome");
$sensores = mysqli_query($conn, "SELECT * FROM sensores ORDER BY id_sensor");
// Mantém o formulário aberto para mostrar um erro de cadastro.
$cadastroAberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensores | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=cadastro-3">
</head>
<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user">
            <p class="painel-user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de sensores cadastrados</h1>
            <h2>Sensores do sistema</h2>

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
                    <h2>Cadastrar novo sensor</h2>

                    <form method="POST" class="form-cadastro-user">
                        <div class="campo-user">
                            <label for="nome_sensor">Nome do sensor</label>
                            <input placeholder="Nome do sensor" type="text" id="nome_sensor" name="nome_sensor"
                                   minlength="3" maxlength="100" required>
                        </div>

                        <div class="campo-user">
                            <label for="tipo_dado">Tipo de dado coletado</label>
                            <select id="tipo_dado" name="tipo_dado" required>
                                <option value="">Selecione</option>
                                <?php foreach ($tipos as $tipo) { ?>
                                    <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="campo-user campo-rota-sensor">
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

                        <button type="submit" class="btn-cadastrar-form">CADASTRAR NOVO SENSOR</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Área de busca e botão para abrir o formulário de cadastro. -->
            <section class="buscar-box-user buscar-usuarios-linha">
                <form method="GET">
                    <label for="buscar">Buscar</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome, tipo ou rota"
                           value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($ehAdministrador) { ?>
                    <button class="btn-cadastrar-user btn-cadastrar-user-compacto"
                            id="btnAbrirCadastro" type="button" aria-controls="cadastro" aria-expanded="<?php echo $cadastroAberto ? 'true' : 'false'; ?>">
                        Cadastrar sensor
                    </button>
                <?php } ?>
            </section>

            <?php include 'visualizacao_sensores.php'; ?>
        </main>
    </div>

<script src="../../script/validacao.js?v=cadastro-3"></script>
</body>
</html>