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
    
    $nome = '';
    $velocidade = '';
    $tipo = '';
    $id_rotas = '';
    $erros = [];

    $tipos = ['Velocidade', 'Temperatura', 'Status'];
    $rotas = [];

    $sqlRotas = "SELECT id_rotas, nome FROM rotas ORDER BY nome";
    $resultadoRotas = $conn->query($sqlRotas);

  if ($resultadoRotas) {
    while ($rota = $resultadoRotas->fetch_assoc()) {
        $rotas[] = $rota;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar trens.';
  }else{
  verificar_csrf();

    $nome = trim($_POST['nome'] ?? '');
    $velocidade = trim($_POST['velocidade'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $id_rotas = $_POST['id_rotas'] ?? '';
    
    if ($nome === '' || $velocidade === '' || $tipo === '' || $id_rotas === '') {
        $mensagem = "Preencha todos os campos.";
    } elseif (strlen($nome) < 3) {
        $erros['nome'] = 'O nome precisa ter ao menos 3 caracteres.';
    } elseif (!is_numeric($velocidade) || $velocidade < 1 || $velocidade > 600) {
        $erros['velocidade'] = 'Use um valor entre 1 e 600 km/h.';
      } elseif (!in_array($tipo, $tipos, true)) {
            $mensagem = "Tipo de trem inválido.";
        } else {
    $rotaValida = false;
    foreach ($rotas as $r) {
        if ((string)$r['id_rotas'] === (string)$id_rotas) {
            $rotaValida = true;
            break;
        }
    }
    if (!$rotaValida) {
      $mensagem = "Selecione uma rota válida.";
    } else {
                $velocidadeBanco = (float)$velocidade;
                $idRotaBanco = (int)$id_rotas;

                $sql = "INSERT INTO trens (nome, velocidade_media, tipo, id_rotas)
                        VALUES (?, ?, ?, ?)";
                        
                $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $mensagem = "Não foi possível concluir o cadastro.";
        } else {
            $stmt->bind_param("sdsi", $nome, $velocidadeBanco, $tipo, $idRotaBanco);

           try {
    if ($stmt->execute()) {
        $_SESSION['mensagem_cadastro'] = 'Trem cadastrado com sucesso!';
        header('Location: cadastrar_trens.php');
        exit;
    }
    } catch (mysqli_sql_exception $e) {
        error_log('Erro ao cadastrar usuário: ' . $e->getMessage());
        $mensagem = 'Não foi possível concluir o cadastro.';
}
            $stmt->close();
        }
    }
  }
    }
}

$manterFormularioAberto = $_SERVER['REQUEST_METHOD'] === 'POST';

    $busca = trim($_GET['buscar'] ?? '');
$textoBusca = "%" . $busca . "%";

$sql = "SELECT t.id_trem, t.nome, t.velocidade_media, t.tipo, t.id_rotas, r.nome AS nome_rota
        FROM trens t LEFT JOIN rotas r ON r.id_rotas = t.id_rotas
        WHERE t.nome LIKE ? OR  t.tipo LIKE ? OR r.nome LIKE ?
        ORDER BY t.nome";

$stmtBusca = $conn->prepare($sql);

if ($stmtBusca) {
    $stmtBusca->bind_param("sss", $textoBusca, $textoBusca, $textoBusca);

    if ($stmtBusca->execute()) {
        $resultado = $stmtBusca->get_result();
    } else {
        error_log("Erro ao consultar trens: " . $stmtBusca->error);
        $resultado = false;
        $mensagemAcesso = 'Não foi possível carregar os trens.';
    }

    $stmtBusca->close();
} else {
    error_log("Erro ao preparar consulta: " . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível carregar os trens.';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>SISTEMA TREMTECH</title>

  <link rel="stylesheet" href="../../assets/style/style.css">
  <link rel="stylesheet" href="visualizacao.css">
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

      <a href="../../public/Tela_Sensores/cadastrar_sensores.php">
        <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
        Sensores
      </a>

      <a class="ativo" href="../../public/Tela_Trens/cadastrar_trens.php">
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

    <main class="conteudo">
      <header>
        <div>
          <small>PAINEL ADMINISTRATIVO</small>
          <h1>Controle de trens cadastrados</h1>
          <h2>Trens</h2>
        </div>

        <div class="admin">
          <div class="foto"></div>

          <b>
            Admin<br>
            <span>Usuário</span>
          </b>
        </div>
      </header>

      <form class="formulario" method="POST">
        <div>
          <label for="nome">Nome do trem</label>
          <input type="text" id="nome" name="nome" placeholder="Nome do trem"
                 value="<?= htmlspecialchars($nome) ?>">
          <span class="erro-campo"><?= $erros['nome'] ?? '' ?></span>
        </div>

        <div>
          <label for="velocidade">Velocidade média</label>
          <input type="number" id="velocidade" name="velocidade" placeholder="Velocidade média"
                 value="<?= htmlspecialchars($velocidade) ?>">
          <span class="erro-campo"><?= $erros['velocidade'] ?? '' ?></span>
        </div>

        <div>
          <label for="tipo">Tipo/categoria</label>
          <select id="tipo" name="tipo">
            <option value="">Selecione o tipo</option>
            <?php foreach ($tipos as $t): ?>
              <option value="<?= $t ?>" <?= $tipo === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
          <span class="erro-campo"><?= $erros['tipo'] ?? '' ?></span>
        </div>

        <div>
          <label for="id_rotas">Rota</label>
          <select id="id_rotas" name="id_rotas">
            <option value="">Selecione a rota</option>
            <?php foreach ($rotas as $r): ?>
              <option value="<?= $r['id_rotas'] ?>" <?= $id_rotas == $r['id_rotas'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($r['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <span class="erro-campo"><?= $erros['rota'] ?? '' ?></span>
        </div>

        <button class="botao" type="submit">Cadastrar Novo Trem</button>
      </form>

      <?php if (isset($_GET['ok'])): ?>
        <p class="sucesso">Trem cadastrado com sucesso.</p>
      <?php endif; ?>

      <?php require_once __DIR__ . '/visualizacao_trens.php'; ?>
    </main>

  </div>

  <script src="validacao.js"></script>
</body>
</html>

  </tbody>
</table>