<?php
require_once '../../infra/auth.php';
exigir_administrador();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Usuário inválido. Volte à listagem e selecione um usuário.');
}
header('Location: cadastrar_usuario.php?editar=' . $id);
exit;
