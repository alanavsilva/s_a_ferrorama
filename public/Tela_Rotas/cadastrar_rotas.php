<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_rotas.php";

exigir_login();

$ehAdministrador =
    strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

$mensagemSucesso = $_SESSION['mensagem_cadastro'] ?? '';
unset($_SESSION['mensagem_cadastro']);

$mensagem = '';
$mensagemAcesso = '';
$nome = '';
$descricao = '';
$extensao = '';
$tempo = '';
$idTrem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$ehAdministrador) {
        http_response_code(403);
        $mensagemAcesso = 'Você não tem permissão para cadastrar rotas.';
    } else {
        verificar_csrf();

        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $extensao = filter_var(
            $_POST['extensao'] ?? '',
            FILTER_VALIDATE_FLOAT
        );
        $tempo = filter_var(
            $_POST['tempo'] ?? '',
            FILTER_VALIDATE_INT
        );
        $idTrem = filter_var($_POST['id_trem'] ?? '', FILTER_VALIDATE_INT);

        if (
            $nome === ''
            || mb_strlen($nome) < 3
            || mb_strlen($nome) > 100
            || $descricao === ''
            || mb_strlen($descricao) > 255
            || $extensao === false
            || $extensao <= 0
            || $tempo === false
            || $tempo <= 0
            || $idTrem === false
            || $idTrem < 1
        ) {
            $mensagem = 'Preencha os campos com valores válidos.';
        } else {
            $checkTrem = $conn->prepare('SELECT id_trem FROM trens WHERE id_trem = ?');
            $checkTrem->bind_param('i', $idTrem);
            $checkTrem->execute();
            $tremExiste = $checkTrem->get_result()->num_rows === 1;
            $checkTrem->close();

            if (!$tremExiste) {
                $mensagem = 'Selecione um trem cadastrado.';
            } else {
                $extensaoNumero = (float) $extensao;
                $tempoNumero = (int) $tempo;
                $stmt = $conn->prepare(
                    'INSERT INTO rotas (nome, descricao, extensao, tempo, id_trem)
                     VALUES (?, ?, ?, ?, ?)'
                );

                if (!$stmt) {
                    error_log('Erro ao preparar cadastro de rota: ' . $conn->error);
                    $mensagem = 'Não foi possível concluir o cadastro.';
                } else {
                    $stmt->bind_param('ssdii', $nome, $descricao, $extensaoNumero, $tempoNumero, $idTrem);
                    try {
                        if ($stmt->execute()) {
                            $_SESSION['mensagem_cadastro'] = 'Rota cadastrada com sucesso!';
                            header('Location: cadastrar_rotas.php');
                            exit;
                        }
                        error_log('Erro ao cadastrar rota: ' . $stmt->error);
                        $mensagem = 'Não foi possível concluir o cadastro.';
                    } catch (mysqli_sql_exception $e) {
                        error_log('Erro ao cadastrar rota: ' . $e->getMessage());
                        $mensagem = 'Não foi possível concluir o cadastro.';
                    } finally {
                        $stmt->close();
                    }
                }
            }
        }
    }
}

$manterFormularioAberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
$resultadoTrens = $conn->query('SELECT id_trem, nome FROM trens ORDER BY nome');
if (!$resultadoTrens) {
    error_log('Erro ao carregar trens: ' . $conn->error);
    $mensagemAcesso = 'Não foi possível carregar os trens cadastrados.';
}

$busca = trim($_GET['buscar'] ?? '');
$like = '%' . $busca . '%';

$stmt = $conn->prepare(
    'SELECT r.id_rotas, r.nome, r.descricao, r.extensao, r.tempo,
            r.data_criacao, r.id_trem, t.nome AS nome_trem
     FROM rotas AS r
     LEFT JOIN trens AS t ON t.id_trem = r.id_trem
     WHERE r.nome LIKE ? OR r.descricao LIKE ? OR t.nome LIKE ?
     ORDER BY r.nome'
);

if ($stmt) {
    $stmt->bind_param('sss', $like, $like, $like);
    try {
        $stmt->execute();
        $resultado = $stmt->get_result();
    } catch (mysqli_sql_exception $e) {
        error_log('Erro ao consultar rotas: ' . $e->getMessage());
        $resultado = false;
        $mensagemAcesso = 'Não foi possível carregar as rotas.';
    }
    $stmt->close();
} else {
    error_log('Erro ao consultar rotas: ' . $conn->error);
    $resultado = false;
    $mensagemAcesso = 'Não foi possível carregar as rotas.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Rotas | TremTech</title>

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

<a href="../../public/Tela_Sensores/cadastrar_sensores.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
  Sensores
</a>

<a href="../../public/Tela_Trens/cadastrar_trens.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/train.png');"></span>
  Trem
</a>

<a class="ativo" href="../../public/Tela_Rotas/cadastrar_rotas.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/map.png');"></span>
  Rota
</a>
<a href="../../public/Tela_Monitoramento/monitoramento.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/visible.png');"></span>
  Monitoramento
</a>

<a href="../../public/Tela_Relatorios/relatorios.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/report-card.png');"></span>
  Relatórios
</a>


<a class="logout" href="../../logout.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');"></span>
  Logout
</a>
    </aside>

    <main class="content-user">
        <p class="painel-user">PAINEL ADMINISTRATIVO</p>
        <h1>Controle de rotas cadastradas</h1>
        <h2>Rotas do sistema</h2>

        <?php if ($mensagemSucesso !== ''): ?>
            <p class="mensagem-sucesso" role="status">
                <?= escapar($mensagemSucesso) ?>
            </p>
        <?php endif; ?>

        <?php if ($mensagemAcesso !== ''): ?>
            <p role="alert"><?= escapar($mensagemAcesso) ?></p>
        <?php endif; ?>

        <?php if ($ehAdministrador): ?>
            <button
                class="btn-cadastrar-user"
                id="btnAbrirCadastro"
                type="button"
                aria-expanded="<?= $manterFormularioAberto ? 'true' : 'false' ?>"
                aria-controls="cadastro"
            >
                CADASTRAR NOVA ROTA
            </button>
          
             <section class="cadastro-usuario" id="cadastro" <?= $manterFormularioAberto ? '' : 'hidden' ?>>
                <form class="form-cadastro-user" method="POST">
              <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
              
            <div class="campo-user">
                <label for="nome">Nome da rota</label>
              <input type="text" id="nome" name="nome" minlength="3" maxlength="100" placeholder="Nome da rota" value="<?= escapar($nome) ?>" required>
            </div>

            <div class="campo-user">
              <label for="descricao">Descrição</label>
              <textarea id="descricao" name="descricao" maxlength="255" rows="3" required><?= escapar($descricao) ?></textarea>
            </div>

            <div class="campo-user">
              <label for="extensao">Extensão(km)</label>
              <input type="number" id="extensao" name="extensao" min="0.01" step="0.01" placeholder="Ex:45" value="<?= escapar((string) $extensao) ?>" required>
            </div>

            <div class="campo-user">
              <label for="minutos">Tempo estimado(min)</label>
              <input type="number" id="minutos" name="tempo" min="1" step="1" placeholder="Ex:30" value="<?= escapar((string) $tempo) ?>" required>
            </div>
            <div class="campo-user">
                <label for="id_trem">Trem vinculado</label>
                <select id="id_trem" name="id_trem" required>
                    <option value="">Selecione um trem</option>
                    <?php if ($resultadoTrens): ?>
                        <?php while ($trem = $resultadoTrens->fetch_assoc()): ?>
                            <option value="<?= (int) $trem['id_trem'] ?>" <?= (string) $idTrem === (string) $trem['id_trem'] ? 'selected' : '' ?>><?= escapar($trem['nome']) ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
<?php if ($mensagem !== ''): ?>
                        <p class="mensagem-cadastro" role="alert">
                            <?= escapar($mensagem) ?>
                        </p>
                    <?php endif; ?>

                    <button class="btn-cadastrar-form" type="submit">
                        CADASTRAR ROTA
                    </button>
                </form>
            </section>
        <?php endif; ?>

        <section class="buscar-box-user">
            <form method="GET">
                <label for="buscar">Buscar rota</label>
                <input
                    type="search"
                    id="buscar"
                    name="buscar"
                    placeholder="Nome da rota"
                    value="<?= escapar($busca) ?>"
                >
                <button type="submit">Buscar</button>
            </form>
        </section>

        <?php mostrar_rotas($resultado); ?>
    </main>
</div>

<script>
(() => {
    const botao = document.getElementById('btnAbrirCadastro');
    const formulario = document.getElementById('cadastro');

    if (!botao || !formulario) return;

    botao.addEventListener('click', () => {
        formulario.hidden = !formulario.hidden;
        formulario.classList.toggle('ativo', !formulario.hidden);
        botao.setAttribute('aria-expanded', String(!formulario.hidden));
    });
})();
</script>
</body>
</html>
      
