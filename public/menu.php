<?php
// A pasta indica qual opção fica destacada, inclusive nas páginas de edição.
$telaAtual = basename(dirname($_SERVER['SCRIPT_NAME']));
$opcoesMenu = [
    ['Tela_Home', 'home.php', 'Home', 'home'],
    ['Tela_Usuario', 'cadastrar_usuario.php', 'Usuários', 'user'],
    ['Tela_Sensores', 'cadastrar_sensores.php', 'Sensores', 'radio-waves'],
    ['Tela_Trens', 'cadastrar_trens.php', 'Trens', 'train'],
    ['Tela_Rotas', 'cadastrar_rotas.php', 'Rotas', 'map'],
    ['Tela_Monitoramento', 'monitoramento.php', 'Monitoramento', 'visible'],
    ['Tela_Falhas', 'cadastrar_falha.php', 'Falhas', 'error'],
    ['Tela_Relatorios', 'relatorios.php', 'Relatórios', 'report-card']
];
?>
<nav class="menu-topo" aria-label="Navegação do TremTech">
    <a class="menu-topo-marca" href="../Tela_Home/home.php">
        <img src="https://img.icons8.com/ios/100/701015/train.png" alt="">
        <span>SISTEMA TREMTECH</span>
    </a>

    <!-- A mesma lista de links aparece em todas as páginas internas. -->
    <div class="menu-topo-links">
        <?php foreach ($opcoesMenu as $opcao) { ?>
            <a href="../<?php echo $opcao[0]; ?>/<?php echo $opcao[1]; ?>"
                <?php if ($telaAtual === $opcao[0]) { echo 'class="ativo"'; } ?>>
                <img src="https://img.icons8.com/ios-filled/22/701015/<?php echo $opcao[3]; ?>.png" alt="">
                <?php echo $opcao[2]; ?>
            </a>
        <?php } ?>
    </div>

    <!-- O atributo data-confirmar-logout indica ao JavaScript qual link deve pedir confirmação. -->
    <a class="menu-topo-sair" href="../../logout.php" data-confirmar-logout>
        <img src="https://img.icons8.com/ios-filled/22/701015/logout-rounded.png" alt="">
        Sair
    </a>
</nav>
