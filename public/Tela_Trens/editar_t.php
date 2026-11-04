
<?php
include "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();

$tipos = ['Velocidade', 'Temperatura', 'Status'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Trem inválido.');
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verificar_csrf();

    $nome = trim($_POST["nome"]?? '');
    $velocidade = filter_var($_POST["velocidade_media"] ?? null, FILTER_VALIDATE_FLOAT);
    $tipo = $_POST["tipo"]?? '';
    $usuario = filter_var($_POST['id_usuario'] ?? null, FILTER_VALIDATE_INT);

     if ( mb_strlen($nome) < 3 || mb_strlen($nome) > 100 || $velocidade === false || $velocidade < 1 || $velocidade > 600 || !in_array($tipo, $tipos, true) || !$usuario ) {
        $erro = 'Confira os dados do trem e o usuário responsável.';
    } else {
        $check = $conn->prepare( 'SELECT COUNT(*) AS usuario_ok FROM usuarios WHERE id_usuario = ?' );

        $check->bind_param('i', $usuario);
        $check->execute();

        $validos = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$validos || !$validos['usuario_ok']) {
            $erro = 'Selecione um usuário cadastrado.';
        } else {
            $stmt = $conn->prepare('UPDATE trens SET nome = ?, velocidade_media = ?, tipo = ?, id_usuario = ?  WHERE id_trem = ?');

            $stmt->bind_param('sdsii', $nome, $velocidade, $tipo, $usuario, $id);

    try {
        if ($stmt->execute()) {
            header( 'Location: cadastrar_trens.php?editado=1');
            exit;
                }

        $erro = 'Não foi possível salvar as alterações.';
            } catch (mysqli_sql_exception $e) {
            error_log( 'Erro ao editar trem: ' . $e->getMessage());
            $erro = 'Não foi possível salvar as alterações.';
            }

            $stmt->close();
        }
    }
}

$stmt = $conn->prepare( 'SELECT id_trem, nome, velocidade_media, tipo, id_usuario FROM trens WHERE id_trem = ?');

$stmt->bind_param('i', $id);
$stmt->execute();

$trem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$trem) {
    http_response_code(404);
    exit('Trem não encontrado.');
}

$usuarios = $conn->query( 'SELECT id_usuario, nome FROM usuarios ORDER BY nome');
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar trem</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>
<body class="body_geral">
    <main class="conteudo">
    <h1>Editar trem</h1>

        <?php if ($erro !== ''): ?>
            <p role="alert"><?= escapar($erro) ?></p>
        <?php endif; ?>

    <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">

        <div>
            <label for="nome">Nome do trem</label>
            <input type="text" id="nome" name="nome" maxlength="100" required
                    value="<?= escapar($trem['nome']) ?>">
        </div>


        <div>
            <label for="velocidade_media">Velocidade média(km/h)</label>
            <input type="number" id="velocidade_media" name="velocidade_media" name="velocidade_maxima" min="1" max="600" step="any" required
                   value="<?= escapar( (string)$trem['velocidade_media']) ?>">
        </div>


        <div>
            <label for="tipo">Tipo/categoria</label>
            <select id="tipo" name="tipo" required>
                <?php foreach ($tipos as $opcao): ?>
                <option value="<?= escapar($opcao) ?>" <?= $trem['tipo'] === $opcao ? 'selected': '' ?> >
                <?= escapar($opcao) ?>
                </option>
                <?php endforeach; ?>
            </select>   
        </div>


         <div>
            <label for="id_usuario">Usuário responsável</label>
            <select id="id_usuario" name="id_usuario" required>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                <option value="<?= (int)$usuario['id_usuario'] ?>" <?= (int)$trem['id_usuario'] === (int)$usuario['id_usuario'] ? 'selected' : '' ?> >
                <?= escapar($usuario['nome']) ?>
                </option>
                <?php endwhile; ?>
                </select>
            </div>


        <button type="submit">Salvar alterações</button>
        <a href="visualizacao_trens.php">Cancelar</a>
    </form>
</main>


</body>
</html>

