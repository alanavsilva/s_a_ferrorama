<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_POST, 'id_sensor', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Sensor inválido.');
}

$stmt = $conn->prepare('SELECT id_sensor, nome_sensor, tipo_dado, id_trem, id_rotas FROM sensores WHERE id_sensor = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$sensor = $stmt->get_result()->fetch_assoc();
if (!$sensor) {
    http_response_code(404);
    exit('Sensor não encontrado.');
}

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $nome = trim($_POST['nome_sensor'] ?? '');
    $tipo = $_POST['tipo_dado'] ?? '';
    $idTrem = filter_input(INPUT_POST, 'id_trem', FILTER_VALIDATE_INT);
    $idRota = filter_input(INPUT_POST, 'id_rotas', FILTER_VALIDATE_INT);
    if ($nome === '' || !in_array($tipo, ['Velocidade', 'Temperatura', 'Status'], true) || !$idTrem || !$idRota) {
        $mensagem = 'Confira os campos e selecione um trem e uma rota.';
    } else {
        $stmt = $conn->prepare('UPDATE sensores SET nome_sensor = ?, tipo_dado = ?, id_trem = ?, id_rotas = ? WHERE id_sensor = ?');
        $stmt->bind_param('ssiii', $nome, $tipo, $idTrem, $idRota, $id);
        if ($stmt->execute()) {
            $_SESSION['mensagem_cadastro'] = 'Sensor atualizado com sucesso!';
            header('Location: cadastrar_sensores.php');
            exit;
        }
        error_log('Erro ao atualizar sensor: ' . $stmt->error);
        $mensagem = 'Não foi possível atualizar o sensor. Confira os dados e tente novamente.';
    }
    $sensor['nome_sensor'] = $nome;
    $sensor['tipo_dado'] = $tipo;
    $sensor['id_trem'] = $idTrem ?: $sensor['id_trem'];
    $sensor['id_rotas'] = $idRota ?: $sensor['id_rotas'];
}

$trens = $conn->query('SELECT id_trem, nome FROM trens ORDER BY nome');
$rotas = $conn->query('SELECT id_rotas, nome FROM rotas ORDER BY nome');
$nomeUsuario = escapar((string)($_SESSION['nome'] ?? 'Administrador'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar sensor | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261021-edicao">
</head>
<body class="body_geral pagina-edicao">
<div class="pagina">
    <aside class="menu">
        <img class="logo-trem" src="https://img.icons8.com/ios/100/ffffff/train.png" alt="Logo TremTech">
        <p class="marca">SISTEMA TREMTECH</p>
        <a href="../../public/Tela_Home/home.php">⌂ &nbsp; Home</a>
        <a href="../../public/Tela_Usuario/cadastrar_usuario.php">♟ &nbsp; Usuários</a>
        <a class="ativo" href="cadastrar_sensores.php">◉ &nbsp; Sensores</a>
        <a href="../../public/Tela_Trens/cadastrar_trens.php">▰ &nbsp; Trem</a>
        <a href="../../public/Tela_Rotas/cadastrar_rotas.php">▥ &nbsp; Rota</a>
        <a href="../../public/Tela_Monitoramento/monitoramento.php">◉ &nbsp; Monitoramento</a>
        <a href="../../public/Tela_Relatorios/relatorios.php">▣ &nbsp; Relatórios</a>
        <a class="logout" href="../../logout.php">⇥ Logout</a>
    </aside>
    <main class="content-user">
        <p class="painel-user">PAINEL ADMINISTRATIVO</p>
        <h1>Controle de sensores cadastrados</h1>
        <h2>Atualize os dados do sensor</h2>
        <div class="admin-badge-user"><div class="avatar-user"></div><div><strong>Administrador</strong><span><?= $nomeUsuario ?></span></div></div>

        <a class="link-voltar-edicao" href="cadastrar_sensores.php">← Voltar para sensores</a>
        <section class="cadastro-usuario ativo formulario-edicao">
            <h2>Editar sensor</h2>
            <?php if ($mensagem !== ''): ?><p class="mensagem-cadastro" role="alert"><?= escapar($mensagem) ?></p><?php endif; ?>
            <form class="form-cadastro-user" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
                <input type="hidden" name="id_sensor" value="<?= (int)$id ?>">
                <div class="campo-user"><label for="nome_sensor">Nome do sensor</label><input id="nome_sensor" name="nome_sensor" maxlength="100" required value="<?= escapar($sensor['nome_sensor']) ?>"></div>
                <div class="campo-user"><label for="tipo_dado">Tipo de dado coletado</label><select id="tipo_dado" name="tipo_dado" required>
                    <?php foreach (['Velocidade', 'Temperatura', 'Status'] as $tipo): ?><option value="<?= escapar($tipo) ?>" <?= $sensor['tipo_dado'] === $tipo ? 'selected' : '' ?>><?= escapar($tipo) ?></option><?php endforeach; ?>
                </select></div>
                <div class="campo-user"><label for="id_trem">Trem vinculado</label><select id="id_trem" name="id_trem" required><option value="">Selecione um trem</option>
                    <?php if ($trens): while ($trem = $trens->fetch_assoc()): ?><option value="<?= (int)$trem['id_trem'] ?>" <?= (int)$sensor['id_trem'] === (int)$trem['id_trem'] ? 'selected' : '' ?>><?= escapar($trem['nome']) ?></option><?php endwhile; endif; ?>
                </select></div>
                <div class="campo-user"><label for="id_rotas">Rota vinculada</label><select id="id_rotas" name="id_rotas" required><option value="">Selecione uma rota</option>
                    <?php if ($rotas): while ($rota = $rotas->fetch_assoc()): ?><option value="<?= (int)$rota['id_rotas'] ?>" <?= (int)$sensor['id_rotas'] === (int)$rota['id_rotas'] ? 'selected' : '' ?>><?= escapar($rota['nome']) ?></option><?php endwhile; endif; ?>
                </select></div>
                <div class="acoes-form-edicao"><button type="submit" class="btn-cadastrar-form">SALVAR ALTERAÇÕES</button><a href="cadastrar_sensores.php">Cancelar</a></div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
