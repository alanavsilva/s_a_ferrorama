<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferrorama - Sistema TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body class="landing_body">

    <!-- Menu -->
    <nav class="menu_topo">

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

        <!-- Boas-vindas -->
        <section class="home_boas_vindas landing_boas_vindas">

            <div>
                <p>SISTEMA DE ORGANIZAÇÃO DO FERRORAMA</p>
                <h1>Bem-vindo ao TremTech</h1>

                <p class="landing_explicacao">
                    Cadastros de trens, rotas e sensores, acompanhamento
                    de falhas e relatórios: tudo em um só lugar.
                </p>

                <a class="landing_cta" href="../../index.php">Acessar o sistema</a>
            </div>

            <!-- Trenzinho -->
            <div class="landing_mini_stage">

                <div class="landing_mini_cabecalho">
                    <span class="landing_mini_identidade">TRENZINHO TREMTECH</span>
                    <span class="landing_mini_sinal_estacao" aria-hidden="true">●</span>
                </div>

                <div class="landing_mini_trilho">

                    <span class="landing_mini_travessas"></span>
                    <span class="landing_mini_trilho_linha landing_mini_trilho_linha_cima"></span>
                    <span class="landing_mini_trilho_linha landing_mini_trilho_linha_baixo"></span>

                    <svg class="landing_mini_trem" viewBox="0 0 100 66"
                         role="img" aria-label="Trem animado">

                        <rect class="corpo" x="10" y="10" width="80" height="42" rx="13"/>

                        <path class="divisao" d="M14 29h72"/>

                        <rect class="janela" x="20" y="17" width="20" height="10" rx="3"/>
                        <rect class="janela" x="45" y="17" width="20" height="10" rx="3"/>
                        <rect class="janela" x="70" y="17" width="11" height="10" rx="3"/>

                        <circle class="roda" cx="29" cy="53" r="7"/>
                        <circle class="roda" cx="72" cy="53" r="7"/>

                        <circle class="luz" cx="82" cy="36" r="3"/>

                    </svg>

                </div>

                <p class="landing_mini_proxima">
                    Próxima parada: <strong>Ferrorama</strong>
                </p>

            </div>

        </section>

        <!-- Sobre o projeto -->
        <section class="home_sobre landing_sobre landing_objetivo" id="sobre">

            <div class="landing_objetivo_intro">
                <p class="landing_objetivo_etiqueta">SOBRE O PROJETO</p>
                <h2>Para que serve o TremTech?</h2>
            </div>

            <div class="landing_objetivo_copy">
                <p class="landing_objetivo_destaque">
                    O TremTech reúne as informações do Ferrorama para facilitar
                    a organização dos cadastros e o acompanhamento das ocorrências.
                </p>

                <p>
                    As informações se conectam: cada trem tem um responsável,
                    cada rota é ligada a um trem e cada sensor fica vinculado a uma rota.
                </p>
            </div>

        </section>

        <!-- Perfis -->
        <section class="landing_perfis">

            <div class="landing_perfis_intro">
                <p class="landing_recursos_etiqueta">PERFIS DE ACESSO</p>
                <h2>Quem usa o TremTech?</h2>
            </div>

            <div class="landing_perfis_grade">

                <article class="landing_perfil">
                    <span class="landing_perfil_marca" aria-hidden="true">A</span>

                    <div>
                        <h3>Administrador</h3>
                        <p>
                            Organiza as contas, gerencia os cadastros de usuários,
                            trens, rotas e sensores e cuida das falhas registradas.
                        </p>
                    </div>
                </article>

                <article class="landing_perfil">
                    <span class="landing_perfil_marca" aria-hidden="true">F</span>

                    <div>
                        <h3>Funcionário</h3>
                        <p>
                            Consulta as informações disponíveis sobre a operação
                            e acompanha as falhas registradas no sistema.
                        </p>
                    </div>
                </article>

            </div>

        </section>

        <!-- Funcionalidades -->
        <section class="landing_recursos">

            <div class="landing_recursos_intro">
                <p class="landing_recursos_etiqueta">ÁREAS DO SISTEMA</p>
                <h2>O que você encontra no TremTech</h2>
            </div>

            <div class="landing_recursos_grade">

                <details class="landing_recurso">
                    <summary>Página inicial</summary>
                    <p>
                        Veja um resumo dos cadastros, acompanhe falhas
                        e consulte informações de clima e CEP.
                    </p>
                </details>

                <details class="landing_recurso">
                    <summary>Usuários</summary>
                    <p>
                        O administrador cadastra e organiza as contas
                        usadas para acessar o TremTech.
                    </p>
                </details>

                <details class="landing_recurso">
                    <summary>Trens</summary>
                    <p>
                        Consulte o nome, a velocidade média cadastrada
                        e quem é responsável por cada trem.
                    </p>
                </details>

                <details class="landing_recurso">
                    <summary>Rotas</summary>
                    <p>
                        Veja a descrição, a distância, o tempo previsto
                        e o trem relacionado a cada rota.
                    </p>
                </details>

                <details class="landing_recurso">
                    <summary>Sensores</summary>
                    <p>
                        Consulte o tipo de cada sensor e veja
                        a rota à qual ele está vinculado.
                    </p>
                </details>

                <details class="landing_recurso">
                    <summary>Falhas</summary>
                    <p>
                        Cadastre falhas vinculadas a sensores de Status,
                        confira a situação e acompanhe a resolução ou reabertura.
                    </p>
                </details>

                <details class="landing_recurso">
                    <summary>Monitoramento</summary>
                    <p>
                        Veja a velocidade, a temperatura e o estado operacional
                        relacionados ao sensor selecionado.
                    </p>
                </details>

                <details class="landing_recurso">
                    <summary>Relatórios</summary>
                    <p>
                        Consulte as leituras de velocidade e temperatura
                        ou as falhas de um sensor na data selecionada.
                    </p>
                </details>

            </div>

        </section>

        <!-- Mapa -->
        <section class="home_sobre landing_trajeto" id="trajeto">

            <div class="landing_trajeto_cabecalho">
                <div>
                    <h2>Percurso do trem</h2>
                    <p>Acompanhe o trem pelos trilhos, pela ponte e pelos desvios.</p>
                </div>
            </div>

            <svg class="mapa_trilhos landing_mapa_trilhos"
                 viewBox="0 0 760 330"
                 role="img" aria-label="Percurso animado do trem">

                <!-- Trilhos -->
                <polyline class="trilho"
                    points="70,175 80,115 150,75 250,60 360,65
                    470,90 580,120 670,165 690,220 640,265
                    540,290 420,300 300,290 190,260 100,225 70,175"/>

                <polyline class="trilho"
                    points="70,175 140,185 250,165 360,160
                    470,175 545,200 640,230"/>

                <polyline class="trilho"
                    points="250,165 270,235 320,285"/>

                <polyline class="trilho"
                    points="360,160 420,130 480,130 580,120"/>

                <polyline class="trilho"
                    points="545,200 580,235 640,265"/>

                <!-- Ponte -->
                <polyline class="trilho_ponte" points="250,60 360,65"/>

                <text x="275" y="45" class="rotulo_ponte">Ponte</text>

                <!-- Movimento -->
                <g>
                    <animateMotion dur="46.25s" repeatCount="indefinite"
                        path="M70 175 L80 115 150 75 250 60
                        360 65 470 90 580 120 670 165
                        690 220 640 265 540 290 420 300
                        300 290 190 260 100 225 70 175
                        140 185 250 165 360 160 470 175
                        545 200 580 235 545 200 470 175
                        360 160 420 130 480 130 580 120
                        480 130 420 130 360 160 270 235
                        320 285 270 235 360 160 250 165
                        140 185 70 175"/>

                    <circle class="marcador_trem" r="12"/>
                    <circle class="marcador_centro" r="4"/>
                </g>

            </svg>

        </section>

    </main>

    <!-- Rodapé -->
    <footer class="home_rodape landing_rodape">
        <p>Ferrorama - Sistema de Monitoramento Ferroviário</p>
        <p>2026 TremTech</p>
    </footer>

</body>
</html>