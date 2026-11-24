<?php
require_once "infra/conexao.php";
require_once "infra/auth.php";

$erro = '';

// O login só consulta o banco depois que o formulário é enviado.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Busca a conta pelo e-mail. O ponto de interrogação recebe o valor do formulário.
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    // Confere a senha digitada com o hash guardado no banco.
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Renova o ID da sessão depois que o usuário entra.
        // Troca o identificador da sessão antes de guardar os dados do usuário.
        session_regenerate_id(true);

        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['tipo'] = $usuario['tipo'];

        header('Location: public/Tela_Home/home.php');
        exit();
    }

    $erro = 'E-mail ou senha incorretos.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - TremTech</title>
    <link rel="stylesheet" href="assets/style/style.css?v=20261115-layout2">
</head>

<body class="body_login">
    <main class="pagina-login">
        <section class="login-vizualizacao" id="login">
            <div class="painel-introducao">
                <img class="icone-trem" src="https://img.icons8.com/ios/100/ffffff/train.png" alt="Ícone de trem">
                <p class="inicio_login">SISTEMA TREMTECH</p>
                <h1>Controle sua ferrovia com praticidade.</h1>
                <p class="introducao_login">Painel para acompanhar usuários, trens e rotas disponíveis.</p>
            </div>

            <div id="login-cadastro">
                <form class="login-painel" id="login-formulario" method="POST">
                    <p class="introducao_formulario">Acesso de Administrador ou Funcionário</p>
                    <h2>Entrar no sistema</h2>

                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                    <?php if ($erro !== '') { ?>
                        <p role="alert"><?php echo $erro; ?></p>
                    <?php } ?>

                    <button id="botao-envio" type="submit">Entrar</button>
                </form>

                <a href="public/Tela_landing/landing.php" class="botao-landing">Conheça o Ferrorama</a>
            </div>
        </section>
    </main>
</body>
</html>