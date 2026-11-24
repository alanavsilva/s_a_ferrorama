<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// A edição só pode ser feita por um administrador.
exigir_administrador();

if (!isset($_GET['id'])) {
    exit('Sensor inválido.');
}

// O ID da URL indica qual registro deve aparecer preenchido no formulário.
$id = (int) $_GET['id'];
$sql = "SELECT * FROM sensores WHERE id_sensor = $id";
$resultado = mysqli_query($conn, $sql);
$sensor = mysqli_fetch_assoc($resultado);

if (!$sensor) {
    exit('Sensor não encontrado.');
}

$tipos = ['Velocidade', 'Temperatura', 'Status'];
$mensagem = '';

// O formulário envia os dados para este mesmo arquivo.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sensor['nome_sensor'] = trim($_POST['nome_sensor']);
    $sensor['tipo_dado'] = $_POST['tipo_dado'];
    $sensor['id_rotas'] = (int) $_POST['id_rotas'];

    $nome = $sensor['nome_sensor'];
    $tipo = $sensor['tipo_dado'];
    $idRota = $sensor['id_rotas'];

    $sql = "SELECT id_rotas FROM rotas WHERE id_rotas = $idRota";
    $rotaEncontrada = mysqli_query($conn, $sql);

    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100 || !in_array($tipo, $tipos)) {
        $mensagem = 'Informe um nome válido e selecione o tipo do sensor.';
    } elseif (mysqli_num_rows($rotaEncontrada) === 0) {
        $mensagem = 'Selecione uma rota cadastrada.';
    } else {
        $sql = "UPDATE sensores SET nome_sensor = ?, tipo_dado = ?, id_rotas = ?
                WHERE id_sensor = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssii', $nome, $tipo, $idRota, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['mensagem_sensor'] = 'Sensor atualizado com sucesso!';
        header('Location: cadastrar_sensores.php');
        exit();
    }
}

$rotas = mysqli_query($conn, "SELECT id_rotas, nome FROM rotas ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar sensor | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-topo-2">
</head>
<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <main class="content-user">
        <h1>Editar sensor</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro-usuario ativo">
            <form method="POST" class="form-cadastro-user">
                <label for="nome_sensor">Nome do sensor</label>
                <input type="text" id="nome_sensor" name="nome_sensor" minlength="3" maxlength="100"
                       value="<?php echo escapar($sensor['nome_sensor']); ?>" required>

                <label for="tipo_dado">Tipo de dado</label>
                <select id="tipo_dado" name="tipo_dado" required>
                    <?php foreach ($tipos as $tipo) { ?>
                        <option value="<?php echo $tipo; ?>"
                            <?php if ($tipo === $sensor['tipo_dado']) { echo 'selected'; } ?>>
                            <?php echo $tipo; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="id_rotas">Rota vinculada</label>
                <select id="id_rotas" name="id_rotas" required>
                    <?php while ($rota = mysqli_fetch_assoc($rotas)) { ?>
                        <option value="<?php echo $rota['id_rotas']; ?>"
                            <?php if ($rota['id_rotas'] == $sensor['id_rotas']) { echo 'selected'; } ?>>
                            <?php echo escapar($rota['nome']); ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit" class="btn-cadastrar-form">Salvar alterações</button>
                <a href="cadastrar_sensores.php">Cancelar</a>
            </form>
        </section>
    </main>
    <script src="../../script/validacao.js"></script>
</body>
</html>