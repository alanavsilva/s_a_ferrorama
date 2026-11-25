<?php
// TABELA. O arquivo de cadastro abre esta parte com o resultado da consulta.
// O while percorre os registros e imprime uma linha para cada um. Editar e Excluir usam o ID dessa linha.
// A página de cadastro já conferiu o login e fez a consulta.
if (!isset($sensores)) {
    exit();
}

$encontrou = false;
?>

<!-- O while monta uma linha por registro. Editar e Excluir aparecem apenas para administradores. -->
<table class="tabela_usuarios tabela_sensores">
    <thead>
        <tr>
            <th>Nome do sensor</th>
            <th>Rota vinculada</th>
            <th>Tipo de dado registrado</th>

            <?php if ($administrador) { ?>
                <th>Ações</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($sensor = mysqli_fetch_assoc($sensores)) {
            // O sensor guarda o ID da rota. O SELECT busca o nome dela.
            $id_rota = (int) $sensor['id_rotas'];
            $sql = "SELECT nome FROM rotas WHERE id_rotas = $id_rota";
            $resultado = mysqli_query($conn, $sql);
            $rota = mysqli_fetch_assoc($resultado);

            // Se não encontrou o texto em nenhum campo, pula esta linha.
            if ($busca !== ''
                && stripos($sensor['nome_sensor'], $busca) === false
                && stripos($sensor['tipo_dado'], $busca) === false
                && stripos($rota['nome'], $busca) === false
            ) {
                continue;
            }
            $encontrou = true;
        ?>
            <tr>
                <td><?php echo escapar($sensor['nome_sensor']); ?></td>
                <td><?php echo escapar($rota['nome']); ?></td>
                <td><?php echo escapar($sensor['tipo_dado']); ?></td>

                <?php if ($administrador) { ?>
                    <td class="acao_user acao_sensor">
                        <a href="cadastrar_sensores.php?editar=<?php echo $sensor['id_sensor']; ?>#cadastro" class="acao_icone editar_user" aria-label="Editar sensor"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                        <form method="POST" action="excluir_s.php" class="confirmar_exclusao">
                            <input type="hidden" name="id" value="<?php echo $sensor['id_sensor']; ?>">
                            <button type="submit" class="acao_icone excluir_user" aria-label="Excluir sensor"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
                        </form>
                    </td>
                <?php } ?>
                </tr>
        <?php } ?>
    </tbody>
</table>

<?php if (!$encontrou) { ?>
    <p class="mensagem_vazia">Nenhum sensor encontrado.</p>
<?php } ?>