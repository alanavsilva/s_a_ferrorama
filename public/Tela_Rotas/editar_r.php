<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_POST, 'id_rotas', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Rota inválida.');
}

$stmt = $conn->prepare(
    'SELECT id_rotas, nome, descricao, extensao, tempo, data_criacao, id_trem
     FROM rotas
     WHERE id_rotas = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$rota = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rota) {
    http_response_code(404);
    exit('Rota não encontrada.');
}

$mensagem = '';
$nome = $rota['nome'];
$descricao = $rota['descricao'];
$extensao = (string) $rota['extensao'];
$tempo = (string) $rota['tempo'];
$idTrem = (int) $rota['id_trem'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                'UPDATE rotas
                 SET nome = ?, descricao = ?, extensao = ?, tempo = ?, id_trem = ?
                 WHERE id_rotas = ?'
            );
            $stmt->bind_param('ssdiii', $nome, $descricao, $extensaoNumero, $tempoNumero, $idTrem, $id);

            try {
                if ($stmt->execute()) {
                    $_SESSION['mensagem_cadastro'] = 'Rota atualizada com sucesso!';
                    $stmt->close();
                    header('Location: cadastrar_rotas.php');
                    exit;
                }
                error_log('Erro ao atualizar rota: ' . $stmt->error);
                $mensagem = 'Não foi possível atualizar a rota.';
            } catch (mysqli_sql_exception $e) {
                error_log('Erro ao atualizar rota: ' . $e->getMessage());
                $mensagem = 'Não foi possível atualizar a rota.';
            }
            $stmt->close();
        }
    }
}

$resultadoTrens = $conn->query('SELECT id_trem, nome FROM trens ORDER BY nome');
if (!$resultadoTrens) {
    error_log('Erro ao carregar trens para edição: ' . $conn->error);
    http_response_code(500);
    exit('Não foi possível carregar os trens.');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar rota | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body class="body_geral pagina-edicao">
<div class="pagina">
    <aside class="menu">
        <img
            class="logo-trem"
            src="https://img.icons8.com/ios/100/ffffff/train.png"
            alt="Logo TremTech"
        >
        <p class="marca">SISTEMA TREMTECH</p>
        <a href="../../public/Tela_Home/home.php">Home</a>
        <a href="../../public/Tela_Usuario/cadastrar_usuario.php">Usuários</a>
        <a href="../../public/Tela_Sensores/cadastrar_sensores.php">Sensores</a>
        <a href="../../public/Tela_Trens/cadastrar_trens.php">Trem</a>
        <a class="ativo" href="cadastrar_rotas.php">Rota</a>
        <a href="../../public/Tela_Monitoramento/monitoramento.php">Monitoramento</a>
<a href="../../public/Tela_Falhas/cadastrar_falha.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/error.png');"></span>
  Falhas
</a>
        <a href="../../public/Tela_Relatorios/relatorios.php">Relatórios</a>
        <a class="logout" href="../../logout.php">Logout</a>
    </aside>

    <main class="content-user">
        <p class="painel-user">PAINEL ADMINISTRATIVO</p>
        <h1>Controle de rotas cadastradas</h1>
        <h2>Atualize os dados da rota</h2>

        <a class="link-voltar-edicao" href="cadastrar_rotas.php">
            ← Voltar para rotas
        </a>

        <section class="cadastro-usuario ativo formulario-edicao">
            <h2>Editar rota</h2>

            <?php if ($mensagem !== ''): ?>
                <p class="mensagem-cadastro" role="alert">
                    <?= escapar($mensagem) ?>
                </p>
            <?php endif; ?>

            <form class="form-cadastro-user" method="POST">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= escapar(token_csrf()) ?>"
                >
                <input
                    type="hidden"
                    name="id_rotas"
                    value="<?= (int) $id ?>"
                >

                <div class="campo-user">
                    <label for="nome">Nome da rota</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        minlength="3"
                        maxlength="100"
                        required
                        value="<?= escapar($nome) ?>"
                    >
                </div>

                <div class="campo-user">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" maxlength="255" rows="3" required><?= escapar($descricao) ?></textarea>
                </div>

                <div class="campo-user">
                    <label for="extensao">Extensão (km)</label>
                    <input
                        type="number"
                        id="extensao"
                        name="extensao"
                        min="0.01"
                        step="0.01"
                        required
                        value="<?= escapar((string) $extensao) ?>"
                    >
                </div>

                <div class="campo-user">
                    <label for="tempo">Tempo estimado (min)</label>
                    <input
                        type="number"
                        id="tempo"
                        name="tempo"
                        min="1"
                        step="1"
                        required
                        value="<?= escapar((string) $tempo) ?>"
                    >
                </div>

                <div class="campo-user">
                    <label for="id_trem">Trem vinculado</label>
                    <select id="id_trem" name="id_trem" required>
                        <option value="">Selecione um trem</option>
                        <?php while ($trem = $resultadoTrens->fetch_assoc()): ?>
                            <option value="<?= (int) $trem['id_trem'] ?>" <?= $idTrem === (int) $trem['id_trem'] ? 'selected' : '' ?>><?= escapar($trem['nome']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <p>Data de criação: <?= escapar(date('d/m/Y H:i', strtotime($rota['data_criacao']))) ?></p>

                <div class="acoes-form-edicao">
                    <button type="submit" class="btn-cadastrar-form">
                        SALVAR ALTERAÇÕES
                    </button>
                    <a href="cadastrar_rotas.php">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
