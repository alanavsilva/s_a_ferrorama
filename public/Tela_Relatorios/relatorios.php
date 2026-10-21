<?php

require_once "../../infra/conexao.php";
require_once "../../infra/auth.php";
require_once "visualizacao_cadastro.php";

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

<body class="pagina-relatorios">

<div class="layout-relatorios">

    <aside class="menu-lateral-relatorios">

        <div class="marca-relatorios">
            <h2>SISTEMA TREMTECH</h2>
        </div>

        <div class="linha-menu-relatorios"></div>

        <nav class="navegacao-relatorios">
            <a href="../home/index.php" class="item-menu-relatorios">Home</a>
            <a href="../usuarios/listar_usuarios.php" class="item-menu-relatorios">Usuários</a>
            <a href="../sensores/listar_sensores.php" class="item-menu-relatorios">Sensores</a>
            <a href="../monitoramento/monitoramento.php" class="item-menu-relatorios">Monitoramento</a>
            <a href="relatorios.php" class="item-menu-relatorios ativo-relatorios">Relatórios</a>
            <a href="../trem/trem.php" class="item-menu-relatorios">Trem</a>
            <a href="../rotas/rotas.php" class="item-menu-relatorios">Rotas</a>
        </nav>

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
