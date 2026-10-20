<?php

function mostrar_sensores($resultado): void
{
    $ehAdministrador =
        strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

    if ($resultado === false) {
        echo '<p>Não foi possível carregar os sensores.</p>';
        return;
    }

    if ($resultado->num_rows === 0) {
        echo '<p class="mensagem_vazia">Nenhum sensor encontrado.</p>';
        return;
    }
    ?>

    <table class="tabela-sensores">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do sensor</th>
                <th>Trem vinculado</th>
                <th>Direção</th>
                <th>Temperatura</th>
                <th>Velocidade</th>

                <?php if ($ehAdministrador): ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php while ($sensor = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $sensor['id_sensor'] ?></td>
                    <td><?= escapar($sensor['nome']) ?></td>
                    <td><?= escapar($sensor['trem_vinculado']) ?></td>
                    <td><?= escapar($sensor['direcao']) ?></td>
                    <td><?= escapar($sensor['temperatura']) ?></td>
                    <td><?= escapar($sensor['velocidade']) ?></td>

            <?php if ($ehAdministrador): ?>
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
        </tbody>
    </table>

    <?php
}
?>