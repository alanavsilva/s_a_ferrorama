
<?php

function mostrar_usuarios($resultado): void
{
    $ehAdministrador =
        strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

    if ($resultado === false) {
        echo "<p>Não foi possível carregar os usuários.</p>";
        return;
    }

if ($resultado->num_rows === 0) {
    echo '<p class="mensagem_vazia">Nenhum usuário encontrado.</p>';
    return;
}

?>

<table class="tabela-usuarios">
    <thead>
        <tr>
            <th>Usuário</th>
            <th>Email</th>
            <th>Tipo usuário</th>

            <?php if ($ehAdministrador) { ?>
                <th>Ação</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php while ($usuario = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><?= escapar($usuario['nome']) ?></td>
                <td><?= escapar($usuario['email']) ?></td>
                <td>
                    <?= strcasecmp($usuario['tipo'], 'Administrador') === 0
                        ? 'Administrador'
                        : 'Funcionário' ?>
                </td>

                <?php if ($ehAdministrador) { ?>
                    <td class="acao_user">
                        <a
                            href="editar_u.php?id=<?= (int)$usuario['id_usuario'] ?>"
                            class="acao-icone editar-user"
                            aria-label="Editar usuário"
                        >
                            <img
                                src="https://img.icons8.com/ios-filled/18/701015/edit.png"
                                alt=""
                            >
                        </a>

                        <form
                            method="POST"
                            action="excluir_u.php"
                            style="display:inline;"
                            onsubmit="return confirm('Deseja realmente excluir este usuário?');"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= escapar(token_csrf()) ?>"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int)$usuario['id_usuario'] ?>"
                            >

                            <button
                                type="submit"
                                class="acao-icone excluir-user"
                                aria-label="Excluir usuário"
                            >
                                <img
                                    src="https://img.icons8.com/ios-filled/18/701015/trash.png"
                                    alt=""
                                >
                            </button>
                        </form>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
}
?>