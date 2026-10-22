
<?php
include "../../infra/conexao.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Trem não encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $velocidade = trim($_POST["velocidade_maxima"]);
    $tipo = trim($_POST["tipo"]);
    $rota = trim($_POST["rota"]);

    $sql = "UPDATE trens
            SET nome = ?, velocidade_maxima = ?, tipo = ?, rota = ?
            WHERE id_trem = ?";
$stmt = mysqli_prepare($conn, $sql);


    mysqli_stmt_bind_param(
        $stmt,
        "ssssi",
        $nome,
        $velocidade,
        $tipo,
        $rota,
        $id
    );


    if (mysqli_stmt_execute($stmt)) {
        header("Location: visualizacao_trens.php");
        exit;
    } else {
        echo "Erro ao editar o trem.";
    }
}


$sql = "SELECT * FROM trens WHERE id_trem = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);


$resultado = mysqli_stmt_get_result($stmt);
$trem = mysqli_fetch_assoc($resultado);


if (!$trem) {
    die("Trem não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar trem</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>
<body class="body_geral">
    <main class="conteudo">
    <h1>Editar trem</h1>


    <form method="POST">
        <div>
            <label for="nome">Nome do trem</label>
            <input type="text" id="nome" name="nome"
                   value="<?= htmlspecialchars($trem['nome']) ?>" required>
        </div>


        <div>
            <label for="velocidade_maxima">Velocidade máxima</label>
            <input type="number" id="velocidade_maxima"
                   name="velocidade_maxima"
                   value="<?= htmlspecialchars($trem['velocidade_maxima']) ?>"
                   required>
        </div>


        <div>
            <label for="tipo">Tipo/categoria</label>
            <input type="text" id="tipo" name="tipo"
                   value="<?= htmlspecialchars($trem['tipo']) ?>" 

