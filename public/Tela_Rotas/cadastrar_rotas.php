<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Rotas | TremTech</title>

  <link rel="stylesheet" href="../../assets/style/style.css">

</head>

<body class="body_geral">
   <div class="pagina">

    <aside class="menu">
       <img class="logo-trem"
    src="https://img.icons8.com/ios/100/ffffff/train.png"
    alt="Logo TremTech">

  <p class="marca">SISTEMA TREMTECH</p>
     <a href="../../public/Tela_Home/home.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
  Home
</a>

<a href="../../public/Tela_Usuario/cadastrar_usuario.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
  Usuários
</a>

<a href="../../public/Tela_Sensores/cadastrar_sensores.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
  Sensores
</a>

<a href="../../public/Tela_Trens/cadastrar_trens.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/train.png');"></span>
  Trem
</a>

<a class="ativo" href="../../public/Tela_Rotas/cadastrar_rotas.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/map.png');"></span>
  Rota
</a>
<a href="../../public/Tela_Monitoramento/monitoramento.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/visible.png');"></span>
  Monitoramento
</a>

<a href="../../public/Tela_Relatorios/relatorios.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/report-card.png');"></span>
  Relatórios
</a>


<a class="logout" href="../../logout.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/ffffff/logout-rounded.png');"></span>
  Logout
</a>
    </aside>

    <main class="content-user">
        <p class="painel-user">PAINEL ADMINISTRATIVO</p>
        <h1>Controle de rotas cadastradas</h1>
        <h2>Rotas do sistema</h2>

        <?php if ($mensagemSucesso !== ''): ?>
            <p class="mensagem-sucesso" role="status">
                <?= escapar($mensagemSucesso) ?>
            </p>
        <?php endif; ?>

        <?php if ($mensagemAcesso !== ''): ?>
            <p role="alert"><?= escapar($mensagemAcesso) ?></p>
        <?php endif; ?>

        <?php if ($ehAdministrador): ?>
            <button
                class="btn-cadastrar-user"
                id="btnAbrirCadastro"
                type="button"
                aria-expanded="false"
            >
                CADASTRAR NOVA ROTA
            </button>
          
             <section class="cadastro-usuario" id="cadastro" hidden>
                <form class="form-cadastro-user" method="POST">            <div class="campo_inteiro">
              <input type="hidden" name="csrf_token"value="<?= escapar(token_csrf()) ?>">
              
            <div class="campo-user">
                <label for="nome">Nome da rota</label>
              <input type="text" id="nome" name="nome" maxlength="100" placeholder="Nome da rota" required>
            </div>

            <div class="campo-user">
              <label for="extensao">Extensão(km)</label>
              <input type="number" id="extensao" name="extensao" min="0.01" step="0.01" placeholder="Ex:45" required>
            </div>

            <div class="campo-user">
              <label for="minutos">Tempo estimado(min)</label>
              <input type="number" id="minutos" name="tempo" min="1" step="1" placeholder="Ex:30" required>
            </div>
<?php if ($mensagem !== ''): ?>
                        <p class="mensagem-cadastro" role="alert">
                            <?= escapar($mensagem) ?>
                        </p>
                    <?php endif; ?>

                    <button class="btn-cadastrar-form" type="submit">
                        CADASTRAR ROTA
                    </button>
                </form>
            </section>
        <?php endif; ?>

        <section class="buscar-box-user">
            <form method="GET">
                <label for="buscar">Buscar rota</label>
                <input
                    type="search"
                    id="buscar"
                    name="buscar"
                    placeholder="Nome da rota"
                    value="<?= escapar($busca) ?>"
                >
                <button type="submit">Buscar</button>
            </form>
        </section>

        <?php mostrar_rotas($resultado); ?>
    </main>
</div>

<script>
(() => {
    const botao = document.getElementById('btnAbrirCadastro');
    const formulario = document.getElementById('cadastro');

    if (!botao || !formulario) return;

    botao.addEventListener('click', () => {
        formulario.hidden = !formulario.hidden;
        formulario.classList.toggle('ativo', !formulario.hidden);
        botao.setAttribute('aria-expanded', String(!formulario.hidden));
    });
})();
</script>
</body>
</html>
      
