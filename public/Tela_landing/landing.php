<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferrorama - Sistema TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261115-ui">
</head>
<body class="landing-body">
<nav class="home_barra" aria-label="Navegação principal">
    <a class="home_logo" href="#inicio">
        <img class="home-imagem-trem" src="https://img.icons8.com/ios/50/701015/train.png" alt="">
        SISTEMA TREMTECH
    </a>
    <a class="home_link" href="#inicio">Início</a>
    <a class="home_link" href="#sobre">Sobre o Ferrorama</a>
    <a class="home_link" href="#trajeto">Trajeto</a>
    <a class="home-logout-link landing-acesso" href="../../index.php">Acessar sistema</a>
</nav>
<main class="home_conteudo landing-main" id="inicio">
    <section class="home_boas_vindas landing-boas-vindas" aria-labelledby="titulo-inicio">
        <div>
            <p>APRESENTAÇÃO DO SISTEMA TREMTECH</p>
            <h1 id="titulo-inicio">Conheça o Ferrorama</h1>
            <p class="landing-explicacao">O sistema organiza os cadastros de trens e responsáveis, rotas e sensores. Também registra falhas operacionais e reúne relatórios para consulta.</p>
        </div>
    </section>
    <section class="home_sobre landing-sobre" id="sobre" aria-labelledby="titulo-sobre">
        <h2 id="titulo-sobre">Como funciona</h2>
        <p>Administradores podem criar e alterar cadastros. Funcionários consultam as informações permitidas pelo sistema e acompanham as falhas.</p>
        <p>As leituras do monitoramento são demonstrativas; o projeto não está conectado a sensores reais.</p>
    </section>
    <section class="home_sobre landing-trajeto" id="trajeto" aria-labelledby="titulo-trajeto">
        <div class="landing-trajeto-cabecalho">
            <div>
                <h2 id="titulo-trajeto">Trajeto ilustrativo</h2>
                <p>A animação representa o percurso do trem e passa pela ponte e pelos desvios.</p>
            </div>
            <span class="landing-trajeto-status"><span></span> Trajeto demonstrativo</span>
        </div>
        <svg class="mapa-trilhos landing-mapa-trilhos" viewBox="0 0 760 330"
             role="img" aria-label="Animação de um trem percorrendo trilhos, ponte e desvios">
            <defs>
                <pattern id="gradeMapaLanding" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M40 0H0V40" fill="none" stroke="#e5dddd" stroke-width="2"/>
                </pattern>
            </defs>
            <rect width="760" height="330" fill="url(#gradeMapaLanding)"/>
            <polyline class="trilho" points="70,175 80,115 150,75 250,60 360,65 470,90 580,120 670,165 690,220 640,265 540,290 420,300 300,290 190,260 100,225 70,175"/>
            <polyline class="trilho" points="70,175 140,185 250,165 360,160 470,175 545,200 640,230"/>
            <polyline class="trilho" points="250,165 270,235 320,285"/>
            <polyline class="trilho" points="360,160 420,130 480,130 580,120"/>
            <polyline class="trilho" points="545,200 580,235 640,265"/>
            <polyline class="trilho-ponte" points="250,60 360,65"/>
            <text x="275" y="45" class="rotulo-ponte">Ponte</text>
            <g id="marcadorTremLanding" transform="translate(70 175)">
                <circle r="12" class="marcador-trem"/>
                <circle r="4" class="marcador-centro"/>
            </g>
        </svg>
    </section>
</main>
<footer class="home_rodape landing-rodape">
    <p>Ferrorama - Sistema de Monitoramento Ferroviário</p>
    <p>2026 TremTech</p>
</footer>
<script>
const caminhoDoTremLanding = [
    [70,175],[80,115],[150,75],[250,60],[360,65],[470,90],
    [580,120],[670,165],[690,220],[640,265],[540,290],[420,300],
    [300,290],[190,260],[100,225],[70,175],
    [140,185],[250,165],[360,160],[470,175],[545,200],
    [580,235],[545,200],[470,175],[360,160],
    [420,130],[480,130],[580,120],[480,130],[420,130],[360,160],
    [270,235],[320,285],[270,235],[360,160],[250,165],[140,185],[70,175]
];
let trechoLanding = 0;
let progressoLanding = 0;
setInterval(() => {
    progressoLanding += 0.04;
    if (progressoLanding >= 1) {
        progressoLanding = 0;
        trechoLanding = (trechoLanding + 1) % (caminhoDoTremLanding.length - 1);
    }
    const inicio = caminhoDoTremLanding[trechoLanding];
    const fim = caminhoDoTremLanding[trechoLanding + 1];
    const x = inicio[0] + (fim[0] - inicio[0]) * progressoLanding;
    const y = inicio[1] + (fim[1] - inicio[1]) * progressoLanding;
    document.getElementById('marcadorTremLanding')
        .setAttribute('transform', 'translate(' + x + ' ' + y + ')');
}, 50);
</script>
</body>
</html>
