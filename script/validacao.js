// AÇÕES DAS TELAS. Este arquivo é carregado no fim das páginas.
// Cada parte procura o elemento que precisa usar. Por exemplo, o bloco do CEP só roda na Home, onde existe o formulário de CEP.
// O HTML dá um nome (ID) ao botão e outro ao formulário. getElementById encontra cada um pelo seu nome.
const botao_cadastro = document.getElementById("botao_abrir_cadastro");
const cadastro = document.getElementById("cadastro");

// Este arquivo é usado em várias telas. Só prepara o clique se encontrou o botão e o formulário nesta página.
if (botao_cadastro && cadastro) {
    botao_cadastro.onclick = function () {
        // Ao clicar em Cadastrar, olha se o formulário está escondido.
        // false faz aparecer. true faz sumir.
        if (cadastro.hidden) {
            cadastro.hidden = false;
        } else {
            cadastro.hidden = true;
        }
    };
}

// Este formulário existe na Home. Em outras telas, este bloco é pulado.
const form_cep = document.getElementById("form_cep");

if (form_cep) {
    // Enviar é clicar em Consultar CEP ou apertar Enter dentro do campo. onsubmit roda nos dois casos.
    form_cep.onsubmit = function (evento) {
        // O formulário normalmente recarregaria a Home. Aqui, o código faz a consulta sem recarregar.
        evento.preventDefault();

        // value é o que está escrito na caixa. Exemplo: se digitar 89201000, cep recebe 89201000.
        const cep = document.getElementById("cep").value;
        const mensagem = document.getElementById("mensagem_cep");
        const rua = document.getElementById("logradouro");
        const bairro = document.getElementById("bairro");
        const cidade = document.getElementById("cidade");
        const uf = document.getElementById("uf");

        // value é o texto dentro da caixa. Colocar "" apaga o endereço que estava lá antes.
        rua.value = "";
        bairro.value = "";
        cidade.value = "";
        uf.value = "";

        // length conta o que foi digitado. Se a quantidade estiver errada, innerText coloca o aviso na tela.
        if (cep.length !== 8) {
            mensagem.innerText = "Digite um CEP com 8 números.";
            return;
        }

        // O for olha um caractere por vez. Se achar uma letra ou um hífen,
        // mostra o mesmo aviso e para antes de consultar o ViaCEP.
        // Por exemplo, 89201ABC tem oito caracteres, mas não é um CEP válido. O for encontra a letra e interrompe a busca.
        for (let i = 0; i < cep.length; i = i + 1) {
            if (cep[i] < "0" || cep[i] > "9") {
                mensagem.innerText = "Digite um CEP com 8 números.";
                return;
            }
        }

        mensagem.innerText = "Consultando CEP...";

        // fetch manda o CEP para o serviço ViaCEP pela internet.
        // Para 89201000, o endereço da consulta termina em /ws/89201000/json/. O navegador envia esse pedido ao ViaCEP.
        return fetch("https://viacep.com.br/ws/" + cep + "/json/")
            // O primeiro then recebe a resposta do ViaCEP quando ela chegar.
            .then(function (resposta) {
                // json() lê a resposta e separa os dados que vieram, como rua e bairro.
                return resposta.json();
            })
            // Quando os dados estiverem prontos, este then preenche as caixas de endereço.
            .then(function (endereco) {
                // O ViaCEP pode responder mesmo sem encontrar o CEP. Nesse caso, ele avisa com endereco.erro e os campos ficam vazios.
                if (endereco.erro) {
                    mensagem.innerText = "CEP não encontrado.";
                    return;
                }

                rua.value = endereco.logradouro;
                bairro.value = endereco.bairro;
                cidade.value = endereco.localidade;
                // Por exemplo, se a resposta vier com SC, uf.value escreve SC no campo de estado.
                uf.value = endereco.uf;
                mensagem.innerText = "Endereço encontrado.";
            })
            // Se o pedido falhar, por exemplo sem internet, mostra este aviso.
            .catch(function () {
                mensagem.innerText = "Erro ao consultar o CEP.";
            });
    };
}

// Troca o nome e a velocidade do trem a cada três segundos.
const trens = document.getElementById("velocidades_trens");
// O índice é a posição na lista. 0 é o primeiro trem, 1 é o segundo e assim por diante.
let indice = 0;

function mostrar_velocidade() {
    // A Home tem uma lista escondida com os trens. indice indica qual deles será mostrado agora.
    // Dentro de cada item, a primeira parte tem o nome e a segunda tem a velocidade.
    const trem_atual = trens.children[indice];
    const campos = trem_atual.children;
    // textContent lê o nome e a velocidade mesmo com a lista escondida.
    document.getElementById("velocidade_titulo").innerText = "Velocidade média - " + campos[0].textContent;
    document.getElementById("velocidade_valor").innerText = campos[1].textContent;

    // A lista começa no número 0. Somar 1 escolhe o próximo trem; voltar a 0 escolhe o primeiro.
    indice = indice + 1;
    // Exemplo: se existem dois trens, as posições são 0 e 1. Quando o índice chega a 2, ele volta para 0.
    if (indice === trens.children.length) {
        indice = 0;
    }
}

if (trens && trens.children.length > 0) {
    mostrar_velocidade();
    // setInterval chama mostrar_velocidade de novo a cada 3 segundos.
    setInterval(mostrar_velocidade, 3000);
}

// Cada lixeira tem um formulário com o ID do registro que pode ser excluído.
const formularios_exclusao = document.getElementsByClassName("confirmar_exclusao");
const janela_exclusao = document.getElementById("janela_exclusao");

if (janela_exclusao) {
    let formulario_escolhido;

    for (let i = 0; i < formularios_exclusao.length; i = i + 1) {
        formularios_exclusao[i].onsubmit = function (evento) {
            // Segura o envio e guarda o formulário da lixeira que a pessoa clicou.
            evento.preventDefault();
            formulario_escolhido = formularios_exclusao[i];
            janela_exclusao.showModal();
        };
    }

    // Cancelar só fecha a janela. Nenhum pedido de exclusão é enviado.
    document.getElementById("cancelar_exclusao").onclick = function () {
        janela_exclusao.close();
    };

    // Só quando a pessoa confirma, o formulário envia o ID escolhido ao PHP.
    document.getElementById("confirmar_exclusao").onclick = function () {
        janela_exclusao.close();
        formulario_escolhido.submit();
    };
}

// O link Sair e a janela de confirmação estão no HTML da página. Aqui ligamos os cliques aos botões.
const link_sair = document.getElementById("link_sair");
const janela_saida = document.getElementById("janela_saida");

if (link_sair && janela_saida) {
    link_sair.onclick = function (evento) {
        evento.preventDefault();
        // Impede a saída imediata e abre a janela de confirmação do menu.
        janela_saida.showModal();
    };

    // Continuar no sistema fecha só a janela. A pessoa fica na mesma página e continua logada.
    document.getElementById("cancelar_saida").onclick = function () {
        janela_saida.close();
    };

    document.getElementById("confirmar_saida").onclick = function () {
        // Só depois de confirmar, abre o endereço que encerra a sessão.
        window.location.href = link_sair.href;
    };
}

// Procura o formulário da tela atual. Ao mudar a escolha, manda os valores para o PHP carregar a lista.
const filtro = document.getElementById("filtros_monitoramento") || document.getElementById("filtro_falhas");

if (filtro) {
    // Ao trocar uma opção, onchange roda e submit envia a escolha ao PHP.
    filtro.onchange = function () {
        // Exemplo: se a pessoa escolhe Resolvida, o formulário envia essa opção e a página mostra as falhas daquele grupo.
        filtro.submit();
    };
}

// Se escolher um sensor de Status, aparece Descrição. Para os outros tipos, aparece Valor.
const sensor = document.getElementById("id_sensor");
const campo_valor = document.getElementById("campo_valor");

if (sensor && campo_valor) {
    sensor.onchange = mostrar_campo_leitura;
    // Também chama a função logo que a página abre. Assim, os campos ficam certos se já existir um sensor selecionado.
    mostrar_campo_leitura();
}

function mostrar_campo_leitura() {
    // Cada opção guarda no HTML o tipo do sensor. dataset.tipo lê esse texto quando a pessoa escolhe um sensor.
    const tipo = sensor.options[sensor.selectedIndex].dataset.tipo;
    const valor = document.getElementById("valor");
    const unidade = document.getElementById("unidade");
    const descricao = document.getElementById("descricao_falha");
    const botao = document.getElementById("botao_salvar_informacao");

    const campo_descricao = document.getElementById("campo_descricao_status");

    if (tipo === "Status") {
        // Para uma falha, esconde o número e pede uma descrição.
        campo_valor.hidden = true;
        valor.disabled = true;
        valor.required = false;
        campo_descricao.hidden = false;
        descricao.required = true;
        botao.innerText = "CADASTRAR FALHA";
        return;
    }

    // Para uma leitura, mostra o número e esconde a descrição.
    campo_valor.hidden = false;
    valor.disabled = false;
    valor.required = true;
    campo_descricao.hidden = true;
    descricao.required = false;
    botao.innerText = "SALVAR LEITURA";

    // O texto ao lado de Valor mostra a unidade: km/h para velocidade ou °C para temperatura.
    // max é o maior valor aceito pelo campo. O PHP também confere o valor quando recebe o formulário.
    if (tipo === "Velocidade") {
        unidade.innerText = "(km/h)";
        valor.max = 600;
    } else if (tipo === "Temperatura") {
        unidade.innerText = "(°C)";
        valor.max = 150;
    } else {
        unidade.innerText = "";
        valor.max = "";
    }
}

