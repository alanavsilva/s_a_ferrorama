<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "../Tela_Sensores/visualizacao_sensores.php";

exigir_login();

$nome = $_SESSION['nome'] ?? 'Usuário';
$ehAdministrador = strcasecmp((string) ($_SESSION['tipo'] ?? ''), 'Administrador') === 0;
$statusFiltro = $_GET['status'] ?? 'todas';

if (!in_array($statusFiltro, ['todas', 'resolvidas', 'nao_resolvidas'], true)) {
    $statusFiltro = 'todas';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_falha'])) {
    exigir_administrador();
    verificar_csrf();
    $idFalha = filter_input(INPUT_POST, 'id_falha', FILTER_VALIDATE_INT);
    $acaoFalha = $_POST['acao_falha'];

if ($idFalha && in_array($acaoFalha, ['resolver', 'reabrir'], true)) {
        $resolvida = $acaoFalha === 'resolver' ? 1 : 0;
        $stmtAtualizarFalha = $conn->prepare("UPDATE falhas_operacionais SET resolvida = ? WHERE id_falha = ?");

        if ($stmtAtualizarFalha) {
            $stmtAtualizarFalha->bind_param('ii', $resolvida, $idFalha);
            $stmtAtualizarFalha->execute();
            $stmtAtualizarFalha->close();
        }
    }

    header('Location: home.php?status=' . urlencode($statusFiltro) . '#falhas');
    exit;
}

$totalUsuarios = $conn->query( "SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalSensores = $conn->query( "SELECT COUNT(*) AS total FROM sensores")->fetch_assoc()['total'];
$totalTrens = $conn->query( "SELECT COUNT(*) AS total FROM trens")->fetch_assoc()['total'];
$totalRotas = $conn->query( "SELECT COUNT(*) AS total FROM rotas")->fetch_assoc()['total'];

$resultadoTrensVelocidade = $conn->query( "SELECT nome, velocidade_media FROM trens ORDER BY id_trem");

$trensVelocidade = [];

while ($trem = $resultadoTrensVelocidade->fetch_assoc()) {
    $trensVelocidade[] = $trem;
}

$totalResolvidas = (int) ($conn->query("SELECT COUNT(*) AS total FROM falhas_operacionais WHERE resolvida = 1")->fetch_assoc()['total'] ?? 0);
$totalNaoResolvidas = (int) ($conn->query("SELECT COUNT(*) AS total FROM falhas_operacionais WHERE resolvida = 0")->fetch_assoc()['total'] ?? 0);
$sqlFalhas = "SELECT f.id_falha, f.descricao, f.data_registro, f.resolvida, s.nome_sensor, t.nome AS nome_trem FROM falhas_operacionais AS f LEFT JOIN sensores AS s ON f.id_sensor = s.id_sensor LEFT JOIN trens AS t ON s.id_trem = t.id_trem";

if ($statusFiltro === 'resolvidas') {
    $sqlFalhas .= " WHERE f.resolvida = 1";
} elseif ($statusFiltro === 'nao_resolvidas') {
    $sqlFalhas .= " WHERE f.resolvida = 0";
}

$sqlFalhas .= " ORDER BY f.data_registro DESC";
$faltas = $conn->query($sqlFalhas);


$usuarios = $conn->query("SELECT id_usuario, nome, email, tipo FROM usuarios ORDER BY nome");
$sensores = $conn->query("SELECT s.id_sensor, s.nome_sensor, s.tipo_dado, t.nome AS trem_vinculado, r.nome AS rota_vinculada FROM sensores AS s INNER JOIN trens AS t ON s.id_trem = t.id_trem INNER JOIN rotas AS r ON s.id_rotas = r.id_rotas ORDER BY s.id_sensor DESC");
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

    <?php if ($ehAdministrador): ?>
    <a class="home_link" href="../../public/Tela_Usuario/cadastrar_usuario.php">
        Tela de Usuarios
    </a>
    <?php endif; ?>

    <a class="home_link" href="../../public/Tela_Sensores/cadastrar_sensores.php">
        Sensores
    </a>

    <a class="home_link" href="../../public/Tela_Rotas/cadastrar_rotas.php">
        Rotas
    </a>

    <a class="home_link" href="../../public/Tela_Trens/cadastrar_trens.php">
        Trens
    </a>

    <a class="home_link" href="../../public/Tela_Monitoramento/monitoramento.php" >
        Monitoramento
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
    <div class="home_status_titulo" id="velocidade-titulo"> Velocidade média</div>

    <div class="home_status_valor" id="velocidade-valor"> 0 km/h</div>
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

    <?php if ($ehAdministrador): ?>
    <section class="home_botoes">
        <a class="home_botao" href="../../public/Tela_Usuario/cadastrar_usuario.php"> Tela de Usuarios </a>
        <a class="home_botao" href="../../public/Tela_Sensores/cadastrar_sensores.php"> Tela de Sensores </a>
        <a class="home_botao" href="../../public/Tela_Trens/cadastrar_trens.php"> Tela de Trens </a>
        <a class="home_botao" href="../../public/Tela_Rotas/cadastrar_rotas.php"> Tela de Rotas </a>
    </section>
    <?php endif; ?>

     <section class="home_caixa_falhas" id="falhas">
        <div class="falhas_cabecalho">
            <h2 class="home_titulo_tabela">Falhas operacionais</h2>

    <form method="GET" class="falhas_filtro">
        <label for="status">Status</label>
            <select id="status" name="status" onchange="this.form.submit()">
                <option value="todas" <?= $statusFiltro === 'todas' ? 'selected' : '' ?> > Todas </option>
                <option value="resolvidas" <?= $statusFiltro === 'resolvidas' ? 'selected' : '' ?> > Resolvidas </option>
                <option value="nao_resolvidas" <?= $statusFiltro === 'nao_resolvidas' ? 'selected' : '' ?> > Não resolvidas </option>
                </select>
            </form>
        </div>

        <div class="falhas_resumo">
            <div class="falha_contador">
                <strong><?= $totalResolvidas ?></strong>
                <span>Resolvidas</span>
            </div>

            <div class="falha_contador">
                <strong><?= $totalNaoResolvidas ?></strong>
                <span>Não resolvidas</span>
            </div>
        </div>

        <div class="falhas_lista">
            <?php if ($faltas && $faltas->num_rows > 0): ?>
            <?php while ($falha = $faltas->fetch_assoc()): ?>
                <article class="falha_item">
                    <div class="falha_dados">
                    <h3><?= escapar($falha['descricao']) ?></h3>

        <p> Sensor: <?= escapar($falha['nome_sensor'] ?? 'Removido') ?>
        — Trem: <?= escapar($falha['nome_trem'] ?? 'Não identificado') ?>
        — <?= escapar(date('d/m/Y H:i', strtotime($falha['data_registro']))) ?>
       </p>

        </div>

    <?php if ($ehAdministrador): ?>
    <form method="POST" class="falha_acao">
        <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>" >
        <input type="hidden" name="id_falha" value="<?= (int) $falha['id_falha'] ?>" >

            <?php if ($falha['resolvida']): ?>
             
        <button type="submit" name="acao_falha" value="reabrir" class="botao_reabrir" > Reabrir </button>
                            
            <?php else: ?>

        <button type="submit" name="acao_falha" value="resolver" class="botao_resolver" > Marcar como resolvida </button>
                            
            <?php endif; ?>
                        
     </form>
     <?php endif; ?>
        </article>

                <?php endwhile; ?>
            <?php else: ?>
                <p class="falhas_vazias">Nenhuma falha encontrada.</p>
            <?php endif; ?>
        </div>
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
<div class="home_tabela_sensores">
    <?php mostrar_sensores($sensores, false); ?>
</div>
</section>

    <section class="home_botoes home_logout" style="grid-column: 1 / -1; display: flex; justify-content: center; width: 100%;">
        <a class="home_botao" href="../../logout.php">Logout</a>
    </section>

</main>

<script>
const trens = <?= json_encode($trensVelocidade) ?>;
let indice = 0;

function mostrarTrem() {
    if (trens.length === 0) {
        document.getElementById('velocidade-valor').textContent = 'Nenhum trem cadastrado';
        return;
    }

    const trem = trens[indice];

    document.getElementById('velocidade-titulo').textContent = 'Velocidade média - ' + trem.nome;

    document.getElementById('velocidade-valor').textContent = trem.velocidade_media + ' km/h';

    indice = (indice + 1) % trens.length;
}

mostrarTrem();
setInterval(mostrarTrem, 3000);
</script>

</body>

