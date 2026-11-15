
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

try {
    if ($stmt->execute()) {
        $_SESSION["mensagem_cadastro"] = "Sensor excluído com sucesso!";
    } else {
        error_log("Erro ao excluir sensor: " . $stmt->error);
        $_SESSION["aviso_exclusao"] = "Não foi possível excluir este sensor.";
    }
} catch (mysqli_sql_exception $e) {
    error_log("Erro ao excluir sensor: " . $e->getMessage());
    if ((int) $e->getCode() === 1451) {
        $_SESSION["aviso_exclusao"] = "Não é possível excluir este sensor porque há falhas operacionais vinculadas a ele.";
    } else {
        $_SESSION["aviso_exclusao"] = "Não foi possível excluir este sensor. Tente novamente.";
    }
}

header("Location: cadastrar_sensores.php");
exit;

?>
