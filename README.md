# Ferrorama — TremTech

O TremTech é uma aplicação web local para organizar a operação de uma ferrovia em escala de Ferrorama. Reúne cadastros de usuários, trens, rotas e sensores, permite registrar falhas e oferece telas de consulta, monitoramento demonstrativo e relatório de falhas.

## Problema e objetivo

Em uma demonstração ferroviária, informações sobre trens, percursos, responsáveis e ocorrências podem ficar espalhadas. O TremTech reúne esses dados em um sistema único, facilita a consulta e ajuda a equipe a explicar os vínculos entre os registros.

## Funcionalidades atuais

- Login, logout e dois perfis de acesso: Administrador e Funcionário.
- CRUD de usuários, trens, rotas e sensores, com pesquisa nas listagens.
- Associação de trens a usuários responsáveis, rotas a trens e sensores a um trem e a uma rota desse mesmo trem.
- Registro manual de falhas, filtro por estado e ações para resolver ou reabrir ocorrências.
- Home com indicadores de cadastros, falhas, clima de Joinville e consulta de CEP.
- Monitoramento demonstrativo com seleção de sensor, valores de exemplo e animação do percurso com ponte e desvios.
- Relatório de falhas por sensor e mês.

### Limites que devem ser explicados na demonstração

O sistema **não recebe dados de sensores físicos**. A velocidade exibida vem da velocidade média cadastrada no trem; a temperatura é um valor demonstrativo. O alerta de velocidade é exibido acima de 100 km/h, mas não cria automaticamente uma falha. A animação do percurso usa um caminho ilustrativo fixo e não desenha a geometria cadastrada na rota.

O relatório atual consulta falhas por sensor e mês; não é um histórico de medições nem gera gráficos/exportações. Clima e consulta de CEP são funções auxiliares da Home e dependem de serviços externos.

Para ver os requisitos funcionais, não funcionais, regras de negócio, limitações e roteiro de aceite, consulte [requisitos.md](requisitos.md). Para aprender a usar as telas, consulte [manual.md](manual.md).

## Tecnologias e ambiente

- PHP 8.2 com MySQLi e extensões `mysqli` e `mbstring`.
- MySQL/MariaDB. O ambiente desta versão foi verificado com MariaDB 10.4.
- Apache e XAMPP para executar localmente.
- HTML, CSS e JavaScript.
- Navegador atualizado.

Não há instalação de dependências por Composer ou Node.js. A Home consulta Open-Meteo para o clima e ViaCEP para endereços; ícones e algumas imagens também vêm de serviços externos. Sem internet, essas partes podem ficar indisponíveis.

## Como executar

O sistema foi desenvolvido para rodar localmente com XAMPP. Instalação, configuração do banco, acesso e contas de demonstração estão no [manual.md](manual.md). As contas são fictícias e exclusivas da demonstração; não as reutilize em um sistema publicado.

## Perfis

| Perfil | O que pode fazer |
| --- | --- |
| Administrador | Cadastrar, consultar, editar e excluir usuários, trens, rotas e sensores; registrar falhas e resolver ou reabrir ocorrências; consultar Home, monitoramento e relatórios. |
| Funcionário | Consultar as informações liberadas, acompanhar falhas e acessar Home, monitoramento e relatórios. Não pode executar ações administrativas. |

As restrições administrativas são verificadas no PHP no servidor. Ocultar um botão na página não é a única proteção.

## Estrutura

```text
assets/      CSS e imagens
database/    Estrutura SQL e contas fictícias de demonstração
doc/         Pesquisas e documentos do projeto
infra/       Autenticação, configuração local do banco e integração de clima
public/      Landing page e telas do sistema separadas por área
script/      Interações e validações da interface
index.php    Login
logout.php   Encerramento da sessão
README.md    Apresentação e instalação
requisitos.md Requisitos funcionais, não funcionais e regras de negócio
manual.md     Guia para quem vai usar o sistema
```

## Integrantes e contribuições registradas

O trabalho foi colaborativo: as integrantes participaram do desenvolvimento e dos ajustes de várias telas, além da identidade visual e da integração da aplicação. Os focos abaixo ajudam a reconhecer contribuições, mas não representam a divisão exclusiva das páginas:

- **Alana:** organização da equipe e da estrutura do projeto, pesquisa sobre CRUD, documentação, licença e contribuição no desenvolvimento e refinamento de várias telas e estilos.
- **Piettra:** elaboração do README, desenvolvimento de telas de cadastro e contribuição em outras telas, na estilização e nos ajustes da aplicação.
- **Nicole:** desenvolvimento da tela de login, estudo da identidade visual e contribuição no desenvolvimento e refinamento de outras telas e estilos.

As tarefas se complementaram ao longo do projeto. A documentação inicial da organização pode ser consultada em `doc/organizacao-projeto.md`.

## Capturas da versão final

A atividade pede capturas da interface no README. Elas ainda precisam ser feitas na estação final e adicionadas ao repositório. Não use imagens de mockup como se fossem capturas do sistema executando.

## Preparação da entrega

O código local não confirma a organização física da bancada, o teste com uma pessoa de fora da equipe, o quadro GitHub Projects nem a Release final. Antes da avaliação, a equipe deve seguir o roteiro em [requisitos.md](requisitos.md), testar os dois perfis e registrar as dificuldades encontradas.

## Requisitos funcionais

RF-01 — O sistema apresenta uma landing page pública com a finalidade do TremTech e acesso ao login.

RF-02 — O sistema autentica usuários cadastrados, mantém a sessão durante o uso e permite logout.

RF-03 — O sistema diferencia Administrador e Funcionário e protege no backend as ações administrativas.

RF-04 — O sistema apresenta na Home os totais de usuários, trens, rotas e sensores, informações de velocidade média, falhas e funções auxiliares de clima e CEP.

RF-05 — O Administrador pode cadastrar, consultar, pesquisar, editar e excluir usuários, respeitando as regras de e-mail e vínculos.

RF-06 — O Administrador pode cadastrar, consultar, pesquisar, editar e excluir trens, associando cada trem a um usuário responsável.

RF-07 — O Administrador pode cadastrar, consultar, pesquisar, editar e excluir rotas, com nome, descrição, distância, duração, data de criação e trem vinculado.

RF-08 — O Administrador pode cadastrar, consultar, pesquisar, editar e excluir sensores, associando-os a um trem e a uma rota desse mesmo trem.

RF-09 — O Administrador pode registrar falhas para sensores, filtrar sua situação e resolver ou reabrir ocorrências; o Funcionário pode acompanhá-las.

RF-10 — O monitoramento permite selecionar sensores, consultar valores demonstrativos e visualizar uma animação ilustrativa de percurso.

RF-11 — O sistema gera na tela um relatório de falhas por sensor e mês, com a situação das ocorrências.

RF-12 — A Home consulta clima de Joinville e permite consultar endereço por CEP usando serviços externos.

RF-13 — Os dados cadastrados são armazenados no MySQL/MariaDB e permanecem disponíveis após atualizar ou reabrir as páginas.
