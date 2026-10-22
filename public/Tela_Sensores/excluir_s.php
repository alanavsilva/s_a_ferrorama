<?php

include "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();
verificar_csrf();

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("usuário inválido.");
}

$stmt = $conn->prepare("SELECT tipo FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    exit("usuário não encontrado.");
}

if ($usuario["tipo"] === "administrador") {
    exit("este usuário não pode ser excluído.");
}

$sql = "DELETE FROM usuarios WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: cadastrar_usuario.php");
    exit;
} else {
    echo "erro ao excluir usuário.";
}

?>