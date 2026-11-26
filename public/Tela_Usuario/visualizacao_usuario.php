<?php
// O while mostra cada registro em uma linha e usa seu ID nos botões Editar e Excluir.
if (!isset($usuarios)) {
    exit();
}
?>

<!-- O while monta uma linha por registro. Editar e Excluir aparecem apenas para administradores. -->
<table class="tabela_usuarios">
    <tr>
        <th>Usuário</th>
        <th>Email</th>
        <th>Tipo usuário</th>

        <?php if ($administrador) { ?>
            <th>Ação</th>
        <?php } ?>
    </tr>

    <?php while ($usuario = mysqli_fetch_assoc($usuarios)) { ?>
        <tr>
            <td><?php echo escapar($usuario['nome']); ?></td>
            <td><?php echo escapar($usuario['email']); ?></td>
            <td><?php echo escapar($usuario['tipo']); ?></td>

            <?php if ($administrador) { ?>
                <td class="acao_user">
                    <a href="cadastrar_usuario.php?editar=<?php echo $usuario['id_usuario']; ?>#cadastro" class="acao_icone editar_user" aria-label="Editar usuário"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                    <form method="POST" action="excluir_u.php" class="confirmar_exclusao">
                        <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                        <button type="submit" class="acao_icone excluir_user" aria-label="Excluir usuário"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
                    </form>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
