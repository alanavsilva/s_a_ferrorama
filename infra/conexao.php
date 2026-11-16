<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Detalhes técnicos ficam no log, inclusive nas falhas de consulta.
set_exception_handler(static function (Throwable $erro): void {
    error_log((string) $erro);
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="pt-BR"><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<title>Não foi possível concluir | TremTech</title><body><main>'
        . '<h1>Não foi possível concluir a operação</h1>'
        . '<p>Tente novamente. Se o problema continuar, avise o responsável pelo sistema.</p>'
        . '<button type="button" onclick="history.back()">Voltar</button></main></body></html>';
});

// Valores padrão para a demonstração local no XAMPP.
// Em ambiente com credenciais próprias, configure as variáveis DB_* no servidor.
// Senhas reais não devem ser gravadas no código publicado.
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'SA_FERRORAMA';
$port = (int) (getenv('DB_PORT') ?: 3306);

$conn = new mysqli($host, $username, $pass, $dbname, $port);
$conn->set_charset('utf8mb4');
