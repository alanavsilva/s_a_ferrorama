
<?php

function consultarClima(): array
{
    $url = 'https://api.open-meteo.com/v1/forecast'
        . '?latitude=-26.3044'
        . '&longitude=-48.8456'
        . '&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m'
        . '&timezone=America%2FSao_Paulo';

    $contexto = stream_context_create([
        'http' => [
            'timeout' => 8,
            'ignore_errors' => true
        ]
    ]);

    $resposta = @file_get_contents($url, false, $contexto);

    if ($resposta === false) {
        return ['erro' => 'Não foi possível consultar o clima.'];
    }

    $dados = json_decode($resposta, true);

    if (!is_array($dados) || !isset($dados['current'])) {
        return ['erro' => 'A API de clima não retornou dados válidos.'];
    }

    $clima = $dados['current'];

    
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

    return [
        'temperatura' => $clima['temperature_2m'] ?? null,
        'sensacao' => $clima['apparent_temperature'] ?? null,
        'umidade' => $clima['relative_humidity_2m'] ?? null,
        'precipitacao' => $clima['precipitation'] ?? null,
        'vento' => $clima['wind_speed_10m'] ?? null,
        'condicao' => $condicoes[$clima['weather_code'] ?? -1]
            ?? 'Condição não identificada',
        'atualizado' => $clima['time'] ?? null
    ];
}
