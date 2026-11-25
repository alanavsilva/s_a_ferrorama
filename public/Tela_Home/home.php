<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

// A página inicial só abre depois que a pessoa faz login.
exigir_login();

$nome = $_SESSION['nome'];
$tipo = $_SESSION['tipo'];
$ehAdministrador = ($tipo === 'Administrador');
$statusFiltro = 'todas';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'resolvidas' || $_GET['status'] === 'nao_resolvidas') {
        $statusFiltro = $_GET['status'];
    }
}

// Quando um administrador clica em Resolver ou Reabrir na Home, este trecho salva a nova situação da falha.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    if (isset($_POST['resolver']) || isset($_POST['reabrir'])) {
        $idFalha = (int) $_POST['id_falha'];

        if (isset($_POST['resolver'])) {
            $situacao = 'Resolvida';
        } else {
            $situacao = 'Não resolvida';
        }

        $sql = "UPDATE falhas_operacionais SET situacao = ? WHERE id_falha = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $situacao, $idFalha);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header('Location: home.php?status=' . $statusFiltro . '#falhas');
    exit();
}

// A função em clima.php consulta a API do Open-Meteo.
require_once "../../infra/apis/clima.php";
$dadosClima = consultarClima();

$iconesClima = [
    'Céu limpo' => '☀️',
    'Predominantemente limpo' => '🌤️',
    'Parcialmente nublado' => '⛅',
    'Nublado' => '☁️',
    'Neblina' => '🌫️',
    'Garoa leve' => '🌦️',
    'Garoa moderada' => '🌧️',
    'Garoa intensa' => '🌧️',
    'Chuva leve' => '🌦️',
    'Chuva moderada' => '🌧️',
    'Chuva intensa' => '🌧️',
    'Pancadas de chuva leves' => '🌦️',
    'Pancadas de chuva moderadas' => '🌧️',
    'Pancadas de chuva intensas' => '🌧️',
    'Trovoadas' => '⛈️',
    'Trovoadas com granizo' => '⛈️',
    'Trovoadas com granizo intenso' => '⛈️'
];

$iconeClima = '☁️';

if (isset($dadosClima['condicao'])) {
    $condicao = $dadosClima['condicao'];

    if (isset($iconesClima[$condicao])) {
        $iconeClima = $iconesClima[$condicao];
    }
}

$sql = "SELECT id_usuario, nome, email, tipo FROM usuarios ORDER BY nome";
$usuarios = mysqli_query($conn, $sql);
$totalUsuarios = mysqli_num_rows($usuarios);

$sql = "SELECT id_sensor, nome_sensor, tipo_dado, id_rotas
        FROM sensores ORDER BY id_sensor DESC";
$sensores = mysqli_query($conn, $sql);
$totalSensores = mysqli_num_rows($sensores);

$sql = "SELECT nome, velocidade_media FROM trens ORDER BY id_trem";
$trens = mysqli_query($conn, $sql);
$totalTrens = mysqli_num_rows($trens);

$sql = "SELECT id_rotas FROM rotas";
$rotas = mysqli_query($conn, $sql);
$totalRotas = mysqli_num_rows($rotas);

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Resolvida'";
$resultado = mysqli_query($conn, $sql);
$totalResolvidas = mysqli_num_rows($resultado);

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Não resolvida'";
$resultado = mysqli_query($conn, $sql);
$totalNaoResolvidas = mysqli_num_rows($resultado);

$sql = "SELECT * FROM falhas_operacionais";

if ($statusFiltro === 'resolvidas') {
    $sql .= " WHERE situacao = 'Resolvida'";
} elseif ($statusFiltro === 'nao_resolvidas') {
    $sql .= " WHERE situacao = 'Não resolvida'";
}

$sql .= " ORDER BY data_registro DESC";
$falhas = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=nomes-underscore-1">
</head>

<body>
    <!-- Menu no topo da Home. -->
    <nav class="home_barra" aria-label="Navegação do TremTech">
        <a class="home_logo" href="home.php">
            <img class="home_imagem_trem" src="https://img.icons8.com/ios/50/701015/train.png" alt="">
            SISTEMA TREMTECH
        </a>

        <a class="home_link" href="../Tela_Home/home.php">Home</a>
        <a class="home_link" href="../Tela_Usuario/cadastrar_usuario.php">Usuários</a>
        <a class="home_link" href="../Tela_Sensores/cadastrar_sensores.php">Sensores</a>
        <a class="home_link" href="../Tela_Trens/cadastrar_trens.php">Trens</a>
        <a class="home_link" href="../Tela_Rotas/cadastrar_rotas.php">Rotas</a>
        <a class="home_link" href="../Tela_Monitoramento/monitoramento.php">Monitoramento</a>
        <a class="home_link" href="../Tela_Falhas/cadastrar_falha.php">Falhas</a>
        <a class="home_link" href="../Tela_Relatorios/relatorios.php">Relatórios</a>
        <a class="home_logout_link" href="../../logout.php" id="link_sair">Sair</a>
    </nav>

    <main class="home_conteudo">
        <section class="home_boas_vindas">
            <div>
                <p>Bem-vindo de volta!</p>
                <h1>Monitore usuários, rotas e operações em um só lugar.</h1>
                <div class="home_usuario home_conta">
                    <div class="home_bolinha"></div>
                    <div class="home_nome_usuario">
                        <strong><?php echo escapar($nome); ?></strong><br>
                        <?php echo escapar($tipo); ?>
                    </div>
                </div>
            </div>

            <div class="home_status">
                <div class="home_status_titulo" id="velocidade_titulo">Velocidade média</div>
                <div class="home_status_valor" id="velocidade_valor">Nenhum trem cadastrado</div>
            </div>

            <!-- O PHP coloca os nomes e as velocidades nesta lista escondida. O JavaScript lê a lista para trocar o trem do cartão. -->
            <ul id="velocidades_trens" hidden>
                <?php while ($trem = mysqli_fetch_assoc($trens)) { ?>
                    <li>
                        <span><?php echo escapar($trem['nome']); ?></span>
                        <span><?php echo escapar($trem['velocidade_media']); ?> km/h</span>
                    </li>
                <?php } ?>
            </ul>
        </section>

        <section class="home_sobre">
            <h2>Sobre o Ferrorama</h2>
            <p>
                O Ferrorama é um sistema de monitoramento ferroviário
                desenvolvido para facilitar o gerenciamento de trens,
                rotas, sensores e falhas operacionais.
            </p>
        </section>

        <!-- Totais dos cadastros consultados no banco no começo do arquivo. -->
        <section class="home_indicadores">
            <div class="home_indicador">
                <span class="home_indicador_icone"><img src="https://img.icons8.com/ios-filled/50/701015/user.png" alt=""></span>
                <div>
                    <div class="home_indicador_nome">Usuários cadastrados</div>
                    <div class="home_indicador_numero"><?php echo $totalUsuarios; ?></div>
                </div>
            </div>

            <div class="home_indicador">
                <span class="home_indicador_icone"><img src="https://img.icons8.com/ios-filled/50/701015/map.png" alt=""></span>
                <div>
                    <div class="home_indicador_nome">Rotas cadastradas</div>
                    <div class="home_indicador_numero"><?php echo $totalRotas; ?></div>
                </div>
            </div>

            <div class="home_indicador">
                <span class="home_indicador_icone"><img src="https://img.icons8.com/ios-filled/50/701015/radio-waves.png" alt=""></span>
                <div>
                    <div class="home_indicador_nome">Sensores cadastrados</div>
                    <div class="home_indicador_numero"><?php echo $totalSensores; ?></div>
                </div>
            </div>

            <div class="home_indicador">
                <span class="home_indicador_icone"><img src="https://img.icons8.com/ios-filled/50/701015/train.png" alt=""></span>
                <div>
                    <div class="home_indicador_nome">Trens cadastrados</div>
                    <div class="home_indicador_numero"><?php echo $totalTrens; ?></div>
                </div>
            </div>
        </section>

        <section class="home_atalhos">
            <h2>Acesso rápido</h2>
            <p class="home_atalhos_descricao">Escolha uma área para consultar ou gerenciar as informações do sistema.</p>

            <div class="home_atalhos_lista">
                <?php if ($ehAdministrador) { ?>
                    <a class="home_atalho" href="../Tela_Usuario/cadastrar_usuario.php"><strong>Usuários</strong><span>Organize as contas de acesso</span></a>
                    <a class="home_atalho" href="../Tela_Sensores/cadastrar_sensores.php"><strong>Sensores</strong><span>Cadastre e consulte sensores das rotas</span></a>
                    <a class="home_atalho" href="../Tela_Trens/cadastrar_trens.php"><strong>Trens</strong><span>Gerencie os trens e responsáveis</span></a>
                    <a class="home_atalho" href="../Tela_Rotas/cadastrar_rotas.php"><strong>Rotas</strong><span>Organize percursos e trens vinculados</span></a>
                    <a class="home_atalho" href="../Tela_Falhas/cadastrar_falha.php"><strong>Falhas</strong><span>Registre e acompanhe ocorrências</span></a>
                <?php } else { ?>
                    <a class="home_atalho" href="../Tela_Usuario/cadastrar_usuario.php"><strong>Usuários</strong><span>Consulte as contas cadastradas</span></a>
                    <a class="home_atalho" href="../Tela_Sensores/cadastrar_sensores.php"><strong>Sensores</strong><span>Consulte os sensores das rotas</span></a>
                    <a class="home_atalho" href="../Tela_Trens/cadastrar_trens.php"><strong>Trens</strong><span>Consulte trens e responsáveis</span></a>
                    <a class="home_atalho" href="../Tela_Rotas/cadastrar_rotas.php"><strong>Rotas</strong><span>Consulte percursos cadastrados</span></a>
                    <a class="home_atalho" href="../Tela_Falhas/cadastrar_falha.php#falhas"><strong>Falhas</strong><span>Acompanhe ocorrências registradas</span></a>
                <?php } ?>

                <a class="home_atalho" href="../Tela_Monitoramento/monitoramento.php"><strong>Monitoramento</strong><span>Acompanhe informações dos trens</span></a>
                <a class="home_atalho home_atalho_destaque" href="../Tela_Relatorios/relatorios.php"><strong>Relatórios</strong><span>Consulte falhas por sensor e período</span></a>
            </div>
        </section>

        <section class="home_caixa_clima">
            <h2>Clima em Joinville - SC</h2>

            <?php if (isset($dadosClima['erro'])) { ?>
                <p class="clima_erro" role="alert"><?php echo escapar($dadosClima['erro']); ?></p>
            <?php } else { ?>
                <div class="clima_resumo">
                    <div class="clima_icone" aria-hidden="true"><?php echo $iconeClima; ?></div>
                    <div class="clima_resumo_texto">
                        <span><?php echo escapar($dadosClima['condicao']); ?></span>
                        <strong><?php echo escapar($dadosClima['temperatura']); ?>°C</strong>
                        <small>Temperatura atual</small>
                    </div>
                </div>

                <div class="clima_detalhes">
                    <div class="clima_dado">
                        <span>Sensação térmica</span>
                        <strong><?php echo escapar($dadosClima['sensacao']); ?>°C</strong>
                    </div>
                    <div class="clima_dado">
                        <span>Umidade</span>
                        <strong><?php echo escapar($dadosClima['umidade']); ?>%</strong>
                    </div>
                    <div class="clima_dado">
                        <span>Vento</span>
                        <strong><?php echo escapar($dadosClima['vento']); ?> km/h</strong>
                    </div>
                    <div class="clima_dado">
                        <span>Precipitação</span>
                        <strong><?php echo escapar($dadosClima['precipitacao']); ?> mm</strong>
                    </div>
                </div>

                <p class="clima_atualizado">
                    Atualizado: <?php echo escapar($dadosClima['atualizado']); ?>
                </p>
            <?php } ?>

            <small>Fonte: Open-Meteo | Localização: Joinville, SC</small>
        </section>

        <!-- O JavaScript consulta o ViaCEP e preenche os campos de endereço deste bloco. -->
        <section class="home_caixa_cep">
            <h2>Consultar endereço por CEP</h2>

            <form id="form_cep" class="home_cep_form">
                <label for="cep">CEP brasileiro</label>
                <input type="text" id="cep" name="cep" placeholder="Ex.: 89201000"
                       maxlength="8" inputmode="numeric" required>
                <button type="submit">Consultar CEP</button>
            </form>

            <p id="mensagem_cep" role="status"></p>

            <div class="home_cep_campos">
                <div class="home_cep_campo">
                    <label for="logradouro">Logradouro</label>
                    <input type="text" id="logradouro" readonly>
                </div>
                <div class="home_cep_campo">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" readonly>
                </div>
                <div class="home_cep_campo">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" readonly>
                </div>
                <div class="home_cep_campo">
                    <label for="uf">Estado (UF)</label>
                    <input type="text" id="uf" readonly>
                </div>
            </div>

            <small>Fonte: ViaCEP</small>
        </section>

        <section class="home_caixa_falhas" id="falhas">
            <div class="falhas_cabecalho">
                <h2 class="home_titulo_tabela">Falhas operacionais</h2>

                <form action="home.php#falhas" method="GET" class="falhas_filtro">
                    <label for="status">Situação</label>
                    <select id="status" name="status">
                        <option value="todas">Todas</option>
                        <option value="resolvidas"
                            <?php if ($statusFiltro === 'resolvidas') { echo 'selected'; } ?>>
                            Resolvidas
                        </option>
                        <option value="nao_resolvidas"
                            <?php if ($statusFiltro === 'nao_resolvidas') { echo 'selected'; } ?>>
                            Não resolvidas
                        </option>
                    </select>
                    <button type="submit">Filtrar</button>
                </form>
            </div>

            <div class="falhas_resumo">
                <div class="falha_contador">
                    <strong><?php echo $totalResolvidas; ?></strong>
                    <span>Resolvidas</span>
                </div>
                <div class="falha_contador">
                    <strong><?php echo $totalNaoResolvidas; ?></strong>
                    <span>Não resolvidas</span>
                </div>
            </div>

            <div class="falhas_lista">
                <?php if (mysqli_num_rows($falhas) === 0) { ?>
                    <p class="falhas_vazias">Nenhuma falha encontrada.</p>
                <?php } ?>

                <?php
                while ($falha = mysqli_fetch_assoc($falhas)) {
                    // A falha tem o ID do sensor. Com ele encontramos a rota e, pela rota, chegamos ao trem.
                    $idSensor = (int) $falha['id_sensor'];
                    $sqlSensor = "SELECT nome_sensor, id_rotas FROM sensores WHERE id_sensor = $idSensor";
                    $resultadoSensor = mysqli_query($conn, $sqlSensor);
                    $sensorDaFalha = mysqli_fetch_assoc($resultadoSensor);

                    $idRota = (int) $sensorDaFalha['id_rotas'];
                    $sqlRota = "SELECT id_trem FROM rotas WHERE id_rotas = $idRota";
                    $resultadoRota = mysqli_query($conn, $sqlRota);
                    $rotaDaFalha = mysqli_fetch_assoc($resultadoRota);

                    $idTrem = (int) $rotaDaFalha['id_trem'];
                    $sqlTrem = "SELECT nome FROM trens WHERE id_trem = $idTrem";
                    $resultadoTrem = mysqli_query($conn, $sqlTrem);
                    $tremDaFalha = mysqli_fetch_assoc($resultadoTrem);

                    // Mostra a data como dia/mês/ano.
                    $data = date('d/m/Y', strtotime($falha['data_registro']));
                ?>
                    <article class="falha_item">
                        <div class="falha_dados">
                            <h3><?php echo escapar($falha['descricao']); ?></h3>
                            <p>
                                Sensor: <?php echo escapar($sensorDaFalha['nome_sensor']); ?>
                                - Trem: <?php echo escapar($tremDaFalha['nome']); ?>
                                - <?php echo $data; ?>
                            </p>
                        </div>

                        <?php if ($ehAdministrador) { ?>
                            <form method="POST" class="falha_acao">
                                <!-- Este campo indica qual falha o botão deve atualizar. -->
                                <input type="hidden" name="id_falha" value="<?php echo $falha['id_falha']; ?>">

                                <?php if ($falha['situacao'] === 'Resolvida') { ?>
                                    <button type="submit" name="reabrir" class="botao_reabrir">Reabrir</button>
                                <?php } else { ?>
                                    <button type="submit" name="resolver" class="botao_resolver">Marcar como resolvida</button>
                                <?php } ?>
                            </form>
                        <?php } ?>
                    </article>
                <?php } ?>
            </div>
        </section>

        <section class="home_caixa_sensores">
            <h2 class="home_titulo_tabela">Usuários cadastrados</h2>

            <table class="home_tabela">
                <thead>
                    <tr>
                        <th>ID Usuário</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalUsuarios === 0) { ?>
                        <tr><td colspan="4">Nenhum usuário cadastrado.</td></tr>
                    <?php } ?>

                    <?php while ($usuario = mysqli_fetch_assoc($usuarios)) { ?>
                        <tr>
                            <td><?php echo $usuario['id_usuario']; ?></td>
                            <td><?php echo escapar($usuario['nome']); ?></td>
                            <td><?php echo escapar($usuario['email']); ?></td>
                            <td><?php echo escapar($usuario['tipo']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <section class="home_caixa_sensores">
            <h2 class="home_titulo_tabela">Sensores cadastrados</h2>

            <div class="home_tabela_sensores">
                <table class="home_tabela">
                    <thead>
                        <tr>
                            <th>ID Sensor</th>
                            <th>Nome</th>
                            <th>Tipo de dado</th>
                            <th>Rota vinculada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalSensores === 0) { ?>
                            <tr><td colspan="4">Nenhum sensor cadastrado.</td></tr>
                        <?php } ?>

                        <?php
                        while ($sensor = mysqli_fetch_assoc($sensores)) {
                            $idRota = (int) $sensor['id_rotas'];
                            $sqlRota = "SELECT nome FROM rotas WHERE id_rotas = $idRota";
                            $resultadoRota = mysqli_query($conn, $sqlRota);
                            $rota = mysqli_fetch_assoc($resultadoRota);
                        ?>
                            <tr>
                                <td><?php echo $sensor['id_sensor']; ?></td>
                                <td><?php echo escapar($sensor['nome_sensor']); ?></td>
                                <td><?php echo escapar($sensor['tipo_dado']); ?></td>
                                <td><?php echo escapar($rota['nome']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="home_rodape">
        <p>Ferrorama - Sistema de Monitoramento Ferroviário</p>
        <p>2026 TremTech</p>
    </footer>

    <!-- A janela fica fechada até a pessoa clicar em Sair. -->
    <dialog class="modal_confirmacao" id="janela_saida" aria-labelledby="tituloConfirmarSaida">
        <div class="modal_confirmacao_icone" aria-hidden="true">?</div>
        <h2 id="tituloConfirmarSaida">Sair do sistema?</h2>
        <p class="modal_confirmacao_mensagem">Tem certeza de que deseja sair da sua conta?</p>
        <div class="modal_confirmacao_acoes">
            <button type="button" class="modal_btn_cancelar" id="cancelar_saida">Continuar no sistema</button>
            <button type="button" class="modal_btn_sair" id="confirmar_saida">Sair</button>
        </div>
    </dialog>

<script src="../../script/validacao.js?v=simples-8"></script>
</body>
</html>