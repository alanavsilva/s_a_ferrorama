<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Confere a permissão no PHP, mesmo se alguém tentar chamar este arquivo direto.
exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastrar_rotas.php');
    exit();
}

// O formulário envia o ID do registro escolhido na tabela.
$id = (int) $_POST['id'];

// Se algum sensor usa a rota, volta para a lista sem excluir.
$sql = "SELECT id_sensor FROM sensores WHERE id_rotas = $id";
$sensores = mysqli_query($conn, $sql);

if (mysqli_num_rows($sensores) > 0) {
    $_SESSION['mensagem_rota'] = 'Esta rota tem sensores vinculados e não pode ser excluída.';
    header('Location: cadastrar_rotas.php');
    exit();
}

// A chave estrangeira também bloqueia a exclusão no banco.
// O catch mostra uma mensagem se o banco recusar o DELETE.
try {
    $sql = "DELETE FROM rotas WHERE id_rotas = $id";
    $excluiu = mysqli_query($conn, $sql);

    if ($excluiu && mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem_rota'] = 'Rota excluída com sucesso!';
    } elseif ($excluiu) {
        $_SESSION['mensagem_rota'] = 'Rota não encontrada.';
    } else {
        $_SESSION['mensagem_rota'] = 'Não foi possível excluir a rota.';
    }
} catch (mysqli_sql_exception $erro) {
    $_SESSION['mensagem_rota'] = 'Não foi possível excluir a rota.';
}

header('Location: cadastrar_rotas.php');
exit();