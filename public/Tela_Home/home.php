<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_login();

$nome = $_SESSION['nome'] ?? 'Usuário';
$totalUsuarios = $conn->query( "SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalSensores = $conn->query( "SELECT COUNT(*) AS total FROM sensores")->fetch_assoc()['total'];
$totalTrens = $conn->query( "SELECT COUNT(*) AS total FROM trens")->fetch_assoc()['total'];
$velocidadeMedia = $conn->query( "SELECT AVG(velocidade) AS media FROM sensores")->fetch_assoc()['media'] ?? 0;

$sql = "SELECT id_sensor, nome, trem_vinculado, direcao, temperatura, velocidade FROM sensores ORDER BY id_sensor DESC";

$sensores = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - TremTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/style.css">
  </head>

<body>

<nav class="barra">
    <div class="menu">
        <a class="marca" href="home.php">🚆 SISTEMA TREMTECH</a>

        <a class="link-menu" href="home.php">Home</a>

        <a class="link-menu" href="../../public/Tela_Usuario/cadastrar_usuario.php">
            Usuários
        </a>

        <a class="link-menu" href="../../public/Tela_Sensores/cadastrar_sensores.php">
            Sensores
        </a>

        <a class="link-menu" href="../../public/Tela_Relatorios/relatorios.php">
            Relatórios
        </a>

        <div class="usuario">
            <strong><?= escapar($_SESSION['tipo'] ?? 'Usuário') ?></strong><br>
            <small><?= escapar($nome) ?></small>
        </div>
    </div>
</nav>

<main class="conteudo">

    <section class="boas-vindas">
        <p>Bem-vindo de volta!</p>
        <h1>Monitore usuários, trens e sensores em um só lugar.</h1>
    </section>

    <section class="indicadores">

        <div class="indicador">
            <div class="nome_indicador">Usuários cadastrados</div>
            <div class="numero"><?= (int) $totalUsuarios ?></div>
        </div>

        <div class="indicador">
            <div class="nome_indicador">Trens cadastrados</div>
            <div class="numero"><?= (int) $totalTrens ?></div>
        </div>

        <div class="indicador">
            <div class="nome_indicador">Sensores cadastrados</div>
            <div class="numero"><?= (int) $totalSensores ?></div>
        </div>

        <div class="indicador">
            <div class="nome_indicador">Velocidade média</div>
            <div class="numero">
                <?= number_format((float) $velocidadeMedia, 0, ',', '.') ?>
            </div>
            <small>km/h</small>
        </div>

    </section>

      <section class="botoes">
        <a class="botao_cadastro" href="../../public/Tela_Sensores/cadastrar_sensores.php"> Cadastro de Sensores e Trens </a>
        <a class="botao_cadastro" href="../../public/Tela_Usuario/cadastrar_usuario.php" > Cadastro de Usuário </a>
      </section>

    <section class="caixa_sensores">  
    <h2 class="titulo">Sensores cadastrados</h2>

        <table class="tabela_sensores">
        <thead>
                <tr>
                  <th>ID do sensor</th>
                  <th>Nome do sensor</th>
                  <th>Trem</th>
                  <th>Direção</th>
                  <th>Temperatura</th>
                  <th>Velocidade</th>
                </tr>
        </thead>
     <tbody>
      <?php if ($sensores && $sensores->num_rows > 0): ?>
      <?php while ($sensor = $sensores->fetch_assoc()): ?>
                <tr>
                  <td><?= (int) $sensor['id_sensor'] ?></td>
                  <td class="sensor-destaque"> <?= escapar($sensor['nome']) ?> </td>
                  <td><?= escapar($sensor['trem_vinculado']) ?></td>
                  <td><?= escapar($sensor['direcao']) ?></td>
                  <td> <?= escapar((string) $sensor['temperatura']) ?> °C </td>
                  <td> <?= escapar((string) $sensor['velocidade']) ?> km/h </td>
                </tr>
      <?php endwhile; ?>
      <?php else: ?>

                 <tr>
                  <td class="sem-sensores" colspan="6"> Nenhum sensor cadastrado </td> 
                </tr>

      <?php endif; ?>
            
    </tbody>
    </table>
    </section>

</main>

</body>
</html>