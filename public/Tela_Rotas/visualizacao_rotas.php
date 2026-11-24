<?php
require_once '../../infra/auth.php';

exigir_login();

$encontrou = false;
?>

<!-- Mostra os registros na tabela e libera as ações para administradores. -->
<table class="tabela-usuarios tabela-rotas">
    <thead>
        <tr>
            <th>Nome da rota</th>
            <th>Trem vinculado</th>
            <th>Descrição</th>
            <th>Distância</th>
            <th>Tempo estimado</th>
            <?php if ($ehAdministrador) { ?>
                <th>Ação</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($rota = mysqli_fetch_assoc($rotas)) {
            // A rota guarda o ID. Este SELECT busca o nome do trem por esse ID.
            $idTrem = (int) $rota['id_trem'];
            $sql = "SELECT nome FROM trens WHERE id_trem = $idTrem";
            $resultado = mysqli_query($conn, $sql);
            $trem = mysqli_fetch_assoc($resultado);

            // stripos procura o texto sem diferenciar maiúsculas e minúsculas.
            if ($busca === ''
                || stripos($rota['nome'], $busca) !== false
                || stripos($rota['descricao'], $busca) !== false
                || stripos($trem['nome'], $busca) !== false) {
                $encontrou = true;
        ?>
                <tr>
                    <td><?php echo escapar($rota['nome']); ?></td>
                    <td><?php echo escapar($trem['nome']); ?></td>
                    <td><?php echo escapar($rota['descricao']); ?></td>
                    <td><?php echo $rota['extensao']; ?> km</td>
                    <td><?php echo $rota['tempo']; ?> min</td>

                    <?php if ($ehAdministrador) { ?>
                        <td class="acao_user">
                            <a href="editar_r.php?id=<?php echo $rota['id_rotas']; ?>" class="acao-icone editar-user" aria-label="Editar rota"><img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt=""></a>

                            <form method="POST" action="excluir_r.php" class="excluir-rota">
                                <input type="hidden" name="id" value="<?php echo $rota['id_rotas']; ?>">
                                <button type="submit" class="acao-icone excluir-user" aria-label="Excluir rota"><img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt=""></button>
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
    <p class="mensagem_vazia">Nenhuma rota encontrada.</p>
<?php } ?>