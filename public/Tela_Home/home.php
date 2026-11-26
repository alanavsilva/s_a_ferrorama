<?php
// O PHP carrega os dados da Home e o JavaScript controla a consulta de CEP e o cartão de velocidade.
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

// A página inicial só abre depois que a pessoa faz login.
exigir_login();

$nome = $_SESSION['nome'];
$tipo = $_SESSION['tipo'];
$eh_administrador = ($tipo === 'Administrador');
$status_filtro = 'todas';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'resolvidas' || $_GET['status'] === 'nao_resolvidas') {
        $status_filtro = $_GET['status'];
    }
}

// Quando um administrador clica em Resolver ou Reabrir na Home, este trecho salva a nova situação da falha.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    if (isset($_POST['resolver']) || isset($_POST['reabrir'])) {
        $id_falha = (int) $_POST['id_falha'];

        if (isset($_POST['resolver'])) {
            $situacao = 'Resolvida';
        } else {
            $situacao = 'Não resolvida';
        }

        $sql = "UPDATE falhas_operacionais SET situacao = ? WHERE id_falha = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $situacao, $id_falha);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header('Location: home.php?status=' . $status_filtro . '#falhas');
    exit();
}

// A função em clima.php consulta a API do Open-Meteo.
require_once "../../infra/apis/clima.php";
$dados_clima = consultarClima();

$icones_clima = [
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

$icone_clima = '☁️';

if (isset($dados_clima['condicao'])) {
    $condicao = $dados_clima['condicao'];

    if (isset($icones_clima[$condicao])) {
        $icone_clima = $icones_clima[$condicao];
    }
}

$sql = "SELECT id_usuario, nome, email, tipo FROM usuarios ORDER BY nome";
$usuarios = mysqli_query($conn, $sql);
$total_usuarios = mysqli_num_rows($usuarios);

$sql = "SELECT id_sensor, nome_sensor, tipo_dado, id_rotas
        FROM sensores ORDER BY id_sensor DESC";
$sensores = mysqli_query($conn, $sql);
$total_sensores = mysqli_num_rows($sensores);

$sql = "SELECT nome, velocidade_media FROM trens ORDER BY id_trem";
$trens = mysqli_query($conn, $sql);
$total_trens = mysqli_num_rows($trens);

$sql = "SELECT id_rotas FROM rotas";
$rotas = mysqli_query($conn, $sql);
$total_rotas = mysqli_num_rows($rotas);

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Resolvida'";
$resultado = mysqli_query($conn, $sql);
$total_resolvidas = mysqli_num_rows($resultado);

$sql = "SELECT id_falha FROM falhas_operacionais WHERE situacao = 'Não resolvida'";
$resultado = mysqli_query($conn, $sql);
$total_nao_resolvidas = mysqli_num_rows($resultado);

$sql = "SELECT * FROM falhas_operacionais";

if ($status_filtro === 'resolvidas') {
    $sql .= " WHERE situacao = 'Resolvida'";
} elseif ($status_filtro === 'nao_resolvidas') {
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
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-direto-topo-1">
</head>

<body>
    <!-- A classe ativo destaca o link da tela aberta. -->
    <nav class="menu_topo" aria-label="Navegação do TremTech">
        <a class="menu_topo_marca" href="../Tela_Home/home.php">
            <img src="https://img.icons8.com/ios/100/701015/train.png" alt="">
            <span>SISTEMA TREMTECH</span>
        </a>

        <div class="menu_topo_links">
            <a class="ativo" href="../Tela_Home/home.php"><img src="https://img.icons8.com/ios-filled/18/701015/home.png" alt=""> Home</a>
            <a href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
        </div>

        <a class="menu_topo_sair" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
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
                    <div class="home_indicador_numero"><?php echo $total_usuarios; ?></div>
                </div>
            </div>

            <div class="home_indicador">
                <span class="home_indicador_icone"><img src="https://img.icons8.com/ios-filled/50/701015/map.png" alt=""></span>
                <div>
                    <div class="home_indicador_nome">Rotas cadastradas</div>
                    <div class="home_indicador_numero"><?php echo $total_rotas; ?></div>
                </div>
            </div>

            <div class="home_indicador">
                <span class="home_indicador_icone"><img src="https://img.icons8.com/ios-filled/50/701015/radio-waves.png" alt=""></span>
                <div>
                    <div class="home_indicador_nome">Sensores cadastrados</div>
                    <div class="home_indicador_numero"><?php echo $total_sensores; ?></div>
                </div>
            </div>

            <div class="home_indicador">
                <span class="home_indicador_icone"><img src="https://img.icons8.com/ios-filled/50/701015/train.png" alt=""></span>
                <div>
                    <div class="home_indicador_nome">Trens cadastrados</div>
                    <div class="home_indicador_numero"><?php echo $total_trens; ?></div>
                </div>
            </div>
        </section>

        <section class="home_atalhos">
            <h2>Acesso rápido</h2>
            <p class="home_atalhos_descricao">Escolha uma área para consultar ou gerenciar as informações do sistema.</p>

            <div class="home_atalhos_lista">
                <?php if ($eh_administrador) { ?>
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

            <?php if (isset($dados_clima['erro'])) { ?>
                <p class="clima_erro" role="alert"><?php echo escapar($dados_clima['erro']); ?></p>
            <?php } else { ?>
                <div class="clima_resumo">
                    <div class="clima_icone" aria-hidden="true"><?php echo $icone_clima; ?></div>
                    <div class="clima_resumo_texto">
                        <span><?php echo escapar($dados_clima['condicao']); ?></span>
                        <strong><?php echo escapar($dados_clima['temperatura']); ?>°C</strong>
                        <small>Temperatura atual</small>
                    </div>
                </div>

                <div class="clima_detalhes">
                    <div class="clima_dado">
                        <span>Sensação térmica</span>
                        <strong><?php echo escapar($dados_clima['sensacao']); ?>°C</strong>
                    </div>
                    <div class="clima_dado">
                        <span>Umidade</span>
                        <strong><?php echo escapar($dados_clima['umidade']); ?>%</strong>
                    </div>
                    <div class="clima_dado">
                        <span>Vento</span>
                        <strong><?php echo escapar($dados_clima['vento']); ?> km/h</strong>
                    </div>
                    <div class="clima_dado">
                        <span>Precipitação</span>
                        <strong><?php echo escapar($dados_clima['precipitacao']); ?> mm</strong>
                    </div>
                </div>

                <p class="clima_atualizado">
                    Atualizado: <?php echo escapar($dados_clima['atualizado']); ?>
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
                            <?php if ($status_filtro === 'resolvidas') { echo 'selected'; } ?>>
                            Resolvidas
                        </option>
                        <option value="nao_resolvidas"
                            <?php if ($status_filtro === 'nao_resolvidas') { echo 'selected'; } ?>>
                            Não resolvidas
                        </option>
                    </select>
                    <button type="submit" class="botao_resolver">Filtrar</button>
                </form>
            </div>

            <div class="falhas_resumo">
                <div class="falha_contador">
                    <strong><?php echo $total_resolvidas; ?></strong>
                    <span>Resolvidas</span>
                </div>
                <div class="falha_contador">
                    <strong><?php echo $total_nao_resolvidas; ?></strong>
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
                    $id_sensor = (int) $falha['id_sensor'];
                    $sql_sensor = "SELECT nome_sensor, id_rotas FROM sensores WHERE id_sensor = $id_sensor";
                    $resultado_sensor = mysqli_query($conn, $sql_sensor);
                    $sensor_da_falha = mysqli_fetch_assoc($resultado_sensor);

                    $id_rota = (int) $sensor_da_falha['id_rotas'];
                    $sql_rota = "SELECT id_trem FROM rotas WHERE id_rotas = $id_rota";
                    $resultado_rota = mysqli_query($conn, $sql_rota);
                    $rota_da_falha = mysqli_fetch_assoc($resultado_rota);

                    $id_trem = (int) $rota_da_falha['id_trem'];
                    $sql_trem = "SELECT nome FROM trens WHERE id_trem = $id_trem";
                    $resultado_trem = mysqli_query($conn, $sql_trem);
                    $trem_da_falha = mysqli_fetch_assoc($resultado_trem);

                    // Mostra a data como dia/mês/ano.
                    $data = date('d/m/Y', strtotime($falha['data_registro']));
                ?>
                    <article class="falha_item">
                        <div class="falha_dados">
                            <h3><?php echo escapar($falha['descricao']); ?></h3>
                            <p>
                                Sensor: <?php echo escapar($sensor_da_falha['nome_sensor']); ?>
                                - Trem: <?php echo escapar($trem_da_falha['nome']); ?>
                                - <?php echo $data; ?>
                            </p>
                        </div>

                        <?php if ($eh_administrador) { ?>
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
                    <?php if ($total_usuarios === 0) { ?>
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
                        <?php if ($total_sensores === 0) { ?>
                            <tr><td colspan="4">Nenhum sensor cadastrado.</td></tr>
                        <?php } ?>

                        <?php
                        while ($sensor = mysqli_fetch_assoc($sensores)) {
                            $id_rota = (int) $sensor['id_rotas'];
                            $sql_rota = "SELECT nome FROM rotas WHERE id_rotas = $id_rota";
                            $resultado_rota = mysqli_query($conn, $sql_rota);
                            $rota = mysqli_fetch_assoc($resultado_rota);
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