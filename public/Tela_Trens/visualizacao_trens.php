<?php

function mostrar_trens($resultado): void
{
    $ehAdministrador =
        strcasecmp($_SESSION['tipo'] ?? '', 'Administrador') === 0;

    if ($resultado === false || $resultado === null) {
        echo '<p role="alert">Não foi possível carregar os trens.</p>';
        return;
    }

     if ($resultado->num_rows === 0) {
        echo '<p class="mensagem_vazia">Nenhum trem encontrado.</p>';
        return;
    }

}

?>

<table class="tabela-trens">
  <thead>
    <tr>
      <th>Nome</th>
      <th>Velocidade média</th>
      <th>Tipo/Categoria</th>
      <th>Rota</th>

          <?php if ($ehAdministrador) { ?>
                <th>Ação</th>
            <?php } ?>
    </tr>
  </thead>

  <tbody>
    <?php while ($trem = $resultado->fetch_assoc()): ?>
      <tr>
          <td><?= (int) $trem['id_trem'] ?></td>
          <td><?= escapar($trem['nome_trem'] ?? '') ?></td>
          <td><?= escapar($trem['velocidade_media'] ?? '') ?></td>
          <td><?= escapar($trem['tipo'] ?? '') ?></td>
          <td><?= escapar($trem['rota'] ?? '') ?></td>

            <?php if ($ehAdministrador) { ?>
            <td class="acao_sensor">

            <a href="editar_t.php?id=<?= (int) $trem['id_trem'] ?>" class="acao-icone" aria-label="Editar trem">
            <img src="https://img.icons8.com/ios-filled/18/701015/edit.png" alt="" >
            </a>

            <form method="POST" action="excluir_t.php" class="js-confirmar-exclusao" data-titulo="Excluir trem" data-mensagem="Tem certeza de que deseja excluir este trem? Essa ação não pode ser desfeita.">
            
            <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">
            <input type="hidden" name="id" value="<?= (int) $trem['id_trem'] ?>">

            <button type="submit" class="acao-icone" aria-label="Excluir trem">
            <img src="https://img.icons8.com/ios-filled/18/701015/trash.png" alt="">
            </button>

                </form>
                </td>
               <?php endif; ?>
        </tr>
      <?php endwhile; ?>
  </tbody>
</table>

 <dialog class="modal-confirmacao"
        aria-labelledby="tituloConfirmacaoTrem">

    <div class="modal-confirmacao-icone" aria-hidden="true">!</div>

    <h2 id="tituloConfirmacaoTrem">Excluir trem?</h2>

    <p class="modal-confirmacao-mensagem">
        Essa ação não pode ser desfeita.
    </p>

    <div class="modal-confirmacao-acoes">
        <button type="button" class="modal-btn-cancelar">
            Cancelar
        </button>

        <button type="button" class="modal-btn-excluir">
            Excluir trem
        </button>
    </div>
</dialog>

<script>
(() => {
    const modal = document.querySelector('.modal-confirmacao');

    if (!modal) return;

    let formularioPendente = null;

    document.querySelectorAll('.js-confirmar-exclusao')
        .forEach((formulario) => {
            formulario.addEventListener('submit', (evento) => {
                evento.preventDefault();

                formularioPendente = formulario;

                modal.querySelector('h2').textContent =
                    formulario.dataset.titulo || 'Confirmar exclusão';

                modal.querySelector('.modal-confirmacao-mensagem')
                    .textContent =
                    formulario.dataset.mensagem ||
                    'Essa ação não pode ser desfeita.';

                modal.querySelector('.modal-btn-excluir').textContent =
                    'Excluir trem';

                modal.showModal();
            });
        });

    modal.querySelector('.modal-btn-cancelar')
        .addEventListener('click', () => {
            modal.close();
            formularioPendente = null;
        });

    modal.querySelector('.modal-btn-excluir')
        .addEventListener('click', () => {
            if (formularioPendente) {
                formularioPendente.submit();
            }
        });

    modal.addEventListener('close', () => {
        formularioPendente = null;
    });

    modal.addEventListener('click', (evento) => {
        if (evento.target === modal) {
            modal.close();
        }
    });
})();
</script>

<?php
}
?>