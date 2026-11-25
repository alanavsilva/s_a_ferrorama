<?php
// PROTEÇÃO DAS TELAS. As páginas internas usam as funções deste arquivo.
// exigir_login verifica a conta. exigir_administrador também verifica o perfil.
// session_status informa se o PHP já abriu uma sessão nesta página.
// PHP_SESSION_ACTIVE é o nome que o PHP usa para dizer: a sessão já está aberta.
// O !== significa diferente de. Só chamamos session_start quando ainda não há sessão ativa.
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
    // global deixa a função usar a variável $conn que a página já criou para falar com o banco.
    global $conn;
    if (!isset($conn)) {
        // __DIR__ é a pasta onde este arquivo está, neste caso infra. O nome tem dois underlines de cada lado.
        // O ponto junta o caminho de infra com /conexao.php para abrir o arquivo certo.
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

