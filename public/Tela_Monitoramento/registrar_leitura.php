<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

// O registro de leituras e falhas é exclusivo de administradores.
exigir_administrador();

$erro = '';
$valor = '';
$descricao = '';
$idSensor = 0;
$data = date('Y-m-d');

// Recebe o sensor e a data. O tipo do sensor define se salva um número ou uma descrição de falha.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idSensor = (int) $_POST['id_sensor'];
    $valor = trim($_POST['valor'] ?? '');
    $descricao = trim($_POST['descricao_falha'] ?? '');
    $data = $_POST['data_leitura'];

    $sql = "SELECT tipo_dado FROM sensores WHERE id_sensor = $idSensor";
    $resultado = mysqli_query($conn, $sql);
    $sensorEscolhido = mysqli_fetch_assoc($resultado);

    // strtotime converte a data. A comparação rejeita datas como 31 de fevereiro.
    $dataConvertida = strtotime($data);

    if (!$sensorEscolhido) {
        $erro = 'Selecione um sensor cadastrado.';
    } elseif ($dataConvertida === false || date('Y-m-d', $dataConvertida) !== $data) {
        $erro = 'Informe uma data válida.';
    } else {
        $dataSql = $data . ' 00:00:00';
        $tipo = $sensorEscolhido['tipo_dado'];

        // Sensor de Status gera uma falha. Velocidade e Temperatura geram leituras numéricas.
        if ($tipo === 'Status') {
            if (mb_strlen($descricao) < 5 || mb_strlen($descricao) > 200) {
                $erro = 'A descrição deve ter de 5 a 200 caracteres.';
            } else {
                $sql = "INSERT INTO falhas_operacionais
                        (id_sensor, descricao, data_registro, situacao)
                        VALUES (?, ?, ?, 'Não resolvida')";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'iss', $idSensor, $descricao, $dataSql);

                $salvou = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                if ($salvou) {
                    $_SESSION['mensagem_leitura'] = 'Falha registrada com sucesso!';
                    header('Location: monitoramento.php#falhas');
                    exit();
                }

                $erro = 'Não foi possível registrar a falha.';
            }
        } else {
            if ($tipo === 'Velocidade') {
                $limite = 600;
                $filtro = 'velocidade';
                $unidade = 'km/h';
            } else {
                $limite = 150;
                $filtro = 'temperatura';
                $unidade = '°C';
            }

            if (!is_numeric($valor) || $valor < 0 || $valor > $limite) {
                $erro = 'Informe um valor entre 0 e ' . $limite . ' ' . $unidade . '.';
            } else {
                $valorNumero = (float) $valor;
                $sql = "INSERT INTO leituras_sensores (id_sensor, valor, data_leitura)
                        VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'ids', $idSensor, $valorNumero, $dataSql);

                $salvou = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                if ($salvou) {
                    $_SESSION['mensagem_leitura'] = 'Leitura cadastrada com sucesso!';
                    header('Location: monitoramento.php?' . $filtro . '=' . $idSensor);
                    exit();
                }

                $erro = 'Não foi possível salvar a leitura.';
            }
        }
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
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-topo-2">
</head>

<body class="body_geral">
    <?php include __DIR__ . '/../menu.php'; ?>
    <div class="pagina">

        <main class="content-user">
            <p class="painel-user">PAINEL DE MONITORAMENTO</p>
            <h1>Registrar leitura ou falha</h1>
            <h2>Informações dos sensores</h2>

            <div class="admin-badge-user">
                <div class="avatar-user"></div>
                <div>
                    <strong>Administrador</strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($erro !== '') { ?>
                <p class="leitura-mensagem-erro" role="alert"><?php echo escapar($erro); ?></p>
            <?php } ?>

            <section class="cadastro-usuario ativo">
                <h2>Nova leitura ou falha</h2>

                <?php if (mysqli_num_rows($sensores) > 0) { ?>
                    <form class="form-cadastro-user" method="POST">
                        <div class="campo-user">
                            <label for="id_sensor">Sensor vinculado</label>
                            <select id="id_sensor" name="id_sensor" required>
                                <option value="">Selecione um sensor</option>

                                <?php while ($sensor = mysqli_fetch_assoc($sensores)) { ?>
                                    <!-- data-tipo informa ao JavaScript qual campo deve aparecer. -->
                                    <option value="<?php echo $sensor['id_sensor']; ?>"
                                            data-tipo="<?php echo escapar($sensor['tipo_dado']); ?>"
                                        <?php if ($idSensor == $sensor['id_sensor']) { echo 'selected'; } ?>>
                                        <?php echo escapar($sensor['nome_sensor']); ?>
                                        (<?php echo escapar($sensor['tipo_dado']); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="campo-user" id="campo-valor">
                            <label for="valor">Valor <span id="unidade"></span></label>
                            <input type="number" id="valor" name="valor" min="0" step="0.01"
                                   value="<?php echo escapar($valor); ?>" required>
                        </div>

                        <div class="campo-user campo-descricao-status" id="campo-descricao-status" hidden>
                            <label for="descricao_falha">Descrição da falha</label>
                            <input type="text" id="descricao_falha" name="descricao_falha"
                                   minlength="5" maxlength="200" value="<?php echo escapar($descricao); ?>">
                        </div>

                        <div class="campo-user campo-responsavel-trem">
                            <label for="data_leitura">Data da leitura ou ocorrência</label>
                            <input type="date" id="data_leitura" name="data_leitura"
                                   value="<?php echo escapar($data); ?>" required>
                        </div>

                        <button class="btn-cadastrar-form" id="botao-salvar-informacao" type="submit">
                            SALVAR LEITURA
                        </button>
                    </form>
                <?php } else { ?>
                    <p>Cadastre um sensor para registrar uma leitura ou falha.</p>
                <?php } ?>
            </section>
        </main>
    </div>

<script src="../../script/validacao.js"></script>
</body>
</html>