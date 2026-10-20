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

    let nome = document.getElementById("nome").value;
    let email = document.getElementById("email").value;
    let senha = document.getElementById("senha").value;

    if (nome == "" || email == "" || senha == "") {
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

//cadastro sensores

document.addEventListener("DOMContentLoaded", () => {
    const botaoAbrir = document.querySelector(".btn-cadastrar-user");
    const formularioCadastro = document.querySelector(".cadastro-usuario");

    if (botaoAbrir && formularioCadastro) {
        formularioCadastro.style.display = "none";

        botaoAbrir.addEventListener("click", () => {
            const estaFechado = formularioCadastro.style.display === "none";

            formularioCadastro.style.display = estaFechado ? "block" : "none";
        });
    }
});

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