<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_usuario.php";

exigir_login();

$ehAdministrador =
    strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$mensagemSucesso = $_SESSION['mensagem_cadastro'] ?? '';
unset($_SESSION['mensagem_cadastro']);
$mensagem = '';
$mensagemAcesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar usuários.';
  }else{
  verificar_csrf();

    $nome = trim ($_POST['nome'] ?? '');
    $email = trim ($_POST['email'] ?? '');   
    $senha = $_POST['senha'] ?? '';
    $tipoRecebido = $_POST['tipo'] ?? '';

     if ($nome === '' || $email === '' || $senha === '') {
        $mensagem = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Informe um email válido.";
    } elseif (strlen($senha) < 8) {
        $mensagem = "A senha deve ter pelo menos 8 caracteres.";
    } elseif (!in_array($tipoRecebido, ['Funcionário', 'Administrador'], true)) {
        $mensagem = "Tipo de usuário inválido.";
    } else {
            $tipo = $tipoRecebido;
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha, tipo)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $mensagem = "Não foi possível concluir o cadastro.";
        } else {
            $stmt->bind_param("ssss", $nome, $email, $senhaHash, $tipo);

           try {
    if ($stmt->execute()) {
        $_SESSION['mensagem_cadastro'] = 'Usuário cadastrado com sucesso!';
        header('Location: cadastrar_usuario.php');
        exit;
    }
    } catch (mysqli_sql_exception $e) {
    if ((int) $e->getCode() === 1062) {
        $mensagem = 'Este email já está cadastrado.';
     } else {
        error_log('Erro ao cadastrar usuário: ' . $e->getMessage());
        $mensagem = 'Não foi possível concluir o cadastro.';
    }
}

            $stmt->close();
        }
    }
  }
    }

$manterFormularioAberto = $_SERVER['REQUEST_METHOD'] === 'POST';

    $busca = trim($_GET['buscar'] ?? '');
$textoBusca = "%" . $busca . "%";

$sql = "SELECT id_usuario, nome, email, tipo
        FROM usuarios
        WHERE nome LIKE ? OR email LIKE ?
        ORDER BY nome";

$stmtBusca = $conn->prepare($sql);

if ($stmtBusca) {
    $stmtBusca->bind_param("ss", $textoBusca, $textoBusca);

    if ($stmtBusca->execute()) {
        $resultado = $stmtBusca->get_result();
    } else {
        error_log("Erro ao consultar usuários: " . $stmtBusca->error);
        $resultado = false;
        $mensagemAcesso = 'Não foi possível carregar os usuários.';
    }

    $stmtBusca->close();
} else {
    error_log("Erro ao preparar consulta: " . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível carregar os usuários.';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Usuário</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261021-4">

   
</head>
    
   <body class="body_geral">
   <div class="pagina">

    <aside class="menu">
       <img class="logo-trem"
    src="https://img.icons8.com/ios/100/ffffff/train.png"
    alt="Logo TremTech">

  <p class="marca">SISTEMA TREMTECH</p>
     <a href="../../public/Tela_Home/home.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
  Home
</a>

<a class="ativo" href="../../public/Tela_Usuario/cadastrar_usuario.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
  Usuários
</a>

<a href="../../public/Tela_Sensores/cadastrar_sensores.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
  Sensores
</a>

<a href="../../public/Tela_Trens/cadastrar_trens.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>
  Trem
</a>

<a href="../../public/Tela_Rotas/cadastrar_rotas.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>
  Rota
</a>
<a href="../../public/Tela_Monitoramento/monitoramento.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>
  Monitoramento
</a>

<a href="../../public/Tela_Relatorios/relatorios.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>
  Relatórios
</a>


<a class="logout" href="../../logout.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png');"></span>
  Logout
</a>
    </aside>

    <main class="content-user">
      <p class="painel-user">PAINEL ADMINISTRATIVO</p>
      <h1>Controle de usuários cadastrados</h1>
      <h2>Administradores e Funcionários</h2>
      <?php if ($mensagemSucesso !== ''): ?>
          <p class="mensagem-sucesso" role="status"><?= escapar($mensagemSucesso) ?></p>
      <?php endif; ?>

     <?php if ($mensagemAcesso !== ''): ?>
            <p role="alert"><?= escapar($mensagemAcesso) ?></p>
        <?php endif; ?>

   <div class="admin-badge-user">
    <div class="avatar-user"></div>
    <div>
        <strong>
            <?= strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0
                ? 'Administrador'
                : 'Funcionário' ?>
        </strong>
        <span><?= escapar($_SESSION['nome'] ?? '') ?></span>
    </div>
</div>
        <?php if ($ehAdministrador): ?>

    <div class="admin-badge-user">
        <div class="avatar-user"></div>
        <div>
            <strong>Admin</strong>
            <span><?= escapar($_SESSION['nome'] ?? 'Administrador') ?></span>
        </div>
    </div>

            <button class="btn-cadastrar-user" type="button" onclick="alternarCadastroUsuario()">CADASTRAR NOVO USUÁRIO</button>

<section class="cadastro-usuario <?= $manterFormularioAberto ? 'ativo' : '' ?>" id="cadastro">    
        <form class="form-cadastro-user" method="POST" onsubmit="return validar_usuario()">
        <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">

        <div class="campo-user">
        <label for="nome">Nome de usuário</label>
        <input type="text" id="nome" name="nome" placeholder="Nome completo" required>
        </div>

        <div class="campo-user">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
        </div>

        <div class="campo-user">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Senha" required>
        </div>

        <div class="campo-user">
        <label for="tipo">Tipo de usuário</label>
        <select id="tipo" name="tipo">
        <option value="Funcionário">Funcionário</option>
        <option value="Administrador">Administrador</option>
        </select>
        </div>

                    <?php if ($mensagem !== ''): ?>
                        <p class="mensagem-cadastro" role="alert"><?= escapar($mensagem) ?></p>
                    <?php endif; ?>

                    <button class="btn-cadastrar-form" type="submit"> CADASTRAR NOVO USUÁRIO</button>
                </form>
            </section>
        <?php endif; ?>

        <section class="buscar-box-user">
        <form method="GET">
        <label for="buscar">Buscar</label>
        <input type="text" id="buscar" name="buscar" placeholder="Nome ou email" value="<?= escapar($busca) ?>">
                <button type="submit">Buscar</button>
            </form>
        </section>


        <?php mostrar_usuarios($resultado); ?>
    </main>
</div>

<script src="../../script/validacao.js?v=20261021-4"></script>
</body>
</html>