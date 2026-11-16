<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferrorama - Sistema TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261115-landing9">
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
            <p>SISTEMA DE ORGANIZAÇÃO DO FERRORAMA</p>
            <h1 id="titulo-inicio">Bem-vindo ao TremTech</h1>
            <p class="landing-explicacao">Cadastros de trens, rotas e sensores, acompanhamento de falhas e relatórios: tudo em um só lugar.</p>
            <a class="landing-cta" href="../../index.php">Acessar o sistema</a>
        </div>
        <aside class="landing-mini-stage" aria-label="Trem em movimento no trilho">
            <div class="landing-mini-cabecalho">
                <span class="landing-mini-identidade">TRENZINHO TREMTECH</span>
                <span class="landing-mini-sinal-estacao" aria-hidden="true">●</span>
            </div>
            <div class="landing-mini-trilho" aria-hidden="true">
                <span class="landing-mini-travessas"></span>
                <span class="landing-mini-trilho-linha landing-mini-trilho-linha-cima"></span>
                <span class="landing-mini-trilho-linha landing-mini-trilho-linha-baixo"></span>
                <svg class="landing-mini-trem" viewBox="0 0 100 66">
                    <rect x="10" y="10" width="80" height="42" rx="13" fill="#ffffff" stroke="#701015" stroke-width="4"/>
                    <path d="M14 29h72" stroke="#701015" stroke-width="3"/>
                    <rect x="20" y="17" width="20" height="10" rx="3" fill="#f1dddd"/>
                    <rect x="45" y="17" width="20" height="10" rx="3" fill="#f1dddd"/>
                    <rect x="70" y="17" width="11" height="10" rx="3" fill="#f1dddd"/>
                    <circle cx="29" cy="53" r="7" fill="#ffffff" stroke="#701015" stroke-width="3"/>
                    <circle cx="72" cy="53" r="7" fill="#ffffff" stroke="#701015" stroke-width="3"/>
                    <circle cx="82" cy="36" r="3" fill="#b5222b"/>
                </svg>
            </div>
            <p class="landing-mini-proxima">Próxima parada: <strong>Ferrorama</strong></p>
        </aside>
    </section>
    <section class="home_sobre landing-sobre landing-objetivo" id="sobre" aria-labelledby="titulo-sobre">
        <div class="landing-objetivo-intro">
            <p class="landing-objetivo-etiqueta">SOBRE O PROJETO</p>
            <h2 id="titulo-sobre">Para que serve o TremTech?</h2>
        </div>
        <div class="landing-objetivo-copy">
            <p class="landing-objetivo-destaque">O TremTech reúne as informações do Ferrorama para facilitar a organização dos cadastros e o acompanhamento das ocorrências.</p>
            <p>As informações se conectam: cada trem tem um responsável, cada rota é ligada a um trem e cada sensor fica associado ao trem correspondente.</p>
        </div>
    </section>
    <section class="landing-perfis" aria-labelledby="titulo-perfis">
        <div class="landing-perfis-intro">
            <p class="landing-recursos-etiqueta">PERFIS DE ACESSO</p>
            <h2 id="titulo-perfis">Quem usa o TremTech?</h2>
        </div>
        <div class="landing-perfis-grade">
            <article class="landing-perfil">
                <span class="landing-perfil-marca" aria-hidden="true">A</span>
                <div>
                    <h3>Administrador</h3>
                    <p>Organiza as contas, gerencia os cadastros de usuários, trens, rotas e sensores e cuida das falhas registradas.</p>
                </div>
            </article>
            <article class="landing-perfil">
                <span class="landing-perfil-marca" aria-hidden="true">F</span>
                <div>
                    <h3>Funcionário</h3>
                    <p>Consulta as informações disponíveis sobre a operação e acompanha as falhas registradas no sistema.</p>
                </div>
            </article>
        </div>
    </section>
    <section class="landing-recursos" aria-labelledby="titulo-recursos">
        <div class="landing-recursos-intro">
            <p class="landing-recursos-etiqueta">ÁREAS DO SISTEMA</p>
            <h2 id="titulo-recursos">O que você encontra no TremTech</h2>
        </div>
<div class="landing-recursos-grade">
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Página inicial</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>Veja um resumo dos cadastros, acompanhe falhas e consulte informações de clima e CEP.</p></div>
            </details>
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Usuários</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>O administrador cadastra e organiza as contas usadas para acessar o TremTech.</p></div>
            </details>
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Trens</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>Consulte o nome, a velocidade média cadastrada e quem é responsável por cada trem.</p></div>
            </details>
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Rotas</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>Veja a descrição, a distância, o tempo previsto e o trem relacionado a cada rota.</p></div>
            </details>
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Sensores</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>Consulte o tipo de cada sensor e veja a qual trem ele está associado.</p></div>
            </details>
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Falhas</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>Consulte as ocorrências, confira o status e acompanhe as ações de resolução ou reabertura.</p></div>
            </details>
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Monitoramento</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>Veja a velocidade, a temperatura e o estado operacional relacionados ao sensor selecionado.</p></div>
            </details>
            <details class="landing-recurso">
                <summary><span class="landing-recurso-titulo">Relatórios</span><span class="landing-recurso-sinal" aria-hidden="true"></span></summary>
                <div class="landing-recurso-conteudo"><p>Gere um panorama das falhas por sensor e mês, com descrição, data, status e totais de ocorrências resolvidas ou em aberto.</p></div>
            </details>
        </div>
    </section>
    <section class="home_sobre landing-trajeto" id="trajeto" aria-labelledby="titulo-trajeto">
        <div class="landing-trajeto-cabecalho">
            <div>
                <h2 id="titulo-trajeto">Percurso do trem</h2>
                <p>Acompanhe o trem pelos trilhos, pela ponte e pelos desvios.</p>
            </div>
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













