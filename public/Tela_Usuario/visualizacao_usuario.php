<?php
// TABELA. O arquivo de cadastro abre esta parte com o resultado da consulta.
// O while percorre os registros e imprime uma linha para cada um. Editar e Excluir usam o ID dessa linha.
// A consulta está no cadastro. Aqui, o while só monta as linhas.
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
                    <a href="editar_u.php?id=<?php echo $usuario['id_usuario']; ?>" class="acao_icone editar_user" aria-label="Editar usuário"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                    <form method="POST" action="excluir_u.php" class="confirmar_exclusao">
                        <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                        <button type="submit" class="acao_icone excluir_user" aria-label="Excluir usuário"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
                    </form>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>