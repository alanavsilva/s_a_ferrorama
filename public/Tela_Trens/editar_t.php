<?php
// EDITAR TREM. O link Editar manda o ID no endereço e o PHP busca o trem no banco.
// A página de cadastro usa este arquivo para buscar o registro e salvar a edição.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();

// O endereço antigo leva à edição dentro da página de cadastro.
if (empty($editando)) {
    $id = (int) ($_GET['id'] ?? 0);
    header('Location: cadastrar_trens.php?editar=' . $id . '#cadastro');
    exit();
}

// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) ($_GET['editar'] ?? 0);
$sql = "SELECT * FROM trens WHERE id_trem = $id";
$resultado = mysqli_query($conn, $sql);
$trem = mysqli_fetch_assoc($resultado);

if (!$trem) {
    exit('Trem não encontrado.');
}

$nome = $trem['nome'];
$velocidade = $trem['velocidade_media'];
$id_usuario = $trem['id_usuario'];
$mensagem = '';

// Ao enviar o formulário, este mesmo arquivo salva o trem indicado na URL.
// Quando a página é só aberta, este bloco é pulado. Ele roda quando a pessoa clica em Salvar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $velocidade = $_POST['velocidade'] ?? '';
    $id_usuario = (int) ($_POST['id_usuario'] ?? 0);

    $sql = "SELECT id_usuario FROM usuarios WHERE id_usuario = $id_usuario";
    $responsavel = mysqli_query($conn, $sql);

    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
        $mensagem = 'O nome deve ter entre 3 e 100 caracteres.';
    } elseif (!is_numeric($velocidade)) {
        $mensagem = 'Informe a velocidade média em km/h.';
    } elseif (mysqli_num_rows($responsavel) === 0) {
        $mensagem = 'Selecione um responsável cadastrado.';
    } else {
        $sql = "UPDATE trens SET nome = ?, velocidade_media = ?, id_usuario = ?
                WHERE id_trem = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sdii', $nome, $velocidade, $id_usuario, $id);
        // Se o banco não conseguir gravar, mostra a mensagem abaixo do formulário.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }
        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_trem'] = 'Trem atualizado com sucesso!';
            $_SESSION['sucesso_trem'] = true;
            header('Location: cadastrar_trens.php');
            exit();
        }

        $mensagem = 'Não foi possível atualizar o trem.';
    }
}

$usuarios = mysqli_query($conn, "SELECT id_usuario, nome FROM usuarios ORDER BY nome");
