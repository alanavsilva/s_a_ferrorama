<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_sensores.php";

exigir_login();

$ehAdministrador =
    strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$mensagemSucesso = $_SESSION['mensagem_cadastro'] ?? '';
unset($_SESSION['mensagem_cadastro']);

$mensagem = '';
$mensagemAcesso = '';
$busca = trim($_GET['buscar'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar sensores.';
    } else {
        verificar_csrf();

        $nomeSensor = trim($_POST['nome_sensor'] ?? '');
        $tipoDado = trim($_POST['tipo_dado'] ?? '');
        $idTrem = filter_input(INPUT_POST, 'id_trem', FILTER_VALIDATE_INT);
        $idRotas = filter_input(INPUT_POST, 'id_rotas', FILTER_VALIDATE_INT);
        $tiposPermitidos = ['Velocidade', 'Temperatura', 'Status'];


       if ($nomeSensor === '' || !in_array($tipoDado, $tiposPermitidos, true) || !$idTrem || !$idRotas) {
            $mensagem = 'Preencha todos os campos corretamente.';
        } else {
            $sql = "INSERT INTO sensores (nome_sensor, tipo_dado, id_trem, id_rotas) VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                error_log('Erro ao preparar cadastro de sensor: ' . $conn->error);
                $mensagem = 'Não foi possível concluir o cadastro.';
            } else {
                $stmt->bind_param('ssii', $nomeSensor, $tipoDado, $idTrem, $idRotas);

                if ($stmt->execute()) {
                    $_SESSION['mensagem_cadastro'] = 'Sensor cadastrado com sucesso!';
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

$sqlTrens = "SELECT id_trem, nome FROM trens ORDER BY nome";
$resultadoTrens = $conn->query($sqlTrens);

if (!$resultadoTrens) {
    error_log('Erro ao carregar trens: ' . $conn->error);
    $mensagemAcesso = 'Não foi possível carregar os trens.';
}

$sqlRotas = "SELECT id_rotas, nome FROM rotas ORDER BY nome";
$resultadoRotas = $conn->query($sqlRotas);

if (!$resultadoRotas) {
    error_log('Erro ao carregar rotas: ' . $conn->error);
    $mensagemAcesso = 'Não foi possível carregar as rotas.';
}

$textoBusca = '%' . $busca . '%';

$sql = "
    SELECT
        s.id_sensor,
        s.nome_sensor,
        s.tipo_dado,
        t.nome AS trem_vinculado,
        r.nome AS rota_vinculada
    FROM sensores AS s
    LEFT JOIN trens AS t ON t.id_trem = s.id_trem
    LEFT JOIN rotas AS r ON r.id_rotas = s.id_rotas
    WHERE s.nome_sensor LIKE ?
       OR s.tipo_dado LIKE ?
       OR t.nome LIKE ?
       OR r.nome LIKE ?
    ORDER BY s.id_sensor
";

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
    error_log('Erro ao preparar consulta dos sensores: ' . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível preparar a consulta dos sensores.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
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

          <?php if ($mensagemSucesso !== ''): ?>
            <p class="mensagem-sucesso" role="status"> <?= escapar($mensagemSucesso) ?></p>
        <?php endif; ?>

     <?php if ($mensagemAcesso !== ''): ?>
            <p role="alert"><?= escapar($mensagemAcesso) ?></p>
        <?php endif; ?>

  <div class="admin-badge-user">
    <div class="avatar-user"></div>
    <div>
        <strong>
            <?= $ehAdministrador ? 'Administrador' : 'Funcionário' ?>
        </strong>
        <span><?= escapar($_SESSION['nome'] ?? '') ?></span>
    </div>
</div>
    <?php if ($ehAdministrador): ?>
      <button class="btn-cadastrar-user" id="btnAbrirCadastro" type="button" aria-expanded="false" aria-controls="cadastro"> CADASTRAR NOVO SENSOR </button>

       <section class="cadastro-usuario" id="cadastro" hidden>
          
      <form class="form-cadastro-user" method="POST" onsubmit="return validar_sensor()">
    <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">

    <div class="campo-user">
        <label for="nome_sensor">Nome do sensor</label>
        <input type="text" id="nome_sensor" name="nome_sensor" placeholder="Nome do sensor" required >
    </div>

    <div class="campo-user">
        <label for="tipo_dado">Tipo de dado coletado</label>
        <select id="tipo_dado" name="tipo_dado" required>
            <option value="">Selecione</option>
            <option value="Velocidade">Velocidade</option>
            <option value="Temperatura">Temperatura</option>
            <option value="Status">Status</option>
        </select>
    </div>

    <div class="campo-user">
    <label for="id_trem">Trem vinculado</label>
    <select id="id_trem" name="id_trem" required>
        <option value="">Selecione um trem</option>

        <?php if ($resultadoTrens): ?>
            <?php while ($trem = $resultadoTrens->fetch_assoc()): ?>
                <option value="<?= (int) $trem['id_trem'] ?>">
                    <?= escapar($trem['nome']) ?>
                </option>
            <?php endwhile; ?>
        <?php endif; ?>
    </select>
</div>

    <div class="campo-user">
        <label for="id_rotas">Rota vinculada</label>
        <select id="id_rotas" name="id_rotas" required>
            <option value="">Selecione uma rota</option>

            <?php if ($resultadoRotas): ?>
                <?php while ($rota = $resultadoRotas->fetch_assoc()): ?>
                    <option value="<?= (int) $rota['id_rotas'] ?>">
                    <?= escapar($rota['nome']) ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
    </div>

    <?php if ($mensagem !== ''): ?>
        <p role="alert"><?= escapar($mensagem) ?></p>
    <?php endif; ?>

    <button class="btn-cadastrar-form" type="submit">
        CADASTRAR NOVO SENSOR
    </button>
</form>
        </section>
    <?php endif; ?>

    <section class="buscar-box-user">
        <form method="GET">
            <label for="buscar">Buscar</label>
            <input type="text" id="buscar" name="buscar" placeholder="Nome, tipo, trem ou rota" value="<?= escapar($busca) ?>" >
            <button type="submit">Buscar</button>
        </form>
    </section>

    <?php mostrar_sensores($resultado); ?>
    </main>
</div>
<script src="../../script/validacao.js"></script>
</body>
</html>