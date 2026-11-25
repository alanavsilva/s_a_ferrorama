<?php
// session_status indica se a sessão está ativa (PHP_SESSION_ACTIVE) para o PHP abrir só quando precisar.
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

    // global permite usar a conexão $conn para buscar a conta da sessão e sair se ela foi excluída.
    global $conn;
    if (!isset($conn)) {
        // __DIR__ dá a pasta deste arquivo e o ponto junta esse caminho com /conexao.php.
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
        // 403 significa acesso negado. A pessoa fez login, mas não tem o perfil exigido para esta ação.
        http_response_code(403);
        exit('Acesso negado.');
    }
}

// Exibe nomes e descrições como texto. Se alguém digitar uma tag de HTML, ela aparece escrita na tela.
function escapar($texto)
{
    return htmlspecialchars($texto);
}

