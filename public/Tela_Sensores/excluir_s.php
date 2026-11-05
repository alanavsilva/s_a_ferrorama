
<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();
verificar_csrf();

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION["aviso_exclusao"] = "Sensor inválido.";
    header("Location: cadastrar_sensores.php");
    exit;
}

$stmt = $conn->prepare("SELECT id_sensor FROM sensores WHERE id_sensor = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION["aviso_exclusao"] = "Sensor não encontrado.";
    header("Location: cadastrar_sensores.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM sensores WHERE id_sensor = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION["mensagem_cadastro"] = "Sensor excluído com sucesso!";
} else {
    error_log("Erro ao excluir sensor: " . $stmt->error);
    $_SESSION["aviso_exclusao"] = "Não foi possível excluir este sensor.";
}

header("Location: cadastrar_sensores.php");
exit;

?>
