
<?php
include "../../infra/conexao.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: visualizacao_trens.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID do trem inválido.");
}

$sql = "DELETE FROM trens WHERE id_trem = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: visualizacao_trens.php");
    exit;
} else {
    echo "Não foi possível excluir o trem.";
}
?>
