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

