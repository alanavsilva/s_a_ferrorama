<?php
// Esta tabela recebe os dados da página de cadastro.
if (!isset($trens)) {
    exit();
}

$encontrou = false;
?>

<!-- Mostra os registros na tabela e libera as ações para administradores. -->
<table class="tabela-usuarios tabela-trens">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Velocidade média</th>
            <th>Usuário responsável</th>
            <?php if ($ehAdministrador) { ?>
                <th>Ação</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($trem = mysqli_fetch_assoc($trens)) {
            $nomeResponsavel = '-';

            // O trem guarda o ID do responsável. O SELECT busca o nome dele.
            if (isset($trem['id_usuario'])) {
                $idUsuario = (int) $trem['id_usuario'];
                $sql = "SELECT nome FROM usuarios WHERE id_usuario = $idUsuario";
                $resultado = mysqli_query($conn, $sql);
                $usuario = mysqli_fetch_assoc($resultado);

                if ($usuario) {
                    $nomeResponsavel = $usuario['nome'];
                }
            }

            // stripos procura o texto ignorando maiúsculas e minúsculas.
            if ($busca === ''
                || stripos($trem['nome'], $busca) !== false
                || stripos($nomeResponsavel, $busca) !== false) {
                $encontrou = true;
        ?>
                <tr>
                    <td><?php echo escapar($trem['nome']); ?></td>
                    <td><?php echo $trem['velocidade_media']; ?> km/h</td>
                    <td><?php echo escapar($nomeResponsavel); ?></td>

                    <?php if ($ehAdministrador) { ?>
                        <td class="acao_user">
                            <a href="editar_t.php?id=<?php echo $trem['id_trem']; ?>" class="acao-icone editar-user" aria-label="Editar trem"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                            <form method="POST" action="excluir_t.php" class="excluir-trem">
                                <input type="hidden" name="id" value="<?php echo $trem['id_trem']; ?>">
                                <button type="submit" class="acao-icone excluir-user" aria-label="Excluir trem"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
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
    <p class="mensagem_vazia">Nenhum trem encontrado.</p>
<?php } ?>