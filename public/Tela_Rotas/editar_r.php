<?php
require_once '../../infra/auth.php';
exigir_administrador();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Rota inválida.');
}

header('Location: cadastrar_rotas.php?editar=' . $id);
exit;
