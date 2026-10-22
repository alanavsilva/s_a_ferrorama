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