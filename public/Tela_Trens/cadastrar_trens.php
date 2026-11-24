<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Usuários logados podem consultar a lista. O cadastro exige perfil de administrador.
exigir_login();

$ehAdministrador = $_SESSION['tipo'] === 'Administrador';
$mensagem = $_SESSION['mensagem_trem'] ?? '';
unset($_SESSION['mensagem_trem']);

// Recebe os dados do formulário, confere os campos e cadastra o registro.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

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
        $sql = "INSERT INTO trens (nome, velocidade_media, id_usuario) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        // s é texto, d é número decimal e i é número inteiro.
        mysqli_stmt_bind_param($stmt, 'sdi', $nome, $velocidade, $idUsuario);
        $salvou = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_trem'] = 'Trem cadastrado com sucesso!';
            header('Location: cadastrar_trens.php');
            exit();
        }

        $mensagem = 'Não foi possível cadastrar o trem.';
    }
}

// Lê o texto digitado na busca antes de mostrar a tabela.
$busca = trim($_GET['buscar'] ?? '');
$usuarios = mysqli_query($conn, "SELECT id_usuario, nome FROM usuarios ORDER BY nome");
$trens = mysqli_query($conn, "SELECT * FROM trens ORDER BY nome");
// Mantém o formulário aberto para mostrar um erro de cadastro.
$cadastroAberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trens | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=cadastro-3">
</head>
<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user">
            <p class="painel-user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de trens cadastrados</h1>
            <h2>Trens</h2>

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
                    

                    <form method="POST" class="form-cadastro-user">
                        <div class="campo-user">
                            <label for="nome">Nome do trem</label>
                            <input placeholder="Nome do trem" type="text" id="nome" name="nome" minlength="3" maxlength="100" required>
                        </div>

                        <div class="campo-user">
                            <label for="velocidade">Velocidade média</label>
                            <input placeholder="Velocidade média" type="number" id="velocidade" name="velocidade"
                                   step="any" required>
                        </div>

                        <div class="campo-user campo-responsavel-trem">
                            <label for="id_usuario">Usuário responsável</label>
                            <select id="id_usuario" name="id_usuario" required>
                                <option value="">Selecione o responsável</option>
    
                                <?php while ($usuario = mysqli_fetch_assoc($usuarios)) { ?>
                                    <option value="<?php echo $usuario['id_usuario']; ?>">
                                        <?php echo escapar($usuario['nome']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <button class="btn-cadastrar-form" type="submit">CADASTRAR NOVO TREM</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Área de busca e botão para abrir o formulário de cadastro. -->
            <section class="buscar-box-user buscar-usuarios-linha">
                <form method="GET">
                    <label for="buscar">Buscar</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome ou responsável"
                           value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($ehAdministrador) { ?>
                    <button class="btn-cadastrar-user btn-cadastrar-user-compacto"
                            id="btnAbrirCadastro" type="button" aria-controls="cadastro" aria-expanded="<?php echo $cadastroAberto ? 'true' : 'false'; ?>">
                        Cadastrar trem
                    </button>
                <?php } ?>
            </section>

            <?php include 'visualizacao_trens.php'; ?>
        </main>
    </div>

<script src="../../script/validacao.js?v=cadastro-3"></script>
</body>
</html>