<?php

$arquivoConfiguracao = __DIR__ . '/conexao.local.php';

if (!is_file($arquivoConfiguracao)) {
    error_log('Arquivo de configuração do banco não encontrado.');
    http_response_code(500);
    exit('Configure o banco de dados em infra/conexao.local.php. Consulte o README.');
}

$configuracao = require $arquivoConfiguracao;

try {
    $conn = new mysqli(
        $configuracao['host'],
        $configuracao['usuario'],
        $configuracao['senha'],
        $configuracao['banco'],
        (int) ($configuracao['porta'] ?? 3306)
    );
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Falha na conexão com o banco de dados: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível conectar ao banco. Confira infra/conexao.local.php.');
}
