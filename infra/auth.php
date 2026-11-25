<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se a pessoa fez login. Sem uma sessão válida, a página volta para a tela de entrada.
function exigir_login()
{
    if (!isset($_SESSION['id_usuario'], $_SESSION['tipo'])) {
        header('Location: ../../index.php');
        exit();
    }

    // Busca a conta pelo ID guardado na sessão. Se ela foi excluída, encerra o login.
    global $conn;
    if (!isset($conn)) {
        require __DIR__ . '/conexao.php';
    }

    $id = (int) $_SESSION['id_usuario'];
    $resultado = mysqli_query($conn, "SELECT tipo FROM usuarios WHERE id_usuario = $id");
    $usuario = mysqli_fetch_assoc($resultado);

    if (!$usuario) {
        session_unset();
        session_destroy();
        header('Location: ../../index.php');
        exit();
    }

    $_SESSION['tipo'] = $usuario['tipo'];
}

// Confere se a pessoa é administradora antes de deixar cadastrar, editar ou excluir.
function exigir_administrador()
{
    exigir_login();

    if ($_SESSION['tipo'] !== 'Administrador') {
        http_response_code(403);
        exit('Acesso negado.');
    }
}

// Exibe nomes e descrições como texto. Se alguém digitar uma tag de HTML, ela aparece escrita na tela.
function escapar($texto)
{
    return htmlspecialchars($texto);
}

