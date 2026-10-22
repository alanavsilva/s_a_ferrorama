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
}
$sql = "SELECT t.nome, t.velocidade_media, t.tipo, r.nome AS rota
        FROM trens t
        INNER JOIN rotas r ON r.id_rotas = t.id_rotas
        ORDER BY t.id_trem DESC";

$trens = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="titulo-lista">Trens cadastrados</h2>

<table class="tabela-trens">
  <thead>
    <tr>
      <th>Nome</th>
      <th>Velocidade</th>
      <th>Tipo/Categoria</th>
      <th>Rota</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($trens) === 0): ?>
      <tr>
        <td colspan="4" class="vazio">Nenhum trem cadastrado ainda.</td>
      </tr>
    <?php else: ?>
      <?php foreach ($trens as $trem): ?>
        <tr>
          <td><?= htmlspecialchars($trem['nome']) ?></td>
          <td><?= htmlspecialchars($trem['velocidade_media']) ?> km/h</td>
          <td><?= htmlspecialchars($trem['tipo']) ?></td>
          <td><?= htmlspecialchars($trem['rota']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
