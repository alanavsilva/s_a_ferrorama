<?php
// A consulta vem do cadastrar_usuario.php.
if (!isset($usuarios)) {
    exit();
}
?>

<!-- Mostra os registros na tabela e libera as ações para administradores. -->
<table class="tabela-usuarios">
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
                    <a href="editar_u.php?id=<?php echo $usuario['id_usuario']; ?>" class="acao-icone editar-user" aria-label="Editar usuário"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                    <form method="POST" action="excluir_u.php" class="excluir-usuario">
                        <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                        <button type="submit" class="acao-icone excluir-user" aria-label="Excluir usuário"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
                    </form>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>