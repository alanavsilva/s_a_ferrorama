# Identidade visual do TremTech

Este estudo registra as escolhas visuais aplicadas à versão atual do sistema. Ele descreve o que existe no CSS e nas telas; não afirma que uma auditoria formal de acessibilidade ou usabilidade foi realizada.

## Cores

A interface usa fundo claro, principalmente branco, e tons de vermelho escuro/bordô como cor de destaque. A variável principal do CSS é `--cor-vermelho: #701015`; outros tons escuros aparecem em menus, ícones, títulos e botões. Rosa claro (`#fbe0e3`) aparece em estados destacados e fundos suaves. Cinzas são usados em bordas, tabelas e áreas secundárias.

O resultado buscado é deixar o branco como base e o vermelho como cor secundária de identidade, com destaque suficiente para navegação, ações e informações importantes. O CSS também declara `--cor-petroleo: #35696a`, mas essa variável não é aplicada de forma relevante nas telas atuais.

## Tipografia

O projeto usa a pilha `Arial, Helvetica, sans-serif`. Tamanhos, pesos e cores distinguem títulos, rótulos, mensagens e dados. Não há fonte externa carregada como tipografia principal.

## Componentes e organização

- As telas internas organizam a navegação em menu lateral e usam cabeçalhos, cartões, formulários e tabelas para consulta e gerenciamento.
- A Home autenticada reúne indicadores, atalhos, falhas e funções auxiliares de clima e CEP.
- A landing page pública usa navegação horizontal, apresentação do sistema, explicação resumida, trajeto ilustrado e acesso ao login.
- A identidade usa ícones de trem e ícones de navegação. Alguns ícones e imagens são carregados do serviço Icons8, portanto podem depender de conexão com a internet.
- Estados como seleção do menu, alertas, confirmação e ações de editar/excluir usam cores de destaque para facilitar a identificação.

## Referências e autoria visual

O ClickBus foi registrado pela equipe como referência visual inicial para navegação e apresentação. A interface atual tem marca, conteúdo e organização próprios do TremTech; a referência não deve ser apresentada como uma reprodução do ClickBus.

## Responsividade e acessibilidade

O CSS contém breakpoints para reorganizar menu, cabeçalhos, formulários e cartões em larguras menores. Há rótulos de formulário e atributos ARIA em partes das telas. Isso não equivale a uma certificação de acessibilidade; contraste, teclado e diferentes tamanhos ainda devem ser verificados na estação e no teste com pessoa externa.

## Correspondência com o sistema

As escolhas de branco predominante, bordô/vermelho de destaque, tipografia sem serifa e navegação com menu lateral correspondem ao CSS atual. A descrição antiga de “tons de marrom e branco”, a afirmação de que o layout foi pensado para mostrar baldeações e a lista de seções antigas da landing page não descrevem corretamente a versão atual e foram substituídas por esta documentação.
