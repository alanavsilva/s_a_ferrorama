
<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();
verificar_csrf();

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION["aviso_exclusao"] = "Usuário inválido.";
    header("Location: cadastrar_usuario.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT tipo FROM usuarios WHERE id_usuario = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    $_SESSION["aviso_exclusao"] = "Usuário não encontrado.";
    header("Location: cadastrar_usuario.php");
    exit;
}

if (strcasecmp($usuario["tipo"], "Administrador") === 0) {
    $_SESSION["aviso_exclusao"] = "Não é permitido excluir um administrador.";
    header("Location: cadastrar_usuario.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id_trem FROM trens WHERE id_usuario = ? LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$temTremVinculado = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($temTremVinculado) {
    $_SESSION["aviso_exclusao"] =
        "Não é possível excluir este usuário porque ele está vinculado a um ou mais trens.";

    header("Location: cadastrar_usuario.php");
    exit;
}

try {
    $stmt = $conn->prepare(
        "DELETE FROM usuarios WHERE id_usuario = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION["mensagem_cadastro"] = "Usuário excluído com sucesso!";

} catch (mysqli_sql_exception $e) {
    error_log("Erro ao excluir usuário: " . $e->getMessage());

    $_SESSION["aviso_exclusao"] =
        "Não foi possível excluir este usuário porque existem outros registros vinculados a ele.";
}

header("Location: cadastrar_usuario.php");
exit;
