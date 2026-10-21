//cadastro sensores
let sensores = [];

function cadastrarSensor() {
    let sensor = {
        nome: document.getElementById("nome").value,
        tipo: document.getElementById("tipo").value,
        trem: document.getElementById("trem").value,
        rota: document.getElementById("rota").value
    };
    sensores.push(sensor); 
    mostrarSensores();
}

//cadastro usuário
function validar_usuario() {
    const nome = document.getElementById("nome").value.trim();
    const email = document.getElementById("email").value.trim();
    const senha = document.getElementById("senha").value;

    if (nome === "" || email === "" || senha === "") {
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

function alternarCadastroUsuario() {
    const formulario = document.getElementById("cadastro");
    if (!formulario) return;

    const mensagem = formulario.querySelector(".mensagem-cadastro");
    const estaAberto = formulario.classList.contains("ativo");

    if (estaAberto) {
        formulario.classList.remove("ativo");
        if (mensagem) mensagem.remove();
    } else {
        formulario.classList.add("ativo");
    }
}

function validar_sensor() {
    const nome = document.getElementById("nome_sensor").value.trim();
    const tipo = document.getElementById("tipo_dado").value;
    const localizacao = document.getElementById("localizacao").value.trim();
    const rota = document.getElementById("id_rota").value;

    if (!nome || !tipo || !localizacao || !rota) {
        alert("Preencha todos os campos.");
        return false;
    }

    return true;
}

document.addEventListener("DOMContentLoaded", () => {
    const botao = document.getElementById("btnAbrirCadastro");
    const formulario = document.getElementById("cadastro");

    if (botao && formulario) {
        botao.addEventListener("click", () => {
            formulario.hidden = !formulario.hidden;
        });
    }
});