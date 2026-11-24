<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// A edição só pode ser feita por um administrador.
exigir_administrador();

// O ID da URL indica qual registro deve aparecer preenchido no formulário.
$id = (int) ($_GET['id'] ?? 0);
$sql = "SELECT * FROM trens WHERE id_trem = $id";
$resultado = mysqli_query($conn, $sql);
$trem = mysqli_fetch_assoc($resultado);

if (!$trem) {
    exit('Trem não encontrado.');
}

$nome = $trem['nome'];
$velocidade = $trem['velocidade_media'];
$idUsuario = $trem['id_usuario'];
$mensagem = '';

// Ao enviar o formulário, este mesmo arquivo salva o trem indicado na URL.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $velocidade = $_POST['velocidade'] ?? '';
    $idUsuario = (int) ($_POST['id_usuario'] ?? 0);

    $sql = "SELECT id_usuario FROM usuarios WHERE id_usuario = $idUsuario";
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
        mysqli_stmt_bind_param($stmt, 'sdii', $nome, $velocidade, $idUsuario, $id);
        $salvou = mysqli_stmt_execute($stmt);
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
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-topo-2">
</head>
<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <main class="content-user">
        <h1>Editar trem</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro-usuario ativo">
            <form method="POST" class="form-cadastro-user">
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
                            <?php if ($usuario['id_usuario'] == $idUsuario) { echo 'selected'; } ?>>
                            <?php echo escapar($usuario['nome']); ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit" class="btn-cadastrar-form">Salvar alterações</button>
                <a href="cadastrar_trens.php">Cancelar</a>
            </form>
        </section>
    </main>
    <script src="../../script/validacao.js"></script>
</body>
</html>