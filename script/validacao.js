// Validações de campos para os formulários que chamam estas funções.
function validar_usuario() {
    const nome = document.getElementById("nome").value.trim();
    const email = document.getElementById("email").value.trim();
    const senha = document.getElementById("senha").value;

    if (!nome || !email || !senha) {
        alert("Preencha todos os campos.");
        return false;
    }

    if (nome.length < 3 || !email.includes("@")) {
        alert("Confira o nome e o e-mail.");
        return false;
    }

    return true;
}

function validar_sensor() {
    const nome = document.getElementById("nome_sensor").value.trim();
    const tipo = document.getElementById("tipo_dado").value;
    const rota = document.getElementById("id_rotas").value;

    if (!nome || !tipo || !rota) {
        alert("Preencha todos os campos.");
        return false;
    }

    return true;
}

// Botão que abre e fecha o formulário de cadastro.
function alternarCadastroUsuario() {
    const cadastro = document.getElementById("cadastro");

    if (cadastro) {
        // hidden esconde o formulário. A classe ativo aplica o estilo de aberto.
        cadastro.hidden = !cadastro.hidden;
        cadastro.classList.toggle("ativo", !cadastro.hidden);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const botao = document.getElementById("btnAbrirCadastro");
    const formCep = document.getElementById("form_cep");

    if (botao) {
        botao.onclick = function () {
            alternarCadastroUsuario();
            const cadastro = document.getElementById("cadastro");
            if (cadastro) {
                botao.setAttribute("aria-expanded", String(!cadastro.hidden));
            }
        };
    }

    if (formCep) {
        formCep.onsubmit = consultarCep;
    }
});

// Consulta o endereço na API do ViaCEP.
async function consultarCep(evento) {
    evento.preventDefault();

    const mensagem = document.getElementById("mensagem_cep");

    // Remove caracteres que não são números, como o hífen.
    const cep = document.getElementById("cep").value.replace(/\D/g, "");

    document.getElementById("logradouro").value = "";
    document.getElementById("bairro").value = "";
    document.getElementById("cidade").value = "";
    document.getElementById("uf").value = "";

    if (cep.length !== 8) {
        mensagem.textContent = "Digite um CEP com 8 números.";
        return;
    }

    mensagem.textContent = "Consultando CEP...";

    try {
        // await espera a resposta do ViaCEP antes de preencher os campos.
        const resposta = await fetch("https://viacep.com.br/ws/" + cep + "/json/");
        const dados = await resposta.json();

        if (dados.erro) {
            mensagem.textContent = "CEP não encontrado.";
            return;
        }

        document.getElementById("logradouro").value = dados.logradouro;
        document.getElementById("bairro").value = dados.bairro;
        document.getElementById("cidade").value = dados.localidade;
        document.getElementById("uf").value = dados.uf;
        mensagem.textContent = "Endereço encontrado.";
    } catch (erro) {
        mensagem.textContent = "Erro ao consultar o CEP.";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const trens = document.querySelectorAll("#velocidades-trens li");
    const titulo = document.getElementById("velocidade-titulo");
    const valor = document.getElementById("velocidade-valor");
    let indice = 0;

    if (trens.length === 0) {
        return;
    }

    function mostrarTrem() {
        // Cada item tem o nome do trem no primeiro span e a velocidade no segundo.
        const campos = trens[indice].getElementsByTagName("span");

        titulo.textContent = "Velocidade média - " + campos[0].textContent;
        valor.textContent = campos[1].textContent;

        indice++;

        if (indice === trens.length) {
            indice = 0;
        }
    }

    mostrarTrem();

    // Mostra o próximo trem a cada três segundos.
    setInterval(mostrarTrem, 3000);
});

// A página pública e o monitoramento usam IDs diferentes para o marcador.
// Animação do marcador no mapa da página pública e do monitoramento.
const trem = document.querySelector("#marcadorTremLanding, #marcadorTrem");

// Cada par guarda a posição horizontal e vertical de um ponto do trajeto.
const caminho = [
    [70, 175], [80, 115], [150, 75], [250, 60], [360, 65], [470, 90],
    [580, 120], [670, 165], [690, 220], [640, 265], [540, 290], [420, 300],
    [300, 290], [190, 260], [100, 225], [70, 175],
    [140, 185], [250, 165], [360, 160], [470, 175], [545, 200],
    [580, 235], [545, 200], [470, 175], [360, 160],
    [420, 130], [480, 130], [580, 120], [480, 130], [420, 130], [360, 160],
    [270, 235], [320, 285], [270, 235], [360, 160], [250, 165],
    [140, 185], [70, 175]
];

let trecho = 0;
let passo = 0;

function moverTrem() {
    passo++;

    if (passo === 25) {
        passo = 0;
        trecho++;

        if (trecho === caminho.length - 1) {
            trecho = 0;
        }
    }

    const inicio = caminho[trecho];
    const fim = caminho[trecho + 1];

    // Divide o trecho em 25 passos para o trem se mover aos poucos.
    const x = inicio[0] + (fim[0] - inicio[0]) * passo / 25;
    const y = inicio[1] + (fim[1] - inicio[1]) * passo / 25;

    // translate muda a posição do marcador no desenho.
    trem.setAttribute("transform", "translate(" + x + " " + y + ")");
}

// A animação só roda nas páginas que têm o mapa.
if (trem) {
    setInterval(moverTrem, 50);
}

// Confirmações de exclusão dos cadastros.
const formulariosRota = document.querySelectorAll(".excluir-rota");

formulariosRota.forEach(function (formulario) {
    formulario.onsubmit = function () {
        // Cancelar impede o envio do formulário.
        return confirm("Tem certeza de que deseja excluir esta rota?");
    };
});

const formulariosSensor = document.querySelectorAll(".excluir-sensor");

formulariosSensor.forEach(function (formulario) {
    formulario.onsubmit = function () {
        return confirm("Tem certeza de que deseja excluir este sensor?");
    };
});

const formulariosTrem = document.querySelectorAll(".excluir-trem");

formulariosTrem.forEach(function (formulario) {
    formulario.onsubmit = function () {
        return confirm("Tem certeza de que deseja excluir este trem?");
    };
});

const formulariosUsuario = document.querySelectorAll(".excluir-usuario");

formulariosUsuario.forEach(function (formulario) {
    formulario.onsubmit = function () {
        return confirm("Tem certeza de que deseja excluir este usuário?");
    };
});

// Confirmação de saída com a janela do próprio site.
const linksLogout = document.querySelectorAll("[data-confirmar-logout]");

if (linksLogout.length > 0) {
    // A janela usa as classes que já estão no style.css.
    const janelaSaida = document.createElement("dialog");
    janelaSaida.className = "modal-confirmacao";
    janelaSaida.setAttribute("aria-labelledby", "tituloConfirmarSaida");
    janelaSaida.innerHTML = `
        <div class="modal-confirmacao-icone" aria-hidden="true">?</div>
        <h2 id="tituloConfirmarSaida">Sair do sistema?</h2>
        <p class="modal-confirmacao-mensagem">Tem certeza de que deseja sair da sua conta?</p>
        <div class="modal-confirmacao-acoes">
            <button type="button" class="modal-btn-cancelar">Continuar no sistema</button>
            <button type="button" class="modal-btn-sair">Sair</button>
        </div>
    `;
    document.body.appendChild(janelaSaida);

    const cancelar = janelaSaida.querySelector(".modal-btn-cancelar");
    const sair = janelaSaida.querySelector(".modal-btn-sair");

    cancelar.onclick = function () {
        janelaSaida.close();
    };

    linksLogout.forEach(function (link) {
        link.onclick = function (evento) {
            evento.preventDefault();
            sair.onclick = function () {
                window.location.href = link.href;
            };
            janelaSaida.showModal();
        };
    });
}

// Envio dos filtros do monitoramento quando a pessoa muda a seleção.
const formularioMonitoramento = document.getElementById("filtros-monitoramento");

if (formularioMonitoramento) {
    const filtros = document.querySelectorAll('select[form="filtros-monitoramento"]');

    filtros.forEach(function (campo) {
        campo.onchange = function () {
            // Envia os filtros juntos quando uma opção muda.
            formularioMonitoramento.submit();
        };
    });
}

// No registro, troca o campo numérico pela descrição quando o sensor é de Status.
const sensorLeitura = document.getElementById("id_sensor");
const campoValorLeitura = document.getElementById("campo-valor");

if (sensorLeitura && campoValorLeitura) {
    const valor = document.getElementById("valor");
    const unidade = document.getElementById("unidade");
    const campoDescricao = document.getElementById("campo-descricao-status");
    const descricao = document.getElementById("descricao_falha");
    const botaoSalvar = document.getElementById("botao-salvar-informacao");

    function atualizarCamposLeitura() {
        // data-tipo informa se o sensor registra um número ou uma falha.
        const opcao = sensorLeitura.options[sensorLeitura.selectedIndex];
        const tipo = opcao.getAttribute("data-tipo");

        if (tipo === "Status") {
            campoValorLeitura.hidden = true;
            valor.required = false;
            valor.disabled = true;
            campoDescricao.hidden = false;
            descricao.required = true;
            botaoSalvar.textContent = "CADASTRAR FALHA";
        } else {
            campoValorLeitura.hidden = false;
            valor.required = true;
            valor.disabled = false;
            campoDescricao.hidden = true;
            descricao.required = false;
            botaoSalvar.textContent = "SALVAR LEITURA";

            if (tipo === "Velocidade") {
                unidade.textContent = "(km/h)";
                valor.max = 600;
            } else if (tipo === "Temperatura") {
                unidade.textContent = "(°C)";
                valor.max = 150;
            } else {
                unidade.textContent = "";
                valor.removeAttribute("max");
            }
        }
    }

    sensorLeitura.onchange = atualizarCamposLeitura;
    atualizarCamposLeitura();
}
const filtroFalhas = document.querySelector("[data-filtrar-falhas]");

if (filtroFalhas) {
    filtroFalhas.onchange = function () {
        filtroFalhas.form.submit();
    };
}
