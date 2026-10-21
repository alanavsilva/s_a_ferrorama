```php
<?php
require_once "infra/conexao.php";

$nome = "Administrador Teste";
$email = "teste@exemplo.com";
$senha = "Teste123";
$tipo = "administrador";

$sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo "O usuário de teste já está cadastrado.";
    $stmt->close();
    exit;
}

$stmt->close();

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, tipo, senha)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $tipo, $senha_hash);

if ($stmt->execute()) {
    echo "Usuário de teste criado com sucesso!<br><br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Senha: " . htmlspecialchars($senha) . "<br>";
    echo "Tipo: " . htmlspecialchars($tipo);
} else {
    echo "Erro ao criar usuário: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();
?>
```