<?php

// Consulta o Open-Meteo e devolve os campos que a Home usa.
function consultarClima()
{
    // A latitude e a longitude indicam Joinville.
    // current pede os dados atuais, e timezone define o fuso do horário.
    $url = 'https://api.open-meteo.com/v1/forecast'
        . '?latitude=-26.3044&longitude=-48.8456'
        . '&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m'
        . '&timezone=America%2FSao_Paulo';

    // O PHP espera até 8 segundos pela resposta.
    $opcoes = stream_context_create([
        'http' => ['timeout' => 8]
    ]);

    // O @ esconde o aviso do PHP para a Home mostrar a mensagem abaixo.
    $resposta = @file_get_contents($url, false, $opcoes);

    if ($resposta === false) {
        return ['erro' => 'Não foi possível consultar o clima.'];
    }

    // A resposta vem em JSON. O true faz o PHP ler os dados como array.
    $dados = json_decode($resposta, true);

    if (!isset($dados['current'])) {
        return ['erro' => 'A API de clima não retornou dados válidos.'];
    }

    $clima = $dados['current'];

    // Cada número que a API envia tem um significado.
    // A API manda um código numérico. Esta lista converte cada código em um texto.
    $condicoes = [
        0 => 'Céu limpo',
        1 => 'Predominantemente limpo',
        2 => 'Parcialmente nublado',
        3 => 'Nublado',
        45 => 'Neblina',
        48 => 'Neblina',
        51 => 'Garoa leve',
        53 => 'Garoa moderada',
        55 => 'Garoa intensa',
        61 => 'Chuva leve',
        63 => 'Chuva moderada',
        65 => 'Chuva intensa',
        80 => 'Pancadas de chuva leves',
        81 => 'Pancadas de chuva moderadas',
        82 => 'Pancadas de chuva intensas',
        95 => 'Trovoadas',
        96 => 'Trovoadas com granizo',
        99 => 'Trovoadas com granizo intenso'
    ];

    $condicao = 'Condição não identificada';

    if (isset($clima['weather_code'])) {
        $codigo = $clima['weather_code'];

        if (isset($condicoes[$codigo])) {
            $condicao = $condicoes[$codigo];
        }
    }

    // Os nomes da esquerda são os que a Home usa.
    // O ?? usa um texto vazio quando aquele valor não vem na resposta.
    return [
        'temperatura' => $clima['temperature_2m'] ?? '',
        'sensacao' => $clima['apparent_temperature'] ?? '',
        'umidade' => $clima['relative_humidity_2m'] ?? '',
        'precipitacao' => $clima['precipitation'] ?? '',
        'vento' => $clima['wind_speed_10m'] ?? '',
        'condicao' => $condicao,
        'atualizado' => $clima['time'] ?? ''
    ];
}