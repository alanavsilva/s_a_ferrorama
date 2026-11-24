<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// A edição só pode ser feita por um administrador.
exigir_administrador();

if (!isset($_GET['id'])) {
    exit('Rota inválida.');
}

// O ID da URL indica qual registro deve aparecer preenchido no formulário.
$id = (int) $_GET['id'];
$sql = "SELECT * FROM rotas WHERE id_rotas = $id";
$resultado = mysqli_query($conn, $sql);
$rota = mysqli_fetch_assoc($resultado);

if (!$rota) {
    exit('Rota não encontrada.');
}

$mensagem = '';

// Quando o formulário é enviado, a própria página atualiza a rota.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rota['nome'] = trim($_POST['nome']);
    $rota['descricao'] = trim($_POST['descricao']);
    $rota['extensao'] = (float) $_POST['extensao'];
    $rota['tempo'] = (int) $_POST['tempo'];
    $rota['id_trem'] = (int) $_POST['id_trem'];

    $idTrem = $rota['id_trem'];
    $sql = "SELECT id_trem FROM trens WHERE id_trem = $idTrem";
    $tremEncontrado = mysqli_query($conn, $sql);

    if ($rota['nome'] === '' || $rota['descricao'] === ''
        || $rota['extensao'] <= 0 || $rota['tempo'] <= 0) {
        $mensagem = 'Preencha os campos com valores válidos.';
    } elseif (mysqli_num_rows($tremEncontrado) === 0) {
        $mensagem = 'Selecione um trem cadastrado.';
    } else {
        $sql = "UPDATE rotas SET nome = ?, descricao = ?, extensao = ?, tempo = ?, id_trem = ?
                WHERE id_rotas = ?";
        $stmt = mysqli_prepare($conn, $sql);

        // s é texto, d é número decimal e i é número inteiro.
        mysqli_stmt_bind_param($stmt, 'ssdiii', $rota['nome'], $rota['descricao'],
            $rota['extensao'], $rota['tempo'], $idTrem, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['mensagem_rota'] = 'Rota atualizada com sucesso!';
        header('Location: cadastrar_rotas.php');
        exit();
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
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-topo-2">
</head>

<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <main class="content-user">
        <h1>Editar rota</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro-usuario ativo">
            <form method="POST" class="form-cadastro-user">
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

                <button type="submit" class="btn-cadastrar-form">Salvar alterações</button>
                <a href="cadastrar_rotas.php">Cancelar</a>
            </form>
        </section>
    </main>
    <script src="../../script/validacao.js"></script>
</body>
</html>