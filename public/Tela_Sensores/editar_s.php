<?php
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();

if (!isset($_GET['id'])) {
    exit('Sensor inválido.');
}

// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) $_GET['id'];
$sql = "SELECT * FROM sensores WHERE id_sensor = $id";
$resultado = mysqli_query($conn, $sql);
$sensor = mysqli_fetch_assoc($resultado);

if (!$sensor) {
    exit('Sensor não encontrado.');
}

$tipos = ['Velocidade', 'Temperatura', 'Status'];
$mensagem = '';

// Ao clicar em Salvar, os dados voltam para este arquivo, que atualiza o sensor escolhido.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sensor['nome_sensor'] = trim($_POST['nome_sensor'] ?? '');
    $sensor['tipo_dado'] = $_POST['tipo_dado'] ?? '';
    $sensor['id_rotas'] = (int) ($_POST['id_rotas'] ?? 0);

    $id_rota = $sensor['id_rotas'];

    $sql = "SELECT id_rotas FROM rotas WHERE id_rotas = $id_rota";
    $rota_encontrada = mysqli_query($conn, $sql);

    if (mb_strlen($sensor['nome_sensor']) < 3 || mb_strlen($sensor['nome_sensor']) > 100 || !in_array($sensor['tipo_dado'], $tipos)) {
        $mensagem = 'Informe um nome válido e selecione o tipo do sensor.';
    } elseif (mysqli_num_rows($rota_encontrada) === 0) {
        $mensagem = 'Selecione uma rota cadastrada.';
    } else {
        $sql = "UPDATE sensores SET nome_sensor = ?, tipo_dado = ?, id_rotas = ?
                WHERE id_sensor = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssii', $sensor['nome_sensor'], $sensor['tipo_dado'], $id_rota, $id);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_sensor'] = 'Sensor atualizado com sucesso!';    
            header('Location: cadastrar_sensores.php');    
            exit();
        }

        $mensagem = "Não foi possível salvar as alterações.";
    }
}

$rotas = mysqli_query($conn, "SELECT id_rotas, nome FROM rotas ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar sensor | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=nomes-underscore-1">
</head>
<body class="body_geral">
    <div class="pagina">
        <!-- Menu desta página. A classe ativo destaca a seção aberta. -->
        <aside class="menu">
            <img class="logo_trem" src="https://img.icons8.com/ios/100/701015/train.png" alt="Logo TremTech">
            <p class="marca">SISTEMA TREMTECH</p>

            <a href="../Tela_Home/home.php"><img src="https://img.icons8.com/ios-filled/18/701015/home.png" alt=""> Home</a>
            <a href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a class="ativo" href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
            <a class="logout" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
        </aside>
        <main class="content_user">
        <h1>Editar sensor</h1>

        <?php if ($mensagem !== '') { ?>
            <p><?php echo escapar($mensagem); ?></p>
        <?php } ?>

        <section class="cadastro_usuario ativo">
            <form method="POST" class="form_cadastro_user">
                <label for="nome_sensor">Nome do sensor</label>
                <input type="text" id="nome_sensor" name="nome_sensor" minlength="3" maxlength="100"
                       value="<?php echo escapar($sensor['nome_sensor']); ?>" required>

                <label for="tipo_dado">Tipo de dado</label>
                <select id="tipo_dado" name="tipo_dado" required>
                    <?php foreach ($tipos as $tipo) { ?>
                        <option value="<?php echo $tipo; ?>"
                            <?php if ($tipo === $sensor['tipo_dado']) { echo 'selected'; } ?>>
                            <?php echo $tipo; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="id_rotas">Rota vinculada</label>
                <select id="id_rotas" name="id_rotas" required>
                    <?php while ($rota = mysqli_fetch_assoc($rotas)) { ?>
                        <option value="<?php echo $rota['id_rotas']; ?>"
                            <?php if ($rota['id_rotas'] == $sensor['id_rotas']) { echo 'selected'; } ?>>
                            <?php echo escapar($rota['nome']); ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit" class="btn_cadastrar_form">Salvar alterações</button>
                <a href="cadastrar_sensores.php">Cancelar</a>
            </form>
        </section>
        </main>
    </div>
    <!-- A janela fica fechada até a pessoa clicar em Sair. -->
    <dialog class="modal_confirmacao" id="janela_saida" aria-labelledby="tituloConfirmarSaida">
        <div class="modal_confirmacao_icone" aria-hidden="true">?</div>
        <h2 id="tituloConfirmarSaida">Sair do sistema?</h2>
        <p class="modal_confirmacao_mensagem">Tem certeza de que deseja sair da sua conta?</p>
        <div class="modal_confirmacao_acoes">
            <button type="button" class="modal_btn_cancelar" id="cancelar_saida">Continuar no sistema</button>
            <button type="button" class="modal_btn_sair" id="confirmar_saida">Sair</button>
        </div>
    </dialog>

    <script src="../../script/validacao.js?v=simples-7"></script>
</body>
</html>