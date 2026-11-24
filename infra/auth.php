<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Protege as páginas internas. A pessoa volta ao login se a sessão não for válida.
function exigir_login()
{
    if (!isset($_SESSION['id_usuario'], $_SESSION['tipo'])) {
        header('Location: ../../index.php');
        exit();
    }

    // Confere se a conta ainda existe e qual é o perfil atual dela.
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

// Confere o perfil no servidor antes de permitir uma alteração.
function exigir_administrador()
{
    exigir_login();

    if ($_SESSION['tipo'] !== 'Administrador') {
        http_response_code(403);
        exit('Acesso negado.');
    }
}

// Faz o navegador mostrar o valor como texto, mesmo se ele contiver HTML.
function escapar($texto)
{
    return htmlspecialchars($texto);
}

// Confere a ação e, em uma edição, se o registro existe.
function validar_acao_crud($conn, $acao, $tabela, $coluna, $campo)
{
    // Cada tabela tem seu próprio campo de ID.
    $ids = [
        'trens' => 'id_trem',
        'rotas' => 'id_rotas',
        'sensores' => 'id_sensor',
        'usuarios' => 'id_usuario'
    ];

    if (!isset($ids[$tabela]) || $ids[$tabela] !== $coluna) {
        exit('Tabela inválida.');
    }

    if ($acao === 'criar') {
        return;
    }

    if ($acao !== 'editar' || !isset($_POST[$campo])) {
        exit('Ação inválida.');
    }

    $id = (int) $_POST[$campo];
    $sql = "SELECT $coluna FROM $tabela WHERE $coluna = $id";
    $resultado = mysqli_query($conn, $sql);

    if (mysqli_num_rows($resultado) === 0) {
        exit('Registro não encontrado.');
    }
}