
<?php
$filtro = $_GET["status"] ?? "Todas";
$falhas = [
    [
        "titulo" => "Velocidade acima do limite",
        "sensor" => "Sensor 001 - Linha Norte",
        "data" => "07/05/2026",
        "status" => "Não resolvida"
    ],
    [
        "titulo" => "Temperatura alta",
        "sensor" => "Sensor 256 - Linha Sul",
        "data" => "05/05/2026",
        "status" => "Resolvida"
    ],
    [
        "titulo" => "Leitura estável",
        "sensor" => "Sensor 111 - Linha Oeste",
        "data" => "22/05/2026",
        "status" => "Não resolvida"
      
$resolvidas = 0;
$naoResolvidas = 0;

foreach ($falhas as $falha) {
    if ($falha["status"] == "Resolvida") {
        $resolvidas++;
    } else {
        $naoResolvidas++;
    }
}
  
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Monitoramento</title>
</head>
<body>
    <main>
        <h1>Falhas operacionais</h1>
        
<form method="GET">
    <label for="status">Status:</label>

    <select name="status" id="status" onchange="this.form.submit()">
        <option value="Todas">Todas</option>
        <option value="Resolvida">Resolvidas</option>
        <option value="Não resolvida">Não resolvidas</option>
        
<section>
    <h2><?= $resolvidas ?></h2>
    <p>Falhas resolvidas</p>

    <h2><?= $naoResolvidas ?></h2>
    <p>Falhas não resolvidas</p>
</section>

    </select>
</form>

    </main>
</body>
</html>
