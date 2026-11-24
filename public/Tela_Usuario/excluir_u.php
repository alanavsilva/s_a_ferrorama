<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Confere a permissão no PHP, mesmo se alguém tentar chamar este arquivo direto.
exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: cadastrar_usuario.php');
    exit();
}

// O formulário envia o ID do registro escolhido na tabela.
$id = (int) $_POST['id'];

$sql = "SELECT id_usuario FROM usuarios WHERE id_usuario = $id";
$resultado = mysqli_query($conn, $sql);
$usuario = mysqli_fetch_assoc($resultado);

// LIMIT 1 busca só um vínculo, que já é suficiente para bloquear.
$trens = mysqli_query($conn, "SELECT id_trem FROM trens WHERE id_usuario = $id LIMIT 1");
$falhas = mysqli_query($conn, "SELECT id_falha FROM falhas_operacionais WHERE id_usuario = $id LIMIT 1");

if (!$usuario) {
    $mensagem = 'Usuário não encontrado.';
} elseif (mysqli_num_rows($trens) > 0 || mysqli_num_rows($falhas) > 0) {
    $mensagem = 'Este usuário tem trens ou falhas vinculadas.';
} else {
    $mensagem = 'Não foi possível excluir o usuário.';

    try {
        $sql = "DELETE FROM usuarios WHERE id_usuario = $id";

        if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
            if ($id === (int) $_SESSION['id_usuario']) {
                header('Location: ../../logout.php');
                exit();
            }

            $mensagem = 'Usuário excluído com sucesso!';
        }
    } catch (mysqli_sql_exception $erro) {
        $mensagem = 'Não foi possível excluir o usuário.';
    }
}

$_SESSION['mensagem_usuario'] = $mensagem;
header('Location: cadastrar_usuario.php');
exit();