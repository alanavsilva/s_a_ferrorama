<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax'
    ]);
    session_start();
}

function exigir_login(): void
{
    global $conn;
    if (isset($_SESSION['id_usuario'])) {
        if (!isset($conn)) {
            require __DIR__ . '/conexao.php';
        }
        $stmt = $conn->prepare('SELECT nome, email, tipo FROM usuarios WHERE id_usuario = ?');
        $stmt->bind_param('i', $_SESSION['id_usuario']);
        $stmt->execute();
        $usuarioAtual = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$usuarioAtual) {
            $_SESSION = [];
            session_destroy();
        } else {
            $_SESSION['nome'] = $usuarioAtual['nome'];
            $_SESSION['email'] = $usuarioAtual['email'];
            $_SESSION['tipo'] = $usuarioAtual['tipo'];
        }
    }
    if (!isset($_SESSION['id_usuario'], $_SESSION['tipo'])) {
        header('Location: ../../index.php');
        exit;
    }
}

function exigir_administrador(): void
{
    exigir_login();

    if (strcasecmp($_SESSION['tipo'], 'Administrador') !== 0) {       
        http_response_code(403);
        exit('Acesso negado.');
    }
}

function token_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verificar_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token)) {
        http_response_code(403);
        exit('Requisição inválida.');
    }

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Requisição inválida.');
    }
}

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}




// Os nomes de tabela e coluna são definidos apenas pelo código, nunca pelo formulário.
function validar_acao_crud(mysqli $conn, $acao, string $tabela, string $coluna, string $campo): void
{
    $permitidos = ['trens' => 'id_trem', 'rotas' => 'id_rotas', 'sensores' => 'id_sensor', 'usuarios' => 'id_usuario'];
    if (($permitidos[$tabela] ?? '') !== $coluna || !in_array($acao, ['criar', 'editar'], true)) {
        http_response_code(400);
        exit('Ação inválida. Volte à listagem e tente novamente.');
    }
    if ($acao !== 'editar') return;
    $id = filter_input(INPUT_POST, $campo, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Registro inválido. Volte à listagem e selecione um registro cadastrado.');
    }
    $stmt = $conn->prepare("SELECT $coluna FROM $tabela WHERE $coluna = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $existe = $stmt->get_result()->num_rows === 1;
    $stmt->close();
    if (!$existe) {
        http_response_code(404);
        exit('Registro não encontrado. Volte à listagem e atualize a página.');
    }
}
