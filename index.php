<?php

require_once 'infra/auth.php';

// O formulário envia os dados por POST ao clicar em Entrar. Só então o PHP busca a conta.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'infra/conexao.php';

    // $_POST guarda o que foi digitado no formulário. trim tira espaços das pontas do e-mail.
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // A consulta procura um usuário pelo e-mail digitado no formulário.
    // prepare deixa a consulta pronta, ainda com o ? no lugar do e-mail.
    // bind_param liga $email ao ?. O 's' indica que o e-mail é texto.
    // execute manda o banco fazer a busca usando esse e-mail.
    $sql = "SELECT id_usuario, nome, email, tipo, senha FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();

    // get_result pega o resultado. fetch_assoc lê a linha com os dados da conta encontrada.
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    // Se a conta existir, password_verify compara a senha digitada com o hash salvo no banco.
    if ($usuario && password_verify($senha, $usuario['senha'])) {

        // Guarda quem entrou e o perfil da conta para as telas internas.
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['tipo'] = $usuario['tipo'];

        // Depois de entrar, abre a Home e encerra o código desta página.
        header('Location: public/Tela_Home/home.php');
        exit();
    }

    // Se o PHP chegou aqui, o login falhou. A mensagem aparece no formulário abaixo.
    $erro = 'Credenciais inválidas';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - TremTech</title>
    <link rel="stylesheet" href="assets/style/style.css?v=nomes-underscore-1">
</head>

<body class="body_login">
    <main class="pagina_login">
        <section class="login_vizualizacao" id="login">
            <div class="painel_introducao">
                <img class="icone_trem" src="https://img.icons8.com/ios/100/ffffff/train.png" alt="Ícone de trem">
                <p class="inicio_login">SISTEMA TREMTECH</p>
                <h1>Controle sua ferrovia com praticidade.</h1>
                <p class="introducao_login">Painel para acompanhar usuários, trens e rotas disponíveis.</p>
            </div>

            <div id="login_cadastro">
                <form class="login_painel" id="login_formulario" method="POST">
                    <p class="introducao_formulario">Acesso de Administrador ou Funcionário</p>
                    <h2>Entrar no sistema</h2>

                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                    <?php
                    // Na primeira vez que a tela abre, $erro ainda não existe.
                    // Se o e-mail ou a senha estiver errado, o PHP guarda o aviso em $erro.
                    // isset confere se esse aviso existe. Se existir, echo escreve o aviso na tela.
                    ?>
                    <?php if (isset($erro)) { ?>
                        <p role="alert"><?php echo $erro; ?></p>
                    <?php } ?>

                    <button id="botao_envio" type="submit">Entrar</button>
                </form>

                <a href="public/Tela_landing/landing.php" class="botao_landing">Conheça o Ferrorama</a>
            </div>
        </section>
    </main>
</body>
</html>