<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: cadastrar_sensores.php');
    exit();
}

// O formulário envia o ID. Uma ligação já basta para bloquear a exclusão.
$id = (int) $_POST['id'];
$leituras_sensores = mysqli_query($conn, "SELECT id_leitura FROM leituras_sensores WHERE id_sensor = $id LIMIT 1");
$falhas_operacionais = mysqli_query($conn, "SELECT id_falha FROM falhas_operacionais WHERE id_sensor = $id LIMIT 1");

if (mysqli_num_rows($leituras_sensores) > 0 || mysqli_num_rows($falhas_operacionais) > 0) {
    $mensagem = 'Este sensor tem leituras ou falhas vinculadas.';
} else {
    // O ? recebe o ID que veio do formulário. O tipo i indica um número inteiro.
    $sql = "DELETE FROM sensores WHERE id_sensor = ?";
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
        $mensagem = 'Não foi possível excluir o sensor.';
    } elseif ($linhas === 0) {
        $mensagem = 'Sensor não encontrado.';
    } else {
        $mensagem = 'Sensor excluído com sucesso!';
    }
}

$_SESSION['mensagem_sensor'] = $mensagem;
header('Location: cadastrar_sensores.php');
exit();
