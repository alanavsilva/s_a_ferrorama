function validar_usuario() {
    const nome = document.getElementById("nome")?.value.trim() ?? "";
    const email = document.getElementById("email")?.value.trim() ?? "";
    const senha = document.getElementById("senha")?.value ?? "";

    if (!nome || !email || !senha) {
        alert("Preencha todos os campos.");
        return false;
    }

    if (nome.length < 3) {
        alert("O nome deve ter pelo menos 3 caracteres.");
        return false;
    }

    if (!email.includes("@")) {
        alert("Digite um email válido.");
        return false;
    }

    if (senha.length < 8) {
        alert("A senha deve ter pelo menos 8 caracteres.");
        return false;
    }

    return true;
}

function validar_sensor() {
    const nome = document.getElementById("nome_sensor")?.value.trim() ?? "";
    const tipo = document.getElementById("tipo_dado")?.value ?? "";
    const trem = document.getElementById("id_trem")?.value ?? "";
    const rota = document.getElementById("id_rotas")?.value ?? "";

    if (!nome || !tipo || !trem || !rota) {
        alert("Preencha todos os campos.");
        return false;
    }

    return true;
}

function alternarCadastroUsuario() {
    const formulario = document.getElementById("cadastro");
    if (!formulario) return;

    const abrir = formulario.hidden;
    formulario.hidden = !abrir;
    formulario.classList.toggle("ativo", abrir);

    if (!abrir) {
        const mensagem = formulario.querySelector(".mensagem-cadastro");
        if (mensagem) mensagem.remove();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const botao = document.getElementById("btnAbrirCadastro");
    const formulario = document.getElementById("cadastro");

    if (!botao || !formulario) return;

    botao.addEventListener("click", () => {
        alternarCadastroUsuario();
        botao.setAttribute("aria-expanded", String(!formulario.hidden));
    });
});



const formularioCep = document.getElementById('form_cep');

if (formularioCep) {
    formularioCep.addEventListener('submit', async function (evento) {
        evento.preventDefault();

        const campoCep = document.getElementById('cep');
        const mensagem = document.getElementById('mensagem_cep');
        const cep = campoCep.value.replace(/\D/g, '');

        const campos = ['logradouro', 'bairro', 'cidade', 'uf'];

        function limparCampos() {
            campos.forEach(function (id) {
                document.getElementById(id).value = '';
            });
        }

        limparCampos();

        if (!/^\d{8}$/.test(cep)) {
            mensagem.textContent = 'Informe um CEP com 8 números.';
            return;
        }

        mensagem.textContent = 'Consultando CEP...';

        try {
            const resposta = await fetch(
                'https://viacep.com.br/ws/' + cep + '/json/'
            );

            if (!resposta.ok) {
                throw new Error('Falha na comunicação com a API.');
            }

            const dados = await resposta.json();

            if (dados.erro) {
                mensagem.textContent = 'CEP não encontrado.';
                return;
            }

            document.getElementById('logradouro').value =
                dados.logradouro || '';

            document.getElementById('bairro').value =
                dados.bairro || '';

            document.getElementById('cidade').value =
                dados.localidade || '';

            document.getElementById('uf').value =
                dados.uf || '';

            mensagem.textContent =
                'Endereço encontrado e preenchido com sucesso.';

        } catch (erro) {
            mensagem.textContent =
                'Não foi possível consultar o CEP. Tente novamente.';
        }
    });
}
