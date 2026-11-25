<?php
// O PHP usa o ID do endereço para preencher o formulário e salvar a edição.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();
// O endereço antigo também leva ao formulário dentro da página de cadastro.
if (empty($editando)) {
    $id = (int) ($_GET['id'] ?? 0);
    header('Location: cadastrar_rotas.php?editar=' . $id . '#cadastro');
    exit();
}


// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) $_GET['editar'];
$sql = "SELECT * FROM rotas WHERE id_rotas = $id";
$resultado = mysqli_query($conn, $sql);
$rota = mysqli_fetch_assoc($resultado);

if (!$rota) {
    exit('Rota não encontrada.');
}

$mensagem = '';

// Este bloco atualiza o registro no banco quando a pessoa clica em Salvar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rota['nome'] = trim($_POST['nome'] ?? '');
    $rota['descricao'] = trim($_POST['descricao'] ?? '');
    $rota['extensao'] = (float) ($_POST['extensao'] ?? 0);
    $rota['tempo'] = (int) ($_POST['tempo'] ?? 0);
    $rota['id_trem'] = (int) ($_POST['id_trem'] ?? 0);

    $id_trem = $rota['id_trem'];
    $sql = "SELECT id_trem FROM trens WHERE id_trem = $id_trem";
    $trem_encontrado = mysqli_query($conn, $sql);

    if ($rota['nome'] === '' || $rota['descricao'] === ''
        || $rota['extensao'] <= 0 || $rota['tempo'] <= 0) {
        $mensagem = 'Preencha os campos com valores válidos.';
    } elseif (mysqli_num_rows($trem_encontrado) === 0) {
        $mensagem = 'Selecione um trem cadastrado.';
    } else {
        $sql = "UPDATE rotas SET nome = ?, descricao = ?, extensao = ?, tempo = ?, id_trem = ?
                WHERE id_rotas = ?";
        $stmt = mysqli_prepare($conn, $sql);

        // Os tipos seguem a ordem dos valores enviados ao banco: s para texto, d para decimal e i para inteiro.
        mysqli_stmt_bind_param($stmt, 'ssdiii', $rota['nome'], $rota['descricao'],
            $rota['extensao'], $rota['tempo'], $id_trem, $id);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_rota'] = 'Rota atualizada com sucesso!';    
            $_SESSION['sucesso_rota'] = true;
            header('Location: cadastrar_rotas.php');    
            exit();
        }

        $mensagem = "Não foi possível salvar as alterações.";
    }
}

$trens = mysqli_query($conn, "SELECT id_trem, nome FROM trens ORDER BY nome");
