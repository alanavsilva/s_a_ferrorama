<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Confere a permissão no PHP, mesmo se alguém tentar chamar este arquivo direto.
exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: cadastrar_trens.php');
    exit();
}

// O formulário envia o ID do registro escolhido na tabela.
$id = (int) $_POST['id'];

// Se o trem tem uma rota, a exclusão é bloqueada.
// Isso também protege os sensores que estão ligados a essa rota.
$sql = "SELECT id_rotas FROM rotas WHERE id_trem = $id LIMIT 1";
$rotas = mysqli_query($conn, $sql);

if (mysqli_num_rows($rotas) > 0) {
    $_SESSION['mensagem_trem'] = 'Este trem tem rotas vinculadas e não pode ser excluído.';
} else {
    // O catch mostra uma mensagem se o banco recusar o DELETE.
    try {
        $excluiu = mysqli_query($conn, "DELETE FROM trens WHERE id_trem = $id");

        if ($excluiu && mysqli_affected_rows($conn) > 0) {
            $_SESSION['mensagem_trem'] = 'Trem excluído com sucesso!';
        } elseif ($excluiu) {
            $_SESSION['mensagem_trem'] = 'Trem não encontrado.';
        } else {
            $_SESSION['mensagem_trem'] = 'Não foi possível excluir o trem.';
        }
    } catch (mysqli_sql_exception $erro) {
        $_SESSION['mensagem_trem'] = 'Não foi possível excluir o trem.';
    }
}

header('Location: cadastrar_trens.php');
exit();