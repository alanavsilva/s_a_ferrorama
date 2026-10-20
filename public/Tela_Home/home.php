<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "../Tela_Sensores/visualizacao_sensores.php";

exigir_login();

$nome = $_SESSION['nome'] ?? 'Usuário';
$totalUsuarios = $conn->query( "SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalSensores = $conn->query( "SELECT COUNT(*) AS total FROM sensores")->fetch_assoc()['total'];
$totalTrens = $conn->query( "SELECT COUNT(*) AS total FROM trens")->fetch_assoc()['total'];
$totalRotas = $conn->query( "SELECT COUNT(*) AS total FROM rotas")->fetch_assoc()['total'];
$velocidadeMedia = $conn->query( "SELECT AVG(velocidade) AS media FROM sensores")->fetch_assoc()['media'] ?? 0;
$totalFalhas = $conn->query( "SELECT COUNT(*) AS total FROM falhas_operacionais WHERE resolvida = 0")->fetch_assoc()['total'];
$faltas = $conn->query("SELECT falhas_operacionais.id_falha, falhas_operacionais.descricao, falhas_operacionais.data_registro, falhas_operacionais.resolvida, sensores.nome AS nome_sensor
FROM falhas_operacionais LEFT JOIN sensores ON falhas_operacionais.id_sensor = sensores.id_sensor ORDER BY falhas_operacionais.data_registro DESC");
$usuarios = $conn->query( "SELECT id_usuario, nome, email, tipo FROM usuarios ORDER BY nome");
$sensores = $conn->query("SELECT id_sensor, nome, trem_vinculado, direcao, temperatura, velocidade FROM sensores ORDER BY id_sensor DESC" );

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

<nav class="home_barra">
<a class="home_logo" href="home.php"> <img class="home-imagem-trem" src="https://img.icons8.com/ios/50/ffffff/train.png" alt="Ícone de trem"> SISTEMA TREMTECH </a>
    <a class="home_link" href="home.php">Home</a>

    <a class="home_link" href="../../public/Tela_Usuario/cadastrar_usuario.php">
        Usuários
    </a>

    <a class="home_link" href="../../public/Tela_Sensores/cadastrar_sensores.php">
        Sensores
    </a>

    <a class="home_link" href="../../public/Tela_Relatorios/relatorios.php">
        Relatórios
    </a>

    <div class="home_usuario">
        <div class="home_bolinha"></div>

        <div class="home_nome_usuario"> <strong><?= escapar($_SESSION['tipo'] ?? 'Usuário') ?></strong><br> <?= escapar($nome) ?> </div>
    </div>
</nav>

<main class="home_conteudo">

    <section class="home_boas_vindas">
        <div>
            <p>Bem-vindo de volta!</p>
            <h1>Monitore usuários, rotas e operações em um só lugar.</h1>
        </div>

        <div class="home_status">
            <div class="home_status_titulo">Velocidade média</div>
            <div class="home_status_valor">
                <?= number_format((float)$velocidadeMedia, 0, ',', '.') ?> km/h
            </div>
        </div>
    </section>

    <section class="home_indicadores">

        <div class="home_indicador">
            <div class="home_indicador_nome">Usuários cadastrados</div>
            <div class="home_indicador_numero"> <?= (int)$totalUsuarios ?> </div>
        </div>

        <div class="home_indicador">
            <div class="home_indicador_nome">Rotas cadastradas</div>
            <div class="home_indicador_numero"> <?= (int)$totalRotas ?> </div>
        </div>

        <div class="home_indicador">
            <div class="home_indicador_nome">Sensores cadastrados</div>
            <div class="home_indicador_numero"><?= (int)$totalSensores ?></div>
        </div> 

        <div class="home_indicador">
            <div class="home_indicador_nome">Trens cadastrados</div>
            <div class="home_indicador_numero"> <?= (int)$totalTrens ?> </div>
        </div>

    </section>

    <section class="home_botoes">
        <a class="home_botao" href="../../public/Tela_Sensores/cadastrar_sensores.php"> Cadastro de Sensores e Trens </a>

        <a class="home_botao" href="../../public/Tela_Usuario/cadastrar_usuario.php"> Cadastro de Usuários </a>
    </section>

 <section class="home_caixa_sensores" id="falhas">
    <h2 class="home_titulo_tabela">Falhas operacionais</h2>

    <p>
        Falhas não resolvidas:
        <strong><?= (int) $totalFalhas ?></strong>
    </p>

    <table class="home_tabela">
        <thead>
            <tr>
                <th>ID Sensor</th>
                <th>Nome Sensor</th>
                <th>Tipo de dado coletado</th>
                <th>Trem vinculado</th>
                <th>Rota vinculada</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($faltas && $faltas->num_rows > 0): ?>
                <?php while ($falha = $faltas->fetch_assoc()): ?>
                    <tr>
                        <td><?= escapar($falha['nome_sensor'] ?? 'Sensor removido') ?></td>
                        <td><?= escapar($falha['descricao']) ?></td>
                        <td><?= escapar($falha['data_registro']) ?></td>
                        <td>
                            <?= $falha['resolvida'] ? 'Resolvida' : 'Não resolvida' ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhuma falha cadastrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section class="home_caixa_sensores">
    <h2 class="home_titulo_tabela">Usuários cadastrados</h2>

    <table class="home_tabela">
        <thead>
            <tr>
                <th>ID Usuário</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Tipo</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int) $usuario['id_usuario'] ?></td>
                        <td><?= escapar($usuario['nome']) ?></td>
                        <td><?= escapar($usuario['email']) ?></td>
                        <td><?= escapar($usuario['tipo']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhum usuário cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section class="home_caixa_sensores">
    <h2 class="home_titulo_tabela">Sensores cadastrados</h2>

    <?php mostrar_sensores($sensores, false); ?>
</section>

</main>

</body>