<?php
// CADASTRAR USUÁRIO. O formulário envia os dados pelo método POST.
// O PHP valida e salva. A consulta da lista vai para o arquivo visualizacao_usuario.php.
require_once '../../infra/conexao.php';
require_once '../../infra/auth.php';

// Quem fez login pode ver a tabela. Somente administradores podem enviar o cadastro.
exigir_login();

$administrador = $_SESSION['tipo'] === 'Administrador';
$mensagem = $_SESSION['mensagem_usuario'] ?? '';
// A sessão guarda o recado até a página abrir. Depois, o recado é limpo.
$sucesso = $_SESSION['sucesso_usuario'] ?? false;
unset($_SESSION['mensagem_usuario'], $_SESSION['sucesso_usuario']);

// Cadastro. Recebe o formulário e faz o INSERT no banco.
// O link Editar manda o ID pelo endereço da página.
$editando = isset($_GET['editar']);

if (!$editando && $_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_administrador();

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if ($nome === '' || mb_strlen($nome) > 100) {
        $mensagem = 'Informe um nome de até 100 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $mensagem = 'Informe um e-mail válido de até 100 caracteres.';
    } elseif ($senha === '') {
        $mensagem = 'Informe uma senha.';
    } elseif ($tipo !== 'Administrador' && $tipo !== 'Funcionário') {
        $mensagem = 'Selecione um tipo de usuário válido.';
    } else {
        // Salva uma versão protegida da senha, chamada hash. O login compara a senha digitada com esse valor.
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $nome, $email, $senha_hash, $tipo);

        // Se o banco recusar o cadastro, por exemplo por e-mail repetido, o PHP mostra um aviso.
        try {
            $salvou = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $erro) {
            $salvou = false;
        }

        mysqli_stmt_close($stmt);

        if ($salvou) {
            $_SESSION['mensagem_usuario'] = 'Usuário cadastrado com sucesso!';
            $_SESSION['sucesso_usuario'] = true;
            header('Location: cadastrar_usuario.php');
            exit();
        }

        $mensagem = 'Não foi possível cadastrar. Confira se o e-mail já está em uso.';
    }
}

// Listagem. Busca os dados para a tabela.
$busca = trim($_GET['buscar'] ?? '');

// O % permite encontrar o texto em qualquer parte do nome ou e-mail.
$texto = '%' . $busca . '%';

$sql = "SELECT id_usuario, nome, email, tipo FROM usuarios
        WHERE nome LIKE ? OR email LIKE ?
        ORDER BY nome";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $texto, $texto);
mysqli_stmt_execute($stmt);
$usuarios = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
// Se o cadastro tiver erro, o formulário continua aberto para a pessoa ver o aviso.
// Busca o registro para preencher o formulário ou salva as alterações antes do HTML.
if ($editando) {
    $sucesso = false;
    require 'editar_u.php';
}

$cadastro_aberto = $_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem !== '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=menu-direto-topo-1">
</head>

<body class="body_geral">
    <!-- A classe ativo destaca o link da tela aberta. -->
    <nav class="menu_topo" aria-label="Navegação do TremTech">
        <a class="menu_topo_marca" href="../Tela_Home/home.php">
            <img src="https://img.icons8.com/ios/100/701015/train.png" alt="">
            <span>SISTEMA TREMTECH</span>
        </a>

        <div class="menu_topo_links">
            <a href="../Tela_Home/home.php"><img src="https://img.icons8.com/ios-filled/18/701015/home.png" alt=""> Home</a>
            <a class="ativo" href="../Tela_Usuario/cadastrar_usuario.php"><img src="https://img.icons8.com/ios-filled/18/701015/user.png" alt=""> Usuários</a>
            <a href="../Tela_Sensores/cadastrar_sensores.php"><img src="https://img.icons8.com/ios-filled/18/701015/radio-waves.png" alt=""> Sensores</a>
            <a href="../Tela_Trens/cadastrar_trens.php"><img src="https://img.icons8.com/ios-filled/18/701015/train.png" alt=""> Trens</a>
            <a href="../Tela_Rotas/cadastrar_rotas.php"><img src="https://img.icons8.com/ios-filled/18/701015/map.png" alt=""> Rotas</a>
            <a href="../Tela_Monitoramento/monitoramento.php"><img src="https://img.icons8.com/ios-filled/18/701015/visible.png" alt=""> Monitoramento</a>
            <a href="../Tela_Falhas/cadastrar_falha.php"><img src="https://img.icons8.com/ios-filled/18/701015/error.png" alt=""> Falhas</a>
            <a href="../Tela_Relatorios/relatorios.php"><img src="https://img.icons8.com/ios-filled/18/701015/report-card.png" alt=""> Relatórios</a>
        </div>

        <a class="menu_topo_sair" href="../../logout.php" id="link_sair"><img src="https://img.icons8.com/ios-filled/18/701015/logout-rounded.png" alt=""> Sair</a>
    </nav>
    <div class="pagina">


        <main class="content_user">
            <p class="painel_user">PAINEL ADMINISTRATIVO</p>
            <h1>Controle de usuários cadastrados</h1>
            <h2>Administradores e Funcionários</h2>

            <div class="admin_badge_user card_recuado">
                <div class="avatar_user"></div>
                <div>
                    <strong><?php echo escapar($_SESSION['tipo']); ?></strong>
                    <span><?php echo escapar($_SESSION['nome']); ?></span>
                </div>
            </div>

            <?php if ($mensagem !== '') { ?>
                <?php if ($sucesso) { ?>
                    <p class="mensagem_sucesso"><?php echo escapar($mensagem); ?></p>
                <?php } else { ?>
                    <p class="mensagem_erro" role="alert"><?php echo escapar($mensagem); ?></p>
                <?php } ?>
            <?php } ?>

            <?php if ($editando) { ?>
                <section id="cadastro" class="cadastro_usuario ativo">
                    <h2>Editar usuário</h2>
                    <!-- Cada campo junta o rótulo e a caixa de entrada. O CSS usa esse grupo para colocar o rótulo em cima. -->
                    <form method="POST" class="form_cadastro_user">
                        <div class="campo_user">
                            <label for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" maxlength="100"
                                   value="<?php echo escapar($nome); ?>" required>
                        </div>

                        <div class="campo_user">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" maxlength="100"
                                   value="<?php echo escapar($email); ?>" required>
                        </div>

                        <div class="campo_user">
                            <label for="senha">Nova senha</label>
                            <input type="password" id="senha" name="senha"
                                   placeholder="Deixe em branco para manter a senha">
                        </div>

                        <div class="campo_user">
                            <label for="tipo">Tipo de usuário</label>
                            <select id="tipo" name="tipo">
                                <option <?php if ($tipo === 'Funcionário') { echo 'selected'; } ?>>Funcionário</option>
                                <option <?php if ($tipo === 'Administrador') { echo 'selected'; } ?>>Administrador</option>
                            </select>
                        </div>

                        <div class="acoes_form_edicao modo_edicao">
                            <button class="btn_cadastrar_form" type="submit">Salvar</button>
                            <a class="btn_cancelar_form" href="cadastrar_usuario.php">Cancelar</a>
                        </div>
                    </form>
                </section>
            <?php } elseif ($administrador) { ?>
                <section id="cadastro" class="cadastro_usuario ativo"
                    <?php if (!$cadastro_aberto) { echo 'hidden'; } ?>>
                    <h2>Cadastrar novo usuário</h2>

                    <form method="POST" class="form_cadastro_user">
                        <div class="campo_user">
                            <label for="nome">Nome de usuário</label>
                            <input type="text" id="nome" name="nome" placeholder="Nome completo" maxlength="100" required>
                        </div>
                        <div class="campo_user">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Email" maxlength="100" required>
                        </div>
                        <div class="campo_user">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" placeholder="Senha" required>
                        </div>

                        <div class="campo_user">
                            <label for="tipo">Tipo de usuário</label>
                            <select id="tipo" name="tipo">
                                <option>Funcionário</option>
                                <option>Administrador</option>
                            </select>
                        </div>

                        <button class="btn_cadastrar_form" type="submit">CADASTRAR NOVO USUÁRIO</button>
                    </form>
                </section>
            <?php } ?>

            <!-- Nesta área, Buscar filtra a lista e o botão Cadastrar abre o formulário. -->
            <section class="buscar_box_user buscar_usuarios_linha">
                <form method="GET">
                    <label for="buscar">Buscar</label>
                    <input type="search" id="buscar" name="buscar" placeholder="Nome ou email"
                           value="<?php echo escapar($busca); ?>">
                    <button type="submit">Buscar</button>
                </form>

                <?php if ($administrador && !$editando) { ?>
                    <button class="btn_cadastrar_user btn_cadastrar_user_compacto"
                            id="botao_abrir_cadastro" type="button" aria-controls="cadastro">
                        Cadastrar usuário
                    </button>
                <?php } ?>
            </section>

            <?php
            if (mysqli_num_rows($usuarios) > 0) {
                include 'visualizacao_usuario.php';
            } else {
                echo '<p>Nenhum usuário encontrado.</p>';
            }
            ?>
        </main>
    </div>

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

    <!-- A janela só abre ao clicar na lixeira. Os botões usam o mesmo estilo da confirmação de saída. -->
    <dialog class="modal_confirmacao" id="janela_exclusao" aria-labelledby="titulo_exclusao">
        <div class="modal_confirmacao_icone" aria-hidden="true">?</div>
        <h2 id="titulo_exclusao">Excluir registro?</h2>
        <p class="modal_confirmacao_mensagem">Tem certeza de que deseja excluir este registro?</p>
        <div class="modal_confirmacao_acoes">
            <button type="button" class="modal_btn_cancelar" id="cancelar_exclusao">Cancelar</button>
            <button type="button" class="modal_btn_sair" id="confirmar_exclusao">Excluir</button>
        </div>
    </dialog>

<script src="../../script/validacao.js?v=simples-8"></script>
</body>
</html>