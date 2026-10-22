<?php

function mostrar_sensores($resultado, bool $mostrarAcoes = true): void
{
    $ehAdministrador =
        strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

      $exibirAcoes = $mostrarAcoes && $ehAdministrador;

    if ($resultado === false || $resultado === null) {
        echo '<p role="alert">Não foi possível carregar os sensores.</p>';
        return;
    }

     if ($resultado->num_rows === 0) {
        echo '<p class="mensagem_vazia">Nenhum sensor encontrado.</p>';
        return;
    }

    ?>

          <table class="tabela-sensores">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome do sensor</th>
                <th>Trem vinculado</th>
                <th>Tipo de dado registrado</th>
                <th>Rota vinculada</th>

                <?php if ($exibirAcoes): ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php while ($sensor = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $sensor['id_sensor'] ?></td>
                    <td><?= escapar($sensor['nome_sensor'] ?? '') ?></td>
                    <td><?= escapar($sensor['trem_vinculado'] ?? '') ?></td>
                    <td><?= escapar($sensor['tipo_dado'] ?? '') ?></td>
                    <td><?= escapar($sensor['rota_vinculada'] ?? '') ?></td>

            <?php if ($exibirAcoes): ?>
            <td class="acao_sensor">
                
            <a href="editar_s.php?id=<?= (int) $sensor['id_sensor'] ?>" class="acao-icone" aria-label="Editar sensor">
            <img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt="" >
            </a>

            <form method="POST" action="excluir_s.php" class="js-confirmar-exclusao" data-titulo="Excluir sensor" data-mensagem="Tem certeza de que deseja excluir este sensor? Essa ação não pode ser desfeita.">
            
            <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
            <input type="hidden" name="id" value="<?= (int) $sensor['id_sensor'] ?>">

            <button type="submit" class="acao-icone" aria-label="Excluir sensor">
            <img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt="">
            </button>

             </form>
                </td>
               <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <dialog class="modal-confirmacao" aria-labelledby="tituloConfirmacaoSensor">
        <div class="modal-confirmacao-icone" aria-hidden="true">!</div>
        <h2 id="tituloConfirmacaoSensor">Excluir sensor?</h2>
        <p class="modal-confirmacao-mensagem">Essa ação não pode ser desfeita.</p>
        <div class="modal-confirmacao-acoes">
            <button type="button" class="modal-btn-cancelar">Cancelar</button>
            <button type="button" class="modal-btn-excluir">Excluir sensor</button>
        </div>
    </dialog>
    <script>
    (() => {
        const modal = document.querySelector('.modal-confirmacao');
        if (!modal) return;
        let formularioPendente = null;
        document.querySelectorAll('.js-confirmar-exclusao').forEach((formulario) => {
            formulario.addEventListener('submit', (evento) => {
                evento.preventDefault();
                formularioPendente = formulario;
                modal.querySelector('h2').textContent = formulario.dataset.titulo || 'Confirmar exclusão';
                modal.querySelector('.modal-confirmacao-mensagem').textContent = formulario.dataset.mensagem || 'Essa ação não pode ser desfeita.';
                modal.querySelector('.modal-btn-excluir').textContent = 'Excluir trem';
                modal.showModal();
            });
        });
        modal.querySelector('.modal-btn-cancelar').addEventListener('click', () => modal.close());
        modal.querySelector('.modal-btn-excluir').addEventListener('click', () => {
            if (formularioPendente) formularioPendente.submit();
        });
        modal.addEventListener('click', (evento) => {
            if (evento.target === modal) modal.close();
        });
    })();
    </script>

    <?php
}
?>
