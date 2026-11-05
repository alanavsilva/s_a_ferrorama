<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastrar_trens.php');
    exit;
}

verificar_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID do trem inválido.');
}

$stmt = $conn->prepare('DELETE FROM trens WHERE id_trem = ?');
if (!$stmt) {
    error_log('Erro ao preparar exclusão de trem: ' . $conn->error);
    http_response_code(500);
    exit('Não foi possível excluir o trem.');
}
$stmt->bind_param('i', $id);
try {
    $stmt->execute();
    if ($stmt->affected_rows !== 1) {
        $stmt->close();
        http_response_code(404);
        exit('Trem não encontrado.');
    }
    $stmt->close();
    header('Location: cadastrar_trens.php?excluido=1');
    exit;
} catch (mysqli_sql_exception $e) {
    error_log('Erro ao excluir trem: ' . $e->getMessage());
    $stmt->close();
    http_response_code(409);
    exit('Não foi possível excluir: existem registros relacionados a este trem.');
}
?>
