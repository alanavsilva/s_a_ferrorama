<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: cadastrar_rotas.php');
    exit();
}

// O formulário envia o ID. Uma ligação já basta para bloquear a exclusão.
$id = (int) $_POST['id'];
$sensores = mysqli_query($conn, "SELECT id_sensor FROM sensores WHERE id_rotas = $id LIMIT 1");

if (mysqli_num_rows($sensores) > 0) {
    $mensagem = 'Esta rota tem sensores vinculados.';
} else {
    // O ? recebe o ID que veio do formulário. O tipo i indica um número inteiro.
    $sql = "DELETE FROM rotas WHERE id_rotas = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    try {
        $excluiu = mysqli_stmt_execute($stmt);
    } catch (mysqli_sql_exception $erro) {
        $excluiu = false;
    }

    $linhas = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if (!$excluiu) {
        $mensagem = 'Não foi possível excluir a rota.';
    } elseif ($linhas === 0) {
        $mensagem = 'Rota não encontrada.';
    } else {
        $mensagem = 'Rota excluída com sucesso!';
    }
}

$_SESSION['mensagem_rota'] = $mensagem;
header('Location: cadastrar_rotas.php');
exit();
