<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";

$mensagem_relatorios = "";
$sensor_relatorios = "";
$periodo_relatorios = "";
$relatorio_gerado = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sensor_relatorios = $_POST["sensor_relatorios"] ?? "";
    $periodo_relatorios = $_POST["periodo_relatorios"] ?? "";

    $sensores_permitidos_relatorios = [
        "leste",
        "central",
        "oeste"
    ];

    $periodos_permitidos_relatorios = [
        "maio-2026",
        "junho-2026",
        "julho-2026",
        "agosto-2026",
        "setembro-2026",
        "outubro-2026"
    ];

    if (
        in_array($sensor_relatorios, $sensores_permitidos_relatorios, true) &&
        in_array($periodo_relatorios, $periodos_permitidos_relatorios, true)
    ) {
        $relatorio_gerado = true;
        $mensagem_relatorios = "Filtros selecionados com sucesso.";
    } else {
        $mensagem_relatorios = "Selecione um sensor e um período válidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - TREMTECH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/style.css">
   
</head>


<div class="layout-relatorios">

    <aside class="menu-lateral-relatorios">


        <aside class="menu">
       <img class="logo-trem"
    src="https://img.icons8.com/ios/100/ffffff/train.png"
    alt="Logo TremTech">

  <p class="marca">SISTEMA TREMTECH</p>
     <a href="../../public/Tela_Home/home.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/home.png');"></span>
  Home
</a>

<a class="ativo" href="../../public/Tela_Usuario/cadastrar_usuario.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/user.png');"></span>
  Usuários
</a>

<a href="../../public/Tela_Sensores/cadastrar_sensores.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/701015/radio-waves.png');"></span>
  Sensores
</a>

<a href="../../public/Tela_Trens/cadastrar_trens.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/train.png');"></span>
  Trem
</a>

<a href="../../public/Tela_Rotas/cadastrar_rotas.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/map.png');"></span>
  Rota
</a>
<a href="../../public/Tela_Monitoramento/monitoramento.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/visible.png');"></span>
  Monitoramento
</a>

<a href="../../public/Tela_Relatorios/relatorios.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/report-card.png');"></span>
  Relatórios
</a>


<a class="logout" href="../../logout.php">
  <span class="icone" style="mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png'); -webkit-mask-image: url('https://img.icons8.com/ios-filled/18/000000/logout-rounded.png');"></span>
  Logout
</a>
    </aside>



        

        <a href="../logout.php" class="sair-relatorios">Logout</a>

    </aside>

    <main class="conteudo-relatorios">

        <header class="cabecalho-relatorios">
            <div class="titulo-area-relatorios">
                <p class="sobretitulo-relatorios">
                    PAINEL DE MONITORAMENTO
                </p>

                <h1>Monitoramento em tempo real</h1>
            </div>

            <div class="perfil-relatorios">
                <div class="avatar-relatorios"></div>

                <div class="dados-perfil-relatorios">
                    <strong>Admin</strong>
                    <span>alana.veiga</span>
                </div>
            </div>
        </header>

        <section class="area-relatorios">

            <form
                class="formulario-relatorios"
                action="relatorios.php"
                method="POST"
            >

                <h2>Gerar relatório</h2>

                <div class="campo-relatorios">
                    <label for="sensor-relatorios">Sensor</label>

                    <select
                        name="sensor_relatorios"
                        id="sensor-relatorios"
                        required
                    >
                        <option value="">Selecione um sensor</option>

                        <option value="leste"
                            <?= $sensor_relatorios === "leste" ? "selected" : "" ?>>
                            Sensor leste
                        </option>

                        <option value="central"
                            <?= $sensor_relatorios === "central" ? "selected" : "" ?>>
                            Sensor central
                        </option>

                        <option value="oeste"
                            <?= $sensor_relatorios === "oeste" ? "selected" : "" ?>>
                            Sensor oeste
                        </option>
                    </select>
                </div>

                <div class="campo-relatorios">
                    <label for="periodo-relatorios">Período</label>

                    <select
                        name="periodo_relatorios"
                        id="periodo-relatorios"
                        required
                    >
                        <option value="">Selecione um período</option>

                        <option value="maio-2026"
                            <?= $periodo_relatorios === "maio-2026" ? "selected" : "" ?>>
                            Maio de 2026
                        </option>

                        <option value="junho-2026"
                            <?= $periodo_relatorios === "junho-2026" ? "selected" : "" ?>>
                            Junho de 2026
                        </option>

                        <option value="julho-2026"
                            <?= $periodo_relatorios === "julho-2026" ? "selected" : "" ?>>
                            Julho de 2026
                        </option>

                        <option value="agosto-2026"
                            <?= $periodo_relatorios === "agosto-2026" ? "selected" : "" ?>>
                            Agosto de 2026
                        </option>

                        <option value="setembro-2026"
                            <?= $periodo_relatorios === "setembro-2026" ? "selected" : "" ?>>
                            Setembro de 2026
                        </option>

                        <option value="outubro-2026"
                            <?= $periodo_relatorios === "outubro-2026" ? "selected" : "" ?>>
                            Outubro de 2026
                        </option>
                    </select>
                </div>

                <button type="submit" class="botao-relatorios">
                    Gerar relatório
                </button>

                <?php if ($mensagem_relatorios !== ""): ?>
                    <p class="mensagem-relatorios">
                        <?= htmlspecialchars($mensagem_relatorios, ENT_QUOTES, "UTF-8") ?>
                    </p>
                <?php endif; ?>

            </form>

            <section class="resultado-relatorios">

                <?php if ($relatorio_gerado): ?>

                    <div class="estado-relatorios">
                        <h2>Relatório solicitado</h2>

                        <p>
                            Sensor:
                            <strong>
                                <?= htmlspecialchars($sensor_relatorios, ENT_QUOTES, "UTF-8") ?>
                            </strong>
                        </p>

                        <p>
                            Período:
                            <strong>
                                <?= htmlspecialchars($periodo_relatorios, ENT_QUOTES, "UTF-8") ?>
                            </strong>
                        </p>

                        <p>
                            Os filtros foram recebidos.
                            Os dados reais precisam ser consultados no banco de dados.
                        </p>
                    </div>

                <?php else: ?>

                    <div class="estado-vazio-relatorios">
                        <h2>Relatório</h2>

                        <p>
                            Selecione um sensor e um período
                            para solicitar um relatório.
                        </p>
                    </div>

                <?php endif; ?>

            </section>

        </section>

    </main>

</div>

</body>
</html>
