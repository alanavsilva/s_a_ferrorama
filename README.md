# Ferrorama — TremTech

O TremTech é uma aplicação web local para organizar a operação de uma ferrovia em escala de Ferrorama. Reúne cadastros de usuários, trens, rotas e sensores, permite registrar falhas e oferece telas de consulta, monitoramento por sensor e percurso animado e relatório de falhas.

## Problema e objetivo

Em uma demonstração ferroviária, informações sobre trens, percursos, responsáveis e ocorrências podem ficar espalhadas. O TremTech reúne esses dados em um sistema único, facilita a consulta e ajuda a equipe a explicar os vínculos entre os registros.

## Tecnologias e ferramentas utilizadas

### Tecnologias do sistema

- **HTML** — estrutura das páginas;
- **CSS** — apresentação visual das telas;
- **JavaScript** — interações e validações realizadas no navegador;
- **PHP** — processamento das funcionalidades e comunicação com o banco de dados;
- **MySQL** — armazenamento dos dados.

### Ambiente e ferramentas de desenvolvimento

- **Apache e XAMPP** — execução local do sistema e do banco de dados;
- **Git e GitHub** — controle de versões e armazenamento do código;
- **GitHub Projects** — organização das tarefas da equipe.

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
public/Tela_landing/landing.php Landing page
logout.php   Encerramento da sessão
README.md    Apresentação e instalação
requisitos.md Requisitos funcionais, não funcionais e regras de negócio
manual.md    Guia para quem vai usar o sistema


## Integrantes e contribuições registradas

- **Alana:** organizou a equipe e a estrutura do projeto, pesquisou sobre CRUD e contribuiu com a documentação, a licença, a estilização e o desenvolvimento de diferentes telas.
- **Piettra:** trabalhou no README, login e nas telas de cadastro e em outras partes da aplicação, além de colaborar com a estilização e os ajustes visuais.
- **Nicole:** desenvolveu a tela de home, pesquisou a identidade visual e também colaborou na criação e nos ajustes de outras telas do sistema.

## Requisitos funcionais

RF-01 — O sistema deve apresentar uma página pública sobre o TremTech.

RF-02 — O sistema deve permitir o acesso à tela de login pela página pública.

RF-03 — O sistema deve exibir uma ilustração animada do percurso ferroviário na página pública.

RF-04 — O sistema deve autenticar usuários por e-mail e senha.

RF-05 — O sistema deve recusar o login quando as credenciais forem inválidas.

RF-06 — O sistema deve encaminhar o usuário autenticado para a página inicial.

RF-07 — O sistema deve encerrar a sessão quando o usuário confirmar o logout.

RF-08 — O sistema deve exigir autenticação para acessar as páginas internas.

RF-09 — O sistema deve restringir operações de cadastro, edição e exclusão a administradores.

RF-10 — O sistema deve permitir que funcionários consultem as informações liberadas pelo sistema.

RF-11 — O sistema deve exibir na página inicial os totais de usuários, trens, rotas e sensores cadastrados.

RF-12 — O sistema deve alternar o trem exibido no cartão de velocidade da página inicial.

RF-13 — O sistema deve consultar informações climáticas para exibição na página inicial.

RF-14 — O sistema deve exibir uma mensagem quando não conseguir consultar o clima.

RF-15 — O sistema deve consultar um endereço a partir de um CEP válido.

RF-16 — O sistema deve preencher os campos de endereço com os dados retornados pela consulta de CEP.

RF-17 — O sistema deve informar quando o CEP for inválido, não for encontrado ou a consulta falhar.

RF-18 — O sistema deve exibir falhas operacionais na página inicial.

RF-19 — O sistema deve filtrar as falhas exibidas por situação.

RF-20 — O sistema deve permitir que o administrador altere a situação de uma falha pela página inicial.

RF-21 — O sistema deve exibir informações de usuários e sensores na página inicial.

RF-22 — O sistema deve listar os usuários cadastrados.

RF-23 — O sistema deve permitir que o administrador cadastre usuários.

RF-24 — O sistema deve recusar o cadastro de usuário com e-mail já existente.

RF-25 — O sistema deve recusar senhas de usuário com menos de oito caracteres.

RF-26 — O sistema deve permitir que o administrador edite usuários cadastrados.

RF-27 — O sistema deve preservar a senha atual quando ela não for informada durante a edição do usuário.

RF-28 — O sistema deve permitir a busca de usuários por nome ou e-mail.

RF-29 — O sistema deve impedir a exclusão de administradores e de usuários vinculados como responsáveis por trens.

RF-30 — O sistema deve listar os trens cadastrados.

RF-31 — O sistema deve permitir que o administrador cadastre trens.

RF-32 — O sistema deve permitir selecionar um usuário cadastrado como responsável por um trem.

RF-33 — O sistema deve permitir que o administrador edite os dados e o responsável de um trem.

RF-34 — O sistema deve permitir a busca de trens por nome ou responsável.

RF-35 — O sistema deve impedir a exclusão de trens que possuam registros vinculados.

RF-36 — O sistema deve listar as rotas cadastradas.

RF-37 — O sistema deve permitir que o administrador cadastre rotas.

RF-38 — O sistema deve permitir vincular uma rota a um trem cadastrado.

RF-39 — O sistema deve registrar a data de criação da rota.

RF-40 — O sistema deve permitir a busca de rotas por nome, descrição ou trem vinculado.

RF-41 — O sistema deve permitir que o administrador edite os dados e o trem vinculado a uma rota.

RF-42 — O sistema deve exibir o trem associado a cada rota cadastrada.

RF-43 — O sistema deve listar os sensores cadastrados.

RF-44 — O sistema deve permitir que o administrador cadastre sensores.

RF-45 — O sistema deve permitir associar um sensor a um trem.

RF-46 — O sistema deve validar o trem selecionado para o sensor.

RF-47 — O sistema deve permitir a busca de sensores por nome, tipo ou trem.

RF-48 — O sistema deve permitir que o administrador edite sensores cadastrados.

RF-49 — O sistema deve impedir a exclusão de sensores que possuam falhas vinculadas.

RF-50 — O sistema deve listar falhas operacionais cadastradas.

RF-51 — O sistema deve permitir que o administrador registre uma falha para um sensor cadastrado.

RF-52 — O sistema deve registrar novas falhas como não resolvidas.

RF-53 — O sistema deve permitir filtrar falhas por situação.

RF-54 — O sistema deve permitir que o administrador marque falhas como resolvidas.

RF-55 — O sistema deve permitir que o administrador reabra falhas resolvidas.

RF-56 — O sistema deve exibir a quantidade de falhas resolvidas e não resolvidas.

RF-57 — O sistema deve permitir que funcionários consultem as falhas sem alterar sua situação.

RF-58 — O sistema deve permitir selecionar um sensor na tela de monitoramento.

RF-59 — O sistema deve exibir a velocidade média do trem associado ao sensor selecionado.

RF-60 — O sistema deve exibir a temperatura calculada para o sensor selecionado.

RF-61 — O sistema deve indicar alerta de velocidade quando a velocidade exibida superar 100 km/h.

RF-62 — O sistema deve atualizar os dados exibidos ao trocar o sensor monitorado.

RF-63 — O sistema deve exibir o trem associado ao sensor monitorado.

RF-64 — O sistema deve mover um marcador sobre a ilustração fixa do trajeto na tela de monitoramento.

RF-65 — O sistema deve permitir selecionar um sensor para consulta de relatório.

RF-66 — O sistema deve permitir selecionar o mês do relatório.

RF-67 — O sistema deve listar as falhas do sensor selecionado no mês escolhido.

RF-68 — O sistema deve exibir nos relatórios a descrição, a data e a situação das falhas.

RF-69 — O sistema deve exibir nos relatórios as quantidades de falhas resolvidas e não resolvidas.

