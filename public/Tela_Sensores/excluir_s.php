<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Confere a permissão no PHP, mesmo se alguém tentar chamar este arquivo direto.
exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: cadastrar_sensores.php');
    exit();
}

// O formulário envia o ID do registro escolhido na tabela.
$id = (int) $_POST['id'];

// Basta uma leitura ou uma falha vinculada para bloquear a exclusão.
$sql = "SELECT id_leitura FROM leituras_sensores WHERE id_sensor = $id LIMIT 1";
$leituras = mysqli_query($conn, $sql);

$sql = "SELECT id_falha FROM falhas_operacionais WHERE id_sensor = $id LIMIT 1";
$falhas = mysqli_query($conn, $sql);

if (mysqli_num_rows($leituras) > 0 || mysqli_num_rows($falhas) > 0) {
    $_SESSION['mensagem_sensor'] = 'Este sensor tem leituras ou falhas vinculadas e não pode ser excluído.';
} else {
    // O catch mostra uma mensagem caso o banco bloqueie a exclusão.
    try {
        $excluiu = mysqli_query($conn, "DELETE FROM sensores WHERE id_sensor = $id");

        if ($excluiu && mysqli_affected_rows($conn) > 0) {
            $_SESSION['mensagem_sensor'] = 'Sensor excluído com sucesso!';
        } elseif ($excluiu) {
            $_SESSION['mensagem_sensor'] = 'Sensor não encontrado.';
        } else {
            $_SESSION['mensagem_sensor'] = 'Não foi possível excluir o sensor.';
        }
    } catch (mysqli_sql_exception $erro) {
        $_SESSION['mensagem_sensor'] = 'Não foi possível excluir o sensor.';
    }
}

header('Location: cadastrar_sensores.php');
exit();