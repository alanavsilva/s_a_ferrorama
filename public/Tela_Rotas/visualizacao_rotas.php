<?php
function mostrar_rotas($resultado): void
{
    $ehAdministrador =
        strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

    if ($resultado === false) {
        echo '<p>Não foi possível carregar as rotas.</p>';
        return;
    }

    if ($resultado->num_rows === 0) {
        echo '<p class="mensagem_vazia">Nenhuma rota encontrada.</p>';
        return;
    }
    ?>

    <table class="tabela-usuarios tabela-rotas">
        <thead>
            <tr>
                <th>Nome da rota</th>
                <th>Extensão</th>
                <th>Tempo estimado</th>
                <?php if ($ehAdministrador): ?>
                    <th>Ação</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($rota = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= escapar($rota['nome']) ?></td>
                    <td>
                        <?= escapar(number_format(
                            (float) $rota['extensao'],
                            2,
                            ',',
                            '.'
                        )) ?> km
                    </td>
                    <td><?= (int) $rota['tempo'] ?> min</td>

                    <?php if ($ehAdministrador): ?>
                        <td class="acao_user">
                            <a
                                href="editar_r.php?id=<?= (int) $rota['id_rotas'] ?>"
                                class="acao-icone editar-user"
                                aria-label="Editar rota"
                            >
                                <img
                                    src="https://img.icons8.com/ios-filled/18/701015/edit.png"
                                    alt=""
                                >
                            </a>

                            <form
                                method="POST"
                                action="excluir_r.php"
                                class="js-confirmar-exclusao"
                                data-titulo="Excluir rota"
                                data-mensagem="Tem certeza de que deseja excluir esta rota? Trens vinculados podem impedir a exclusão."
                            >
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= escapar(token_csrf()) ?>"
                                >
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $rota['id_rotas'] ?>"
                                >
                                <button
                                    type="submit"
                                    class="acao-icone excluir-user"
                                    aria-label="Excluir rota"
                                >
                                    <img
                                        src="https://img.icons8.com/ios-filled/18/701015/trash.png"
                                        alt=""
                                    >
                                </button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($ehAdministrador): ?>
        <dialog class="modal-confirmacao" aria-labelledby="tituloConfirmacaoRota">
            <div class="modal-confirmacao-icone" aria-hidden="true">!</div>
            <h2 id="tituloConfirmacaoRota">Excluir rota?</h2>
            <p class="modal-confirmacao-mensagem">
                Essa ação não pode ser desfeita.
            </p>
            <div class="modal-confirmacao-acoes">
                <button type="button" class="modal-btn-cancelar">Cancelar</button>
                <button type="button" class="modal-btn-excluir">Excluir rota</button>
            </div>
        </dialog>

        <script>
        (() => {
            const modal = document.querySelector('.modal-confirmacao');
            if (!modal) return;

            let formularioPendente = null;

            document.querySelectorAll('.js-confirmar-exclusao').forEach((form) => {
                form.addEventListener('submit', (evento) => {
                    evento.preventDefault();
                    formularioPendente = form;
                    modal.querySelector('h2').textContent =
                        form.dataset.titulo || 'Confirmar exclusão';
                    modal.querySelector('.modal-confirmacao-mensagem').textContent =
                        form.dataset.mensagem || 'Essa ação não pode ser desfeita.';
                    modal.showModal();
                });
            });

            modal.querySelector('.modal-btn-cancelar')
                .addEventListener('click', () => modal.close());

            modal.querySelector('.modal-btn-excluir')
                .addEventListener('click', () => {
                    if (formularioPendente) formularioPendente.submit();
                });

            modal.addEventListener('click', (evento) => {
                if (evento.target === modal) modal.close();
            });
        })();
        </script>
    <?php endif;
}