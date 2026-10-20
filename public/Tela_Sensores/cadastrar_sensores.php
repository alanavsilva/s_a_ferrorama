<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_sensores.php";

exigir_login();

$ehAdministrador =
    strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$mensagem = '';
$mensagemAcesso = '';
$busca = trim($_GET['buscar'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar sensores.';
    } else {
        verificar_csrf();

        $nome = trim($_POST['nome'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        $trem = trim($_POST['trem'] ?? '');
        $rota = trim($_POST['rota'] ?? '');

        $tiposPermitidos = ['Velocidade', 'Temperatura', 'Status'];

        if ($nome === '' || $trem === '' || $rota === '') {
            $mensagem = 'Preencha todos os campos.';
        } elseif (!in_array($tipo, $tiposPermitidos, true)) {
            $mensagem = 'Tipo de dado inválido.';
        } else {
            $sql = "INSERT INTO sensores (nome, tipo, trem, rota)
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                error_log('Erro ao preparar cadastro de sensor: ' . $conn->error);
                $mensagem = 'Não foi possível concluir o cadastro.';
            } else {
                $stmt->bind_param('ssss', $nome, $tipo, $trem, $rota);

                if ($stmt->execute()) {
                    $_SESSION['mensagem_cadastro'] =
                        'Sensor cadastrado com sucesso!';
                    header('Location: cadastrar_sensores.php');
                    exit;
                }

                error_log('Erro ao cadastrar sensor: ' . $stmt->error);
                $mensagem = 'Não foi possível concluir o cadastro.';
                $stmt->close();
            }
        }
    }
}

if (isset($_SESSION['mensagem_cadastro'])) {
    $mensagem = $_SESSION['mensagem_cadastro'];
    unset($_SESSION['mensagem_cadastro']);
}

$textoBusca = '%' . $busca . '%';

$sql = "SELECT id_sensor, nome, tipo, trem, rota FROM sensores WHERE nome LIKE ? OR tipo LIKE ? OR trem LIKE ? OR rota LIKE ? ORDER BY nome";

$stmtBusca = $conn->prepare($sql);

if ($stmtBusca) {
    $stmtBusca->bind_param('ssss', $textoBusca, $textoBusca, $textoBusca, $textoBusca);

    if ($stmtBusca->execute()) {
        $resultado = $stmtBusca->get_result();
    } else {
        error_log('Erro ao consultar sensores: ' . $stmtBusca->error);
        $resultado = false;
        $mensagemAcesso = 'Não foi possível carregar os sensores.';
    }

    $stmtBusca->close();
} else {
    error_log('Erro ao preparar consulta de sensores: ' . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível carregar os sensores.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Cadastro Sensores</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="../../assets/style/style.css">

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

<a href="../../public/Tela_Usuario/cadastrar_usuario.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
  Usuários
</a>

<a class="ativo" href="../../public/Tela_Sensores/cadastrar_sensores.php">
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
          <h1>Controle de sensores cadastrados</h1>
          <h2>Sensores do sistema</h2>

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

      <button class="btn-cadastrar-user" type="button">
                CADASTRAR NOVO SENSOR
            </button>

       <section class="cadastro-usuario" id="cadastro">
          
      <form class="form-cadastro-user" method="POST" onsubmit="return validar_usuario()">
            <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
            
      <div class="campo-user">
              <label for="nome">Nome do sensor</label>
              <input type="text" id="nome" name="nome" placeholder="Nome do sensor" required>
            </div>
            
            <div class="campo-user">             
              <label for="tipo">Tipo de dado coletado</label>
              <select id="tipo" name="tipo" required>
                <option value="Velocidade">Velocidade</option>
                <option value="Temperatura">Temperatura</option>
                <option value="Status">Status</option>
                </select>
              </div>
              
            <div class="campo-user">          
              <label for="trem">Trem vinculado</label>
                <select id="trem" name="trem" required>
                  <option>Trem 01</option>
                  <option>Trem 02</option>
                  <option>Trem 03</option>
                </select>
              </div>
              
            <div class="campo-user">          
              <label for="rota">Rota vinculada</label>
                <select id="rota" name= "rota" required>
                  <option>Rota Norte</option>
              <option>Rota Sul</option>
              <option>Rota Leste</option>
              <option>Rota Oeste</option>
            </select>
          </div>
           <?php if ($mensagem !== ''): ?>
                        <p role="alert"><?= escapar($mensagem) ?></p>
                    <?php endif; ?>

                    <button class="btn-cadastrar-form" type="submit">
                        CADASTRAR NOVO USUÁRIO
                    </button>
                </form>
            </section>
        <?php endif; ?>
      
        <section class="buscar-box-user">
            <form method="GET">
                <label for="buscar">Buscar</label>
                <input type="text" id="buscar" name="buscar" placeholder="Nome"
                    value="<?= escapar($busca) ?>">
                <button type="submit">Buscar</button>
            </form>
        </section>
      
      <?php mostrar_sensores($resultado); ?>
    </main>
</div>

<script src="../../script/validacao.js"></script>
</body>
</html>