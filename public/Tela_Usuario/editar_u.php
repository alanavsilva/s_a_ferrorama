<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_administrador();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Usuário inválido.'); }

$stmt = $conn->prepare('SELECT id_usuario, nome, email, tipo FROM usuarios WHERE id_usuario = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
if (!$usuario) { http_response_code(404); exit('Usuário não encontrado.'); }

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Informe um nome e um e-mail válido.';
    } elseif (!in_array($tipo, ['Funcionário', 'Administrador'], true)) {
        $mensagem = 'Tipo de usuário inválido.';
    } elseif ($senha !== '' && strlen($senha) < 8) {
        $mensagem = 'A nova senha deve ter pelo menos 8 caracteres.';
    } else {
        if ($senha !== '') {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ?, senha = ?, tipo = ? WHERE id_usuario = ?');
            $stmt->bind_param('ssssi', $nome, $email, $hash, $tipo, $id);
        } else {
            $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id_usuario = ?');
            $stmt->bind_param('sssi', $nome, $email, $tipo, $id);
        }
        try {
            if ($stmt->execute()) {
                $_SESSION['mensagem_cadastro'] = 'Usuário atualizado com sucesso!';
                header('Location: cadastrar_usuario.php');
                exit;
            }
        } catch (mysqli_sql_exception $erro) {
            if ((int)$erro->getCode() === 1062) $mensagem = 'Este e-mail já está cadastrado.';
            else { error_log('Erro ao atualizar usuário: ' . $erro->getMessage()); $mensagem = 'Não foi possível atualizar o usuário.'; }
        }
        if ($mensagem === '') $mensagem = 'Não foi possível atualizar o usuário.';
    }
    $usuario['nome'] = $nome;
    $usuario['email'] = $email;
    $usuario['tipo'] = $tipo;
}
$nomeUsuario = escapar((string)($_SESSION['nome'] ?? 'Administrador'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuário | TremTech</title>
    <link rel="stylesheet" href="../../assets/style/style.css?v=20261021-edicao">
</head>
<body class="body_geral pagina-edicao">
<div class="pagina">
    <aside class="menu">
        <img class="logo-trem" src="https://img.icons8.com/ios/100/ffffff/train.png" alt="Logo TremTech">
        <p class="marca">SISTEMA TREMTECH</p>
        <a href="../../public/Tela_Home/home.php">⌂ &nbsp; Home</a>
        <a class="ativo" href="cadastrar_usuario.php">♟ &nbsp; Usuários</a>
        <a href="../../public/Tela_Sensores/cadastrar_sensores.php">◉ &nbsp; Sensores</a>
        <a href="../../public/Tela_Trens/cadastrar_trens.php">▰ &nbsp; Trem</a>
        <a href="../../public/Tela_Rotas/cadastrar_rotas.php">▥ &nbsp; Rota</a>
        <a href="../../public/Tela_Monitoramento/monitoramento.php">◉ &nbsp; Monitoramento</a>
        <a href="../../public/Tela_Relatorios/relatorios.php">▣ &nbsp; Relatórios</a>
        <a class="logout" href="../../logout.php">⇥ Logout</a>
    </aside>
    <main class="content-user">
        <p class="painel-user">PAINEL ADMINISTRATIVO</p>
        <h1>Controle de usuários cadastrados</h1>
        <h2>Atualize os dados do usuário</h2>
        <div class="admin-badge-user"><div class="avatar-user"></div><div><strong>Administrador</strong><span><?= $nomeUsuario ?></span></div></div>

        <a class="link-voltar-edicao" href="cadastrar_usuario.php">← Voltar para usuários</a>
        <section class="cadastro-usuario ativo formulario-edicao">
            <h2>Editar usuário</h2>
            <?php if ($mensagem !== ''): ?><p class="mensagem-cadastro" role="alert"><?= escapar($mensagem) ?></p><?php endif; ?>
            <form class="form-cadastro-user" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
                <input type="hidden" name="id_usuario" value="<?= (int)$id ?>">
                <div class="campo-user"><label for="nome">Nome de usuário</label><input type="text" id="nome" name="nome" maxlength="100" required value="<?= escapar($usuario['nome']) ?>"></div>
                <div class="campo-user"><label for="email">E-mail</label><input type="email" id="email" name="email" maxlength="100" required value="<?= escapar($usuario['email']) ?>"></div>
                <div class="campo-user"><label for="senha">Nova senha (opcional)</label><input type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" placeholder="Deixe em branco para manter a senha atual"><small class="ajuda-edicao">A senha atual será mantida se este campo ficar vazio.</small></div>
                <div class="campo-user"><label for="tipo">Tipo de usuário</label><select id="tipo" name="tipo" required>
                    <option value="Funcionário" <?= $usuario['tipo'] === 'Funcionário' ? 'selected' : '' ?>>Funcionário</option>
                    <option value="Administrador" <?= $usuario['tipo'] === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                </select></div>
                <div class="acoes-form-edicao"><button type="submit" class="btn-cadastrar-form">SALVAR ALTERAÇÕES</button><a href="cadastrar_usuario.php">Cancelar</a></div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
