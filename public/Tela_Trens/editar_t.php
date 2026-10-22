
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

   