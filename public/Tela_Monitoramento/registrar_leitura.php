<?php
// O formulário grava uma falha para sensores de Status ou uma leitura numérica para os demais.
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

// O registro de leituras e falhas é exclusivo de administradores.
exigir_administrador();

$erro = '';
$valor = '';
$descricao = '';
$id_sensor = 0;
$data = date('Y-m-d');

// Recebe o formulário e valida o sensor e a data antes de salvar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sensor = (int) ($_POST['id_sensor'] ?? 0);
    $valor = trim($_POST['valor'] ?? '');
    $descricao = trim($_POST['descricao_falha'] ?? '');
    $data = $_POST['data_leitura'] ?? '';

    $sql = "SELECT tipo_dado FROM sensores WHERE id_sensor = $id_sensor";
    $resultado = mysqli_query($conn, $sql);
    $sensor_escolhido = mysqli_fetch_assoc($resultado);

    // Converte e confere a data para rejeitar datas como 31 de fevereiro.
    $data_convertida = strtotime($data);
    $stmt = null;

    if (!$sensor_escolhido) {
        $erro = 'Selecione um sensor cadastrado.';
    } elseif ($data_convertida === false || date('Y-m-d', $data_convertida) !== $data) {
        $erro = 'Informe uma data válida.';
    } else {
        $data_sql = $data . ' 00:00:00';
        $tipo = $sensor_escolhido['tipo_dado'];

        // Status salva uma descrição na tabela de falhas.
        if ($tipo === 'Status') {
            if (mb_strlen($descricao) < 5 || mb_strlen($descricao) > 200) {
                $erro = 'A descrição deve ter de 5 a 200 caracteres.';
            } else {
                $sql = "INSERT INTO falhas_operacionais
                        (id_sensor, descricao, data_registro, situacao)
                        VALUES (?, ?, ?, 'Não resolvida')";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'iss', $id_sensor, $descricao, $data_sql);
                $mensagem = 'Falha registrada com sucesso!';
                $destino = 'monitoramento.php#falhas';
            }
        } else {
            // Velocidade e Temperatura têm unidades e limites diferentes.
            if ($tipo === 'Velocidade') {
                $limite = 600;
                $unidade = 'km/h';
                $filtro = 'velocidade';
            } else {
                $limite = 150;
                $unidade = '°C';
                $filtro = 'temperatura';
            }

            if (!is_numeric($valor) || $valor < 0 || $valor > $limite) {
                $erro = 'Informe um valor entre 0 e ' . $limite . ' ' . $unidade . '.';
            } else {
                $valor_numero = (float) $valor;
                $sql = "INSERT INTO leituras_sensores (id_sensor, valor, data_leitura) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'ids', $id_sensor, $valor_numero, $data_sql);
                $mensagem = 'Leitura cadastrada com sucesso!';
                $destino = 'monitoramento.php?' . $filtro . '=' . $id_sensor;
            }
        }
    }

    // Se uma consulta foi preparada, salva e volta para o Monitoramento.
    if ($stmt) {
        $salvou = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_leitura'] = $mensagem;
            header('Location: ' . $destino);
            exit();
        }

        $erro = 'Não foi possível salvar. Confira os dados e tente novamente.';
    }
}

$sql = "SELECT id_sensor, nome_sensor, tipo_dado FROM sensores ORDER BY nome_sensor";
$sensores = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de leituras | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-direto-topo-1">
</head>

<body class="body_geral">
    <!-- A classe ativo destaca o link da tela aberta. -->
    <nav class="menu_topo" aria-label="Navegação do TremTech">
        <a class="menu_topo_marca" href="../Tela_Home/home.php">
            <img src="https://img.icons8.com/ios/100/701015/train.png" alt="">
            <span>SISTEMA TREMTECH</span>
        </a>

        <div class="menu_topo_links">
            <a href="../Tela_Home/home.php"><img src="https://img.icons8.com/ios-filled/18/701015/home.png" alt=""> Home</a>
            <a href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a class="ativo" href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
        </div>

        <a class="menu_topo_sair" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
    </nav>
    <div class="pagina">


        <main class="content_user">
            <p class="painel_user">PAINEL DE MONITORAMENTO</p>
            <h1>Registrar leitura ou falha</h1>
            <h2>Informações dos sensores</h2>

            <div class="admin_badge_user">
                <div class="avatar_user"></div>
                <div>
                    <strong>Administrador</strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($erro !== '') { ?>
                <p class="leitura_mensagem_erro" role="alert"><?php echo escapar($erro); ?></p>
            <?php } ?>

            <section class="cadastro_usuario ativo">
                <h2>Nova leitura ou falha</h2>

                <?php if (mysqli_num_rows($sensores) > 0) { ?>
                    <form class="form_cadastro_user" method="POST">
                        <div class="campo_user">
                            <label for="id_sensor">Sensor vinculado</label>
                            <select id="id_sensor" name="id_sensor" required>
                                <option value="">Selecione um sensor</option>

                                <?php while ($sensor = mysqli_fetch_assoc($sensores)) { ?>
                                    <!-- Cada opção guarda o tipo do sensor. O JavaScript lê esse tipo para mostrar o campo Valor ou Descrição. -->
                                    <option value="<?php echo $sensor['id_sensor']; ?>"
                                            data-tipo="<?php echo escapar($sensor['tipo_dado']); ?>"
                                        <?php if ($id_sensor == $sensor['id_sensor']) { echo 'selected'; } ?>>
                                        <?php echo escapar($sensor['nome_sensor']); ?>
                                        (<?php echo escapar($sensor['tipo_dado']); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="campo_user" id="campo_valor">
                            <label for="valor">Valor <span id="unidade"></span></label>
                            <input type="number" id="valor" name="valor" min="0" step="0.01"
                                   value="<?php echo escapar($valor); ?>" required>
                        </div>

                        <div class="campo_user campo_descricao_status" id="campo_descricao_status" hidden>
                            <label for="descricao_falha">Descrição da falha</label>
                            <input type="text" id="descricao_falha" name="descricao_falha"
                                   minlength="5" maxlength="200" value="<?php echo escapar($descricao); ?>">
                        </div>

                        <div class="campo_user campo_responsavel_trem">
                            <label for="data_leitura">Data da leitura ou ocorrência</label>
                            <input type="date" id="data_leitura" name="data_leitura"
                                   value="<?php echo escapar($data); ?>" required>
                        </div>

                        <button class="btn_cadastrar_form" id="botao_salvar_informacao" type="submit">
                            SALVAR LEITURA
                        </button>
                    </form>
                <?php } else { ?>
                    <p>Cadastre um sensor para registrar uma leitura ou falha.</p>
                <?php } ?>
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