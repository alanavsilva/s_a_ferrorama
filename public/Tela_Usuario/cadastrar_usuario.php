<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Usuários logados podem consultar a lista. O cadastro exige perfil de administrador.
exigir_login();

$administrador = $_SESSION['tipo'] === 'Administrador';
$mensagem = $_SESSION['mensagem_usuario'] ?? '';
unset($_SESSION['mensagem_usuario']);

// Recebe os dados do formulário, confere os campos e cadastra o registro.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if ($nome === '' || mb_strlen($nome) > 100) {
        $mensagem = 'Informe um nome de até 100 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $mensagem = 'Informe um e-mail válido de até 100 caracteres.';
    } elseif ($senha === '') {
        $mensagem = 'Informe uma senha.';
    } elseif ($tipo !== 'Administrador' && $tipo !== 'Funcionário') {
        $mensagem = 'Selecione um tipo de usuário válido.';
    } else {
        // Guarda o hash da senha. O login confere com password_verify().
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $nome, $email, $senhaHash, $tipo);

        // O catch trata a falha se o banco recusar o cadastro.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }

        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_usuario'] = 'Usuário cadastrado com sucesso!';
            header('Location: cadastrar_usuario.php');
            exit();
        }

        $mensagem = 'Não foi possível cadastrar. Confira se o e-mail já está em uso.';
    }
}

// Lê o texto digitado na busca antes de mostrar a tabela.
$busca = trim($_GET['buscar'] ?? '');

// O % permite encontrar o texto em qualquer parte do nome ou e-mail.
$texto = '%' . $busca . '%';

$sql = "SELECT id_usuario, nome, email, tipo FROM usuarios
        WHERE nome LIKE ? OR email LIKE ?
        ORDER BY nome";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $texto, $texto);
mysqli_stmt_execute($stmt);
$usuarios = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
// Mantém o formulário aberto para mostrar um erro de cadastro.
$cadastroAberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=cadastro-3">
</head>

<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user">
            <p class="painel-user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de usuários cadastrados</h1>
            <h2>Administradores e Funcionários</h2>

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

            <?php if ($administrador) { ?>
                <section id="cadastro" class="cadastro-usuario<?php if ($cadastroAberto) { echo ' ativo'; } ?>"
                    <?php if (!$cadastroAberto) { echo 'hidden'; } ?>>
                    <h2>Cadastrar novo usuário</h2>

                    <form method="POST" class="form-cadastro-user">
                        <div class="campo-user">
                            <label for="nome">Nome de usuário</label>
                            <input type="text" id="nome" name="nome" placeholder="Nome completo" maxlength="100" required>
                        </div>
                        <div class="campo-user">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Email" maxlength="100" required>
                        </div>
                        <div class="campo-user">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" placeholder="Senha" required>
                        </div>

                        <div class="campo-user">
                            <label for="tipo">Tipo de usuário</label>
                            <select id="tipo" name="tipo">
                                <option>Funcionário</option>
                                <option>Administrador</option>
                            </select>
                        </div>

                        <button class="btn-cadastrar-form" type="submit">CADASTRAR NOVO USUÁRIO</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Área de busca e botão para abrir o formulário de cadastro. -->
            <section class="buscar-box-user buscar-usuarios-linha">
                <form method="GET">
                    <label for="buscar">Buscar</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome ou email"
                           value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($administrador) { ?>
                    <button class="btn-cadastrar-user btn-cadastrar-user-compacto"
                            id="btnAbrirCadastro" type="button" aria-controls="cadastro" aria-expanded="<?php echo $cadastroAberto ? 'true' : 'false'; ?>">
                        Cadastrar usuário
                    </button>
                <?php } ?>
            </section>

            <?php
            if (mysqli_num_rows($usuarios) > 0) {
                include 'visualizacao_usuario.php';
            } else {
                echo '<p>Nenhum usuário encontrado.</p>';
            }
            ?>
        </main>
    </div>

<script src="../../script/validacao.js?v=cadastro-3"></script>
</body>
</html>