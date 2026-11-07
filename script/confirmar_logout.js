(() => {
    const links = document.querySelectorAll('[data-confirmar-logout]');
    if (links.length === 0) return;

    const dialog = document.createElement('dialog');
    dialog.className = 'modal-confirmacao';
    dialog.setAttribute('aria-labelledby', 'tituloConfirmarSaida');

    const icone = document.createElement('div');
    icone.className = 'modal-confirmacao-icone';
    icone.setAttribute('aria-hidden', 'true');
    icone.textContent = '?';

    const titulo = document.createElement('h2');
    titulo.id = 'tituloConfirmarSaida';
    titulo.textContent = 'Sair do sistema?';

    const mensagem = document.createElement('p');
    mensagem.className = 'modal-confirmacao-mensagem';
    mensagem.textContent = 'Tem certeza de que deseja sair da sua conta?';

    const acoes = document.createElement('div');
    acoes.className = 'modal-confirmacao-acoes';

    const cancelar = document.createElement('button');
    cancelar.type = 'button';
    cancelar.className = 'modal-btn-cancelar';
    cancelar.textContent = 'Continuar no sistema';

    const sair = document.createElement('button');
    sair.type = 'button';
    sair.className = 'modal-btn-sair';
    sair.textContent = 'Sair';

    acoes.append(cancelar, sair);
    dialog.append(icone, titulo, mensagem, acoes);
    document.body.append(dialog);

    let linkSelecionado = null;

    links.forEach((link) => {
        link.addEventListener('click', (evento) => {
            evento.preventDefault();
            linkSelecionado = link;
            dialog.showModal();
        });
    });

    cancelar.addEventListener('click', () => dialog.close());
    sair.addEventListener('click', () => {
        if (linkSelecionado) window.location.href = linkSelecionado.href;
    });

    dialog.addEventListener('close', () => {
        linkSelecionado = null;
    });

    dialog.addEventListener('click', (evento) => {
        if (evento.target === dialog) dialog.close();
    });
})();
