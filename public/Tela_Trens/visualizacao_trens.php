<?php
// TABELA. O arquivo de cadastro abre esta parte com o resultado da consulta.
// O while percorre os registros e imprime uma linha para cada um. Editar e Excluir usam o ID dessa linha.
// Esta tabela recebe os dados da página de cadastro.
if (!isset($trens)) {
    exit();
}

$encontrou = false;
?>

<!-- O while monta uma linha por registro. Editar e Excluir aparecem apenas para administradores. -->
<table class="tabela_usuarios tabela_trens">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Velocidade média</th>
            <th>Usuário responsável</th>
            <?php if ($administrador) { ?>
                <th>Ação</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($trem = mysqli_fetch_assoc($trens)) {
            $nome_responsavel = '-';

            // O trem guarda o ID do responsável. O SELECT busca o nome dele.
            if (isset($trem['id_usuario'])) {
                $id_usuario = (int) $trem['id_usuario'];
                $sql = "SELECT nome FROM usuarios WHERE id_usuario = $id_usuario";
                $resultado = mysqli_query($conn, $sql);
                $usuario = mysqli_fetch_assoc($resultado);

                if ($usuario) {
                    $nome_responsavel = $usuario['nome'];
                }
            }

            // Se não encontrou o texto em nenhum campo, pula esta linha.
            if ($busca !== ''
                && stripos($trem['nome'], $busca) === false
                && stripos($nome_responsavel, $busca) === false
            ) {
                continue;
            }
            $encontrou = true;
        ?>
            <tr>
                <td><?php echo escapar($trem['nome']); ?></td>
                <td><?php echo $trem['velocidade_media']; ?> km/h</td>
                <td><?php echo escapar($nome_responsavel); ?></td>

                <?php if ($administrador) { ?>
                    <td class="acao_user">
                        <a href="cadastrar_trens.php?editar=<?php echo $trem['id_trem']; ?>#cadastro" class="acao_icone editar_user" aria-label="Editar trem"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                        <form method="POST" action="excluir_t.php" class="confirmar_exclusao">
                            <input type="hidden" name="id" value="<?php echo $trem['id_trem']; ?>">
                            <button type="submit" class="acao_icone excluir_user" aria-label="Excluir trem"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
                        </form>
                    </td>
                <?php } ?>
                </tr>
        <?php } ?>
    </tbody>
</table>

<?php if (!$encontrou) { ?>
    <p class="mensagem_vazia">Nenhum trem encontrado.</p>
<?php } ?>