<?php

$pdo = new PDO("mysql:host=localhost;dbname=SA_FERRORAMA;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tipos = ['Velocidade', 'Temperatura', 'Status'];
$rotas = $pdo->query("SELECT id_rotas, nome FROM rotas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$erros = [];
$nome = '';
$velocidade = '';
$tipo = '';
$id_rotas = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome'] ?? '');
    $velocidade = trim($_POST['velocidade'] ?? '');
    $tipo       = $_POST['tipo'] ?? '';
    $id_rotas   = $_POST['id_rotas'] ?? '';

    
    if ($nome === '') {
        $erros['nome'] = 'Informe o nome do trem.';
    } elseif (strlen($nome) < 3) {
        $erros['nome'] = 'O nome precisa ter ao menos 3 caracteres.';
    } else {
        $busca = $pdo->prepare("SELECT COUNT(*) FROM trens WHERE nome = ?");
        $busca->execute([$nome]);
        if ($busca->fetchColumn() > 0) {
            $erros['nome'] = 'Já existe um trem com esse nome.';
        }
    }

    
    if ($velocidade === '') {
        $erros['velocidade'] = 'Informe a velocidade.';
    } elseif (!is_numeric($velocidade) || $velocidade < 1 || $velocidade > 600) {
        $erros['velocidade'] = 'Use um valor entre 1 e 600 km/h.';
    }

    
    if (!in_array($tipo, $tipos)) {
        $erros['tipo'] = 'Selecione o tipo/categoria.';
    }

    
    $rotaValida = false;
    foreach ($rotas as $r) {
        if ($r['id_rotas'] == $id_rotas) {
            $rotaValida = true;
        }
    }
    if (!$rotaValida) {
        $erros['rota'] = 'Selecione a rota.';
    }

    
    if (count($erros) === 0) {
        $insert = $pdo->prepare(
            "INSERT INTO trens (nome, velocidade_media, tipo, id_rotas) VALUES (?, ?, ?, ?)"
        );
        $insert->execute([$nome, $velocidade, $tipo, $id_rotas]);

        
        header("Location: cadastrar_trens.php?ok=1");
        exit;
    }
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