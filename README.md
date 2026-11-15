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

## Tecnologias e ambiente

- PHP
- MySQL.
- Apache e XAMPP.
- HTML.
- CSS.
- JavaScript.

## Perfis

| Perfil | O que pode fazer |
| --- | --- |
| Administrador | Cadastrar, consultar, editar e excluir usuários, trens, rotas e sensores; registrar falhas e resolver ou reabrir ocorrências; consultar Home, monitoramento e relatórios. |
| Funcionário | Consultar as informações liberadas, acompanhar falhas e acessar Home, monitoramento e relatórios. Não pode executar ações administrativas. |


## Estrutura

assets/      CSS e imagens
database/    Estrutura SQL
doc/         Pesquisas e documentos do projeto
infra/       Autenticação, configuração local do banco
public/      Landing page e telas do sistema separadas por área
script/      Interações e validações da interface
index.php    Login
logout.php   Encerramento da sessão
README.md    Apresentação e instalação
requisitos.md Requisitos funcionais, não funcionais e regras de negócio
manual.md    Guia para quem vai usar o sistema

## Integrantes e contribuições registradas

- **Alana:** organizou a equipe e a estrutura do projeto, pesquisou sobre CRUD e contribuiu com a documentação, a licença, a estilização e o desenvolvimento de diferentes telas.
- **Piettra:** trabalhou no README, login e nas telas de cadastro e em outras partes da aplicação, além de colaborar com a estilização e os ajustes visuais.
- **Nicole:** desenvolveu a tela de home, pesquisou a identidade visual e também colaborou na criação e nos ajustes de outras telas do sistema.
