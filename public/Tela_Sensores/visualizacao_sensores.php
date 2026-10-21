<?php

function mostrar_sensores($resultado, bool $mostrarAcoes = true): void
{
    $ehAdministrador =
        strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

      $exibirAcoes = $mostrarAcoes && $ehAdministrador;

    if ($resultado === false || $resultado === null) {
        echo '<p>Não foi possível carregar os sensores.</p>';
        return;
    }

    ?>

          <table class="home_tabela">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome do sensor</th>
                <th>Trem vinculado</th>
                <th>Tipo de dado registrado</th>
                <th>Rota vinculada</th>

                <?php if ($exibirAcoes): ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php if ($resultado->num_rows === 0): ?>
                <tr>
                    <td colspan="<?= $exibirAcoes ? 6 : 5 ?>" class="mensagem_vazia">
                        Nenhum sensor encontrado.
                    </td>
                </tr>            <?php else: ?>
            <?php while ($sensor = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $sensor['id_sensor'] ?></td>
                    <td><?= escapar($sensor['nome_sensor']) ?></td>
                    <td><?= escapar($sensor['tipo_dado']) ?></td>
                    <td><?= escapar($sensor['localizacao']) ?></td>
                    <td><?= escapar($sensor['rota_vinculada']) ?></td>

            <?php if ($exibirAcoes): ?>
            <td class="acao_sensor">
            <a href="editar_s.php?id=<?= (int) $sensor['id_sensor'] ?>" class="icon_editar" aria-label="Editar sensor">
            <img src="https://img.icons8.com/ios-filled/18/ffffff/edit.png" alt="" >
            </a>

            <form method="POST" action="excluir_s.php" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir este sensor?');">
            
            <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
            <input type="hidden" name="id" value="<?= (int) $sensor['id_sensor'] ?>">

            <button type="submit" class="icon_excluir" aria-label="Excluir sensor">
            <img src="https://img.icons8.com/ios-filled/18/ffffff/trash.png" alt="">
            </button>

             </form>
                </td>
               <?php endif; ?>
                </tr>
            <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
}
?>