<?php
// EDITAR USUÁRIO. O link Editar manda o ID no endereço e o PHP busca o usuário no banco.
// A página de cadastro usa este arquivo para buscar o registro e salvar a edição.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Antes de abrir a edição, o PHP verifica se a pessoa logada é administradora.
exigir_administrador();

// O endereço antigo leva à edição dentro da página de cadastro.
if (empty($editando)) {
    $id = (int) ($_GET['id'] ?? 0);
    header('Location: cadastrar_usuario.php?editar=' . $id . '#cadastro');
    exit();
}

// Busca pelo ID do link Editar e preenche o formulário.
$id = (int) ($_GET['editar'] ?? 0);
$sql = "SELECT nome, email, tipo FROM usuarios WHERE id_usuario = $id";
$resultado = mysqli_query($conn, $sql);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    exit('Usuário não encontrado.');
}

$nome = $usuario['nome'];
$email = $usuario['email'];
$tipo = $usuario['tipo'];
$mensagem = '';

// Quando a página é só aberta, este bloco é pulado. Ele roda quando a pessoa clica em Salvar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if ($nome === '' || mb_strlen($nome) > 100) {
        $mensagem = 'Informe um nome de até 100 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $mensagem = 'Informe um e-mail válido de até 100 caracteres.';
    } elseif ($tipo !== 'Administrador' && $tipo !== 'Funcionário') {
        $mensagem = 'Selecione um tipo de usuário válido.';
    } else {
        // Só atualiza a senha se a pessoa preencher o campo Nova senha.
        if ($senha === '') {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id_usuario = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssi', $nome, $email, $tipo, $id);
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ?, senha = ? WHERE id_usuario = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $email, $tipo, $senha_hash, $id);
        }

        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }

        mysqli_stmt_close($stmt);

        if ($salvou) {
            // Se editou a própria conta, atualiza também os dados da sessão.
            if ($id == $_SESSION['id_usuario']) {
                $_SESSION['nome'] = $nome;
                $_SESSION['email'] = $email;
                $_SESSION['tipo'] = $tipo;
            }

            $_SESSION['mensagem_usuario'] = 'Usuário atualizado com sucesso!';
            $_SESSION['sucesso_usuario'] = true;
            header('Location: cadastrar_usuario.php');
            exit();
        }

        $mensagem = 'Não foi possível salvar. Confira se o e-mail já está em uso.';
    }
}
