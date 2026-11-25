<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: cadastrar_trens.php');
    exit();
}

// O formulário envia o ID. Uma ligação já basta para bloquear a exclusão.
$id = (int) $_POST['id'];
$rotas = mysqli_query($conn, "SELECT id_rotas FROM rotas WHERE id_trem = $id LIMIT 1");

if (mysqli_num_rows($rotas) > 0) {
    $mensagem = 'Este trem tem rotas vinculadas.';
} else {
    // O ? recebe o ID que veio do formulário. O tipo i indica um número inteiro.
    $sql = "DELETE FROM trens WHERE id_trem = ?";
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
        $mensagem = 'Não foi possível excluir o trem.';
    } elseif ($linhas === 0) {
        $mensagem = 'Trem não encontrado.';
    } else {
        $mensagem = 'Trem excluído com sucesso!';
    }
}

$_SESSION['mensagem_trem'] = $mensagem;
header('Location: cadastrar_trens.php');
exit();
