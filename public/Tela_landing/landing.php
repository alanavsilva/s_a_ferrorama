<!DOCTYPE html>
<!-- Página pública. Apresenta o TremTech, mostra o percurso e tem o link para entrar no sistema. -->
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferrorama - Sistema TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=nomes-underscore-1">
</head>

<body class="landing_body">
    <!-- Menu da página pública. Os links com # apontam para partes desta mesma página. -->
    <nav class="menu_topo" aria-label="Navegação principal">
        <a class="menu_topo_marca" href="#inicio">
            <img src="https://img.icons8.com/ios/100/701015/train.png" alt="">
            <span>SISTEMA TREMTECH</span>
        </a>

        <div class="menu_topo_links">
            <a href="#inicio">
                <img src="https://img.icons8.com/ios-filled/22/701015/home.png" alt="">
                Início
            </a>
            <a href="#sobre">
                <img src="https://img.icons8.com/ios-filled/22/701015/info.png" alt="">
                Sobre o Ferrorama
            </a>
            <a href="#trajeto">
                <img src="https://img.icons8.com/ios-filled/22/701015/map.png" alt="">
                Trajeto
            </a>
        </div>

        <a class="menu_topo_sair" href="../../index.php">Acessar sistema</a>
    </nav>

    <main class="home_conteudo landing_main" id="inicio">
        <section class="home_boas_vindas landing_boas_vindas" aria-labelledby="titulo_inicio">
            <div>
                <p>SISTEMA DE ORGANIZAÇÃO DO FERRORAMA</p>
                <h1 id="titulo_inicio">Bem-vindo ao TremTech</h1>
                <p class="landing_explicacao">Cadastros de trens, rotas e sensores, acompanhamento de falhas e relatórios: tudo em um só lugar.</p>
                <a class="landing_cta" href="../../index.php">Acessar o sistema</a>
            </div>

            <aside class="landing_mini_stage" aria-label="Trem em movimento no trilho">
                <div class="landing_mini_cabecalho">
                    <span class="landing_mini_identidade">TRENZINHO TREMTECH</span>
                    <span class="landing_mini_sinal_estacao" aria-hidden="true">●</span>
                </div>

                <div class="landing_mini_trilho" aria-hidden="true">
                    <span class="landing_mini_travessas"></span>
                    <span class="landing_mini_trilho_linha landing_mini_trilho_linha_cima"></span>
                    <span class="landing_mini_trilho_linha landing_mini_trilho_linha_baixo"></span>

                    <!-- O SVG desenha o trem com retângulos, linhas e círculos. -->
                    <svg class="landing_mini_trem" viewBox="0 0 100 66">
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

                <p class="landing_mini_proxima">Próxima parada: <strong>Ferrorama</strong></p>
            </aside>
        </section>

        <section class="home_sobre landing_sobre landing_objetivo" id="sobre" aria-labelledby="titulo_sobre">
            <div class="landing_objetivo_intro">
                <p class="landing_objetivo_etiqueta">SOBRE O PROJETO</p>
                <h2 id="titulo_sobre">Para que serve o TremTech?</h2>
            </div>
            <div class="landing_objetivo_copy">
                <p class="landing_objetivo_destaque">O TremTech reúne as informações do Ferrorama para facilitar a organização dos cadastros e o acompanhamento das ocorrências.</p>
                <p>As informações se conectam: cada trem tem um responsável, cada rota é ligada a um trem e cada sensor fica vinculado a uma rota.</p>
            </div>
        </section>

        <section class="landing_perfis" aria-labelledby="titulo_perfis">
            <div class="landing_perfis_intro">
                <p class="landing_recursos_etiqueta">PERFIS DE ACESSO</p>
                <h2 id="titulo_perfis">Quem usa o TremTech?</h2>
            </div>

            <div class="landing_perfis_grade">
                <article class="landing_perfil">
                    <span class="landing_perfil_marca" aria-hidden="true">A</span>
                    <div>
                        <h3>Administrador</h3>
                        <p>Organiza as contas, gerencia os cadastros de usuários, trens, rotas e sensores e cuida das falhas registradas.</p>
                    </div>
                </article>

                <article class="landing_perfil">
                    <span class="landing_perfil_marca" aria-hidden="true">F</span>
                    <div>
                        <h3>Funcionário</h3>
                        <p>Consulta as informações disponíveis sobre a operação e acompanha as falhas registradas no sistema.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="landing_recursos" aria-labelledby="titulo_recursos">
            <div class="landing_recursos_intro">
                <p class="landing_recursos_etiqueta">ÁREAS DO SISTEMA</p>
                <h2 id="titulo_recursos">O que você encontra no TremTech</h2>
            </div>

            <!-- details e summary deixam o navegador abrir e fechar os cards. -->
            <div class="landing_recursos_grade">
                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Página inicial</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>Veja um resumo dos cadastros, acompanhe falhas e consulte informações de clima e CEP.</p></div>
                </details>

                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Usuários</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>O administrador cadastra e organiza as contas usadas para acessar o TremTech.</p></div>
                </details>

                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Trens</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>Consulte o nome, a velocidade média cadastrada e quem é responsável por cada trem.</p></div>
                </details>

                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Rotas</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>Veja a descrição, a distância, o tempo previsto e o trem relacionado a cada rota.</p></div>
                </details>

                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Sensores</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>Consulte o tipo de cada sensor e veja a rota à qual ele está vinculado.</p></div>
                </details>

                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Falhas</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>Cadastre falhas vinculadas a sensores de Status, confira a situação e acompanhe a resolução ou reabertura.</p></div>
                </details>

                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Monitoramento</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>Veja a velocidade, a temperatura e o estado operacional relacionados ao sensor selecionado.</p></div>
                </details>

                <details class="landing_recurso">
                    <summary><span class="landing_recurso_titulo">Relatórios</span><span class="landing_recurso_sinal" aria-hidden="true"></span></summary>
                    <div class="landing_recurso_conteudo"><p>Consulte as leituras de velocidade e temperatura ou as falhas de um sensor na data selecionada.</p></div>
                </details>
            </div>
        </section>

        <section class="home_sobre landing_trajeto" id="trajeto" aria-labelledby="titulo_trajeto">
            <div class="landing_trajeto_cabecalho">
                <div>
                    <h2 id="titulo_trajeto">Percurso do trem</h2>
                    <p>Acompanhe o trem pelos trilhos, pela ponte e pelos desvios.</p>
                </div>
            </div>

            <svg class="mapa_trilhos landing_mapa_trilhos" viewBox="0 0 760 330"
                 role="img" aria-label="Animação de um trem percorrendo trilhos, ponte e desvios">

                <!-- O pattern repete o mesmo quadrado para formar a grade do fundo. -->
                <defs>
                    <pattern id="gradeMapaLanding" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M40 0H0V40" fill="none" stroke="#e5dddd" stroke-width="2"/>
                    </pattern>
                </defs>
                <rect width="760" height="330" fill="url(#gradeMapaLanding)"/>

                <!-- Cada polyline liga os pontos para desenhar um trecho do trilho. -->
                <polyline class="trilho" points="70,175 80,115 150,75 250,60 360,65 470,90 580,120 670,165 690,220 640,265 540,290 420,300 300,290 190,260 100,225 70,175"/>
                <polyline class="trilho" points="70,175 140,185 250,165 360,160 470,175 545,200 640,230"/>
                <polyline class="trilho" points="250,165 270,235 320,285"/>
                <polyline class="trilho" points="360,160 420,130 480,130 580,120"/>
                <polyline class="trilho" points="545,200 580,235 640,265"/>
                <polyline class="trilho_ponte" points="250,60 360,65"/>
                <text x="275" y="45" class="rotulo_ponte">Ponte</text>

                <g id="marcadorTremLanding">
                        <!-- O SVG move o marcador pelo caminho, sem cálculo no JavaScript. -->
                        <animateMotion dur="46.25s" repeatCount="indefinite" path="M 70 175 L 80 115 150 75 250 60 360 65 470 90 580 120 670 165 690 220 640 265 540 290 420 300 300 290 190 260 100 225 70 175 140 185 250 165 360 160 470 175 545 200 580 235 545 200 470 175 360 160 420 130 480 130 580 120 480 130 420 130 360 160 270 235 320 285 270 235 360 160 250 165 140 185 70 175"/>
                    <circle r="12" class="marcador_trem"/>
                    <circle r="4" class="marcador_centro"/>
                </g>
            </svg>
        </section>
    </main>

    <footer class="home_rodape landing_rodape">
        <p>Ferrorama - Sistema de Monitoramento Ferroviário</p>
        <p>2026 TremTech</p>
    </footer>

<script src="../../script/validacao.js?v=simples-7"></script>
</body>
</html>




