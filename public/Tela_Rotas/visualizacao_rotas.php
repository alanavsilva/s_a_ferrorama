<?php
// O while mostra cada registro em uma linha e usa seu ID nos botões Editar e Excluir.
if (!isset($rotas)) {
    exit();
}

$encontrou = false;
?>

<!-- O while monta uma linha por registro. Editar e Excluir aparecem apenas para administradores. -->
<table class="tabela_usuarios tabela_rotas">
    <thead>
        <tr>
            <th>Nome da rota</th>
            <th>Trem vinculado</th>
            <th>Descrição</th>
            <th>Distância</th>
            <th>Tempo estimado</th>
            <?php if ($administrador) { ?>
                <th>Ação</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($rota = mysqli_fetch_assoc($rotas)) {
            // A rota guarda o ID. Este SELECT busca o nome do trem por esse ID.
            $id_trem = (int) $rota['id_trem'];
            $sql = "SELECT nome FROM trens WHERE id_trem = $id_trem";
            $resultado = mysqli_query($conn, $sql);
            $trem = mysqli_fetch_assoc($resultado);

            // Se não encontrou o texto em nenhum campo, pula esta linha.
            if ($busca !== ''
                && stripos($rota['nome'], $busca) === false
                && stripos($rota['descricao'], $busca) === false
                && stripos($trem['nome'], $busca) === false
            ) {
                continue;
            }
            $encontrou = true;
        ?>
            <tr>
                <td><?php echo escapar($rota['nome']); ?></td>
                <td><?php echo escapar($trem['nome']); ?></td>
                <td><?php echo escapar($rota['descricao']); ?></td>
                <td><?php echo $rota['extensao']; ?> km</td>
                <td><?php echo $rota['tempo']; ?> min</td>

                <?php if ($administrador) { ?>
                    <td class="acao_user">
                        <a href="cadastrar_rotas.php?editar=<?php echo $rota['id_rotas']; ?>#cadastro" class="acao_icone editar_user" aria-label="Editar rota"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                        <form method="POST" action="excluir_r.php" class="confirmar_exclusao">
                            <input type="hidden" name="id" value="<?php echo $rota['id_rotas']; ?>">
                            <button type="submit" class="acao_icone excluir_user" aria-label="Excluir rota"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
                        </form>
                    </td>
                <?php } ?>
                </tr>
        <?php } ?>
    </tbody>
</table>

<?php if (!$encontrou) { ?>
    <p class="mensagem_vazia">Nenhuma rota encontrada.</p>
<?php } ?>