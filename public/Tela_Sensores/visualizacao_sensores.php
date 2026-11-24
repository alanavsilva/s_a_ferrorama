<?php
require_once '../../infra/auth.php';

exigir_login();

$encontrou = false;
?>

<!-- Mostra os registros na tabela e libera as ações para administradores. -->
<table class="tabela-usuarios tabela-sensores">
    <thead>
        <tr>
            <th>Nome do sensor</th>
            <th>Rota vinculada</th>
            <th>Tipo de dado registrado</th>

            <?php if ($ehAdministrador) { ?>
                <th>Ações</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($sensor = mysqli_fetch_assoc($sensores)) {
            // O sensor guarda o ID da rota. O SELECT busca o nome dela.
            $idRota = (int) $sensor['id_rotas'];
            $sql = "SELECT nome FROM rotas WHERE id_rotas = $idRota";
            $resultado = mysqli_query($conn, $sql);
            $rota = mysqli_fetch_assoc($resultado);

            // stripos procura o texto sem diferenciar maiúsculas e minúsculas.
            if ($busca === ''
                || stripos($sensor['nome_sensor'], $busca) !== false
                || stripos($sensor['tipo_dado'], $busca) !== false
                || stripos($rota['nome'], $busca) !== false) {
                $encontrou = true;
        ?>
                <tr>
                    <td><?php echo escapar($sensor['nome_sensor']); ?></td>
                    <td><?php echo escapar($rota['nome']); ?></td>
                    <td><?php echo escapar($sensor['tipo_dado']); ?></td>

                    <?php if ($ehAdministrador) { ?>
                        <td class="acao_user acao_sensor">
                            <a href="editar_s.php?id=<?php echo $sensor['id_sensor']; ?>" class="acao-icone editar-user" aria-label="Editar sensor"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                            <form method="POST" action="excluir_s.php" class="excluir-sensor">
                                <input type="hidden" name="id" value="<?php echo $sensor['id_sensor']; ?>">
                                <button type="submit" class="acao-icone excluir-user" aria-label="Excluir sensor"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
                            </form>
                        </td>
                    <?php } ?>
                </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table>

<?php if (!$encontrou) { ?>
    <p class="mensagem_vazia">Nenhum sensor encontrado.</p>
<?php } ?>