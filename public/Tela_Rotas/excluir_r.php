<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

verificar_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Rota inválida.');
}

$stmt = $conn->prepare('DELETE FROM rotas WHERE id_rotas = ?');
if (!$stmt) {
    error_log('Erro ao preparar exclusão de rota: ' . $conn->error);
    $_SESSION['mensagem_erro'] = 'Não foi possível excluir a rota.';
    header('Location: cadastrar_rotas.php');
    exit;
}
$stmt->bind_param('i', $id);

try {
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['mensagem_cadastro'] = 'Rota excluída com sucesso!';
    } else {
        $_SESSION['mensagem_erro'] = 'A rota não foi encontrada.';
    }
} catch (mysqli_sql_exception $e) {
    if ((int) $e->getCode() === 1451) {
        $_SESSION['mensagem_erro'] =
            'Não foi possível excluir esta rota porque existem registros relacionados.';
    } else {
        error_log('Erro ao excluir rota: ' . $e->getMessage());
        $_SESSION['mensagem_erro'] = 'Não foi possível excluir a rota.';
    }
}

$stmt->close();

header('Location: cadastrar_rotas.php');
exit;
