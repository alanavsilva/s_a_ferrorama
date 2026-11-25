<?php
// EXCLUIR USUÁRIO. Recebe o ID enviado pelo botão Excluir da tabela.
// Antes de apagar, procura trens ou falhas ligados à conta. Depois volta para a lista e mostra o resultado.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: cadastrar_usuario.php');
    exit();
}

// O formulário envia o ID. Uma ligação já basta para bloquear a exclusão.
$id = (int) $_POST['id'];
$trens = mysqli_query($conn, "SELECT id_trem FROM trens WHERE id_usuario = $id LIMIT 1");
$falhas_operacionais = mysqli_query($conn, "SELECT id_falha FROM falhas_operacionais WHERE id_usuario = $id LIMIT 1");

if (mysqli_num_rows($trens) > 0 || mysqli_num_rows($falhas_operacionais) > 0) {
    $mensagem = 'Este usuário tem trens ou falhas vinculadas.';
} else {
    // O ? recebe o ID que veio do formulário. O tipo i indica um número inteiro.
    $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
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
        $mensagem = 'Não foi possível excluir o usuário.';
    } elseif ($linhas === 0) {
        $mensagem = 'Usuário não encontrado.';
    } else {
        if ($id === (int) $_SESSION['id_usuario']) {
            header('Location: ../../logout.php');
            exit();
        }

        $_SESSION['sucesso_usuario'] = true;
        $mensagem = 'Usuário excluído com sucesso!';
    }
}

$_SESSION['mensagem_usuario'] = $mensagem;
header('Location: cadastrar_usuario.php');
exit();
