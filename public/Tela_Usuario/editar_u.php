<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// A edição só pode ser feita por um administrador.
exigir_administrador();

// O ID da URL indica qual registro deve aparecer preenchido no formulário.
$id = (int) ($_GET['id'] ?? 0);
$sql = "SELECT nome, email, tipo, senha FROM usuarios WHERE id_usuario = $id";
$resultado = mysqli_query($conn, $sql);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    exit('Usuário não encontrado.');
}

$nome = $usuario['nome'];
$email = $usuario['email'];
$tipo = $usuario['tipo'];
$mensagem = '';

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
        // Campo vazio mantém o hash atual. Se digitar uma senha, cria outro hash.
        $senhaHash = $usuario['senha'];

        if ($senha !== '') {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        }

        $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ?, senha = ?
                WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $email, $tipo, $senhaHash, $id);

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
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-topo-2">
</head>
<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <main class="content-user">
        <h1>Editar usuário</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro-usuario ativo">
            <form method="POST" class="form-cadastro-user">
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

                <button class="btn-cadastrar-form" type="submit">Salvar</button>
                <a href="cadastrar_usuario.php">Cancelar</a>
            </form>
        </section>
    </main>
    <script src="../../script/validacao.js"></script>
</body>
</html>