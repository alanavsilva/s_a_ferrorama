<?php
require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

exigir_login();

$sensores = $conn->query(
    "SELECT id_sensor, nome_sensor FROM sensores ORDER BY nome_sensor"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();

    $idSensor = filter_input(INPUT_POST, 'id_sensor', FILTER_VALIDATE_INT);
    $descricao = trim($_POST['descricao'] ?? '');

    if ($idSensor && $descricao !== '') {
        $stmt = $conn->prepare(
            "INSERT INTO falhas_operacionais (id_sensor, descricao, resolvida, data_registro)
             VALUES (?, ?, 0, NOW())"
        );

        $stmt->bind_param('is', $idSensor, $descricao);
        $stmt->execute();
        $stmt->close();

        header('Location: ../Tela_Home/home.php?status=todas#falhas');
        exit;
    }
}
?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= escapar(token_csrf()) ?>">

    <label for="id_sensor">Sensor</label>
    <select name="id_sensor" id="id_sensor" required>
        <?php while ($sensor = $sensores->fetch_assoc()): ?>
            <option value="<?= (int) $sensor['id_sensor'] ?>">
                <?= escapar($sensor['nome_sensor']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="descricao">Ocorrência simulada</label>
    <input
        type="text"
        name="descricao"
        id="descricao"
        value="Falha detectada pelo sensor"
        required
    >

    <button type="submit">Simular falha</button>
</form>