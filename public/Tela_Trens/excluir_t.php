
<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastrar_trens.php');
    exit;
}

verificar_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION['aviso_exclusao'] = 'ID do trem inválido.';
    header('Location: cadastrar_trens.php');
    exit;
}

$stmt = $conn->prepare(
    "SELECT id_trem FROM trens WHERE id_trem = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();

$trem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$trem) {
    $_SESSION['aviso_exclusao'] = 'Trem não encontrado.';
    header('Location: cadastrar_trens.php');
    exit;
}

$stmt = $conn->prepare(
    "SELECT id_sensor FROM sensores WHERE id_trem = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();

$temSensorVinculado = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($temSensorVinculado) {
    $_SESSION['aviso_exclusao'] =
        'Não é possível excluir este trem porque existem sensores vinculados a ele.';

    header('Location: cadastrar_trens.php');
    exit;
}

try {
    $stmt = $conn->prepare(
        "DELETE FROM trens WHERE id_trem = ?"
    );

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['mensagem_cadastro'] = 'Trem excluído com sucesso!';

} catch (mysqli_sql_exception $e) {
    error_log('Erro ao excluir trem: ' . $e->getMessage());

    $_SESSION['aviso_exclusao'] =
        'Não foi possível excluir este trem porque existem outros registros vinculados a ele.';
}

header('Location: cadastrar_trens.php');
exit;
