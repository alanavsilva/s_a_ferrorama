<?php
// O PHP usa o ID do endereço para preencher o formulário e salvar a edição.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();

// O endereço antigo leva à edição dentro da página de cadastro.
if (empty($editando)) {
    $id = (int) ($_GET['id'] ?? 0);
    header('Location: cadastrar_sensores.php?editar=' . $id . '#cadastro');
    exit();
}

// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) $_GET['editar'];
$sql = "SELECT * FROM sensores WHERE id_sensor = $id";
$resultado = mysqli_query($conn, $sql);
$sensor = mysqli_fetch_assoc($resultado);

if (!$sensor) {
    exit('Sensor não encontrado.');
}

$tipos = ['Velocidade', 'Temperatura', 'Status'];
$mensagem = '';

// Este bloco atualiza o registro no banco quando a pessoa clica em Salvar.
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
    
            $_SESSION['sucesso_sensor'] = true;
            header('Location: cadastrar_sensores.php');
    
            exit();
        }

        $mensagem = "Não foi possível salvar as alterações.";
    }
}

$rotas = mysqli_query($conn, "SELECT id_rotas, nome FROM rotas ORDER BY nome");
