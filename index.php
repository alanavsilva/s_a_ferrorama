
<?php
require_once "infra/conexao.php";
require_once "infra/auth.php";

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$email = trim($_POST["email"] ?? '');
$senha = ($_POST["senha"]??'');

if ($email === '' || $senha === '') {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Credenciais inválidas.";
    } else {

$sql = "SELECT id_usuario, nome, email, tipo, senha
        FROM usuarios
        WHERE email = ? 
        LIMIT 1";

  $stmt = $conn->prepare($sql);

   if (!$stmt) {

            $erro = "Erro ao consultar o banco de dados.";

        } else {

           $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $usuario = $resultado->fetch_assoc();


       if ($usuario && password_verify($senha, $usuario['senha'])) {

                session_regenerate_id(true);

                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['tipo'] = $usuario['tipo'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                $stmt->close();

                header("Location: public/Tela_Home/home.php");
                exit;

            } else {

                $erro = "Email ou senha incorretos.";
            }

            $stmt->close();
        }
    }
}
?>


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página login</title>
   <link rel="stylesheet" href="../assets/style/style.css">

</head>

<body class="body_login">
    <main class="pagina-login">
        <section class="login-vizualização" id="login">
            <div class="painel-introducao">

            <img
        class="icone-trem"
        src="https://img.icons8.com/ios/100/ffffff/train.png"
        alt="Ícone de trem"
    >
                <p class="inicio_login">SISTEMA TREMTECH</p>
                <h1>Controle sua ferrovia com praticidade.</h1>
                <p class="introducao_login">
                    Painel para acompanhar usuarios, trens, passagens e rotas disponiveis.
                </p>
            </div>


            <div id="login-cadastro">
            <form class="login-painel" id="login-formulario" method="POST">
                    <p class="introducao_formulario">Acesso administrador/funcionario</p>
                    <h2>Entrar no sistema</h2>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu email" required>
                    <br>

                    <br>

                    <label for="senha">
                        Senha
                    </label>
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                   
                <p role="alert">
                            <?= escapar($erro) ?>
                        </p>

                    <button id="botao-envio" type="submit">Entrar</button>
</form>                   
</div>
    </section>
</main>
    <link rel="stylesheet" href="assets/style/style.css">
</body>
</html>

