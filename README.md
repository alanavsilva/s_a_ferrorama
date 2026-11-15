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

Para ver os requisitos funcionais, não funcionais, regras de negócio, limitações e roteiro de aceite, consulte [REQUISITOS.md](REQUISITOS.md). Para aprender a usar as telas, consulte [MANUAL.md](MANUAL.md).

## Tecnologias e ambiente

- PHP 8.2 com MySQLi e extensões `mysqli` e `mbstring`.
- MySQL/MariaDB. O ambiente desta versão foi verificado com MariaDB 10.4.
- Apache e XAMPP para executar localmente.
- HTML, CSS e JavaScript.
- Navegador atualizado.

Não há instalação de dependências por Composer ou Node.js. A Home consulta Open-Meteo para o clima e ViaCEP para endereços; ícones e algumas imagens também vêm de serviços externos. Sem internet, essas partes podem ficar indisponíveis.

## Instalação local

1. Instale o XAMPP e copie a pasta do projeto para `C:\xampp\htdocs`.
2. Inicie Apache e MySQL pelo painel do XAMPP.
3. Abra o phpMyAdmin e importe `database/db.sql` uma vez para criar `SA_FERRORAMA` e as tabelas.
4. Copie `infra/conexao.local.example.php` para `infra/conexao.local.php`. Edite host, usuário, senha, banco e porta para corresponder ao MySQL local. O arquivo real fica ignorado pelo Git.
5. Importe `database/usuarios_demo.sql` para criar as contas fictícias de demonstração.
6. Abra a landing page em `http://localhost/<pasta-do-projeto>/public/Tela_landing/landing.php` ou vá direto para `http://localhost/<pasta-do-projeto>/index.php`. Inclua a porta no endereço se o Apache não usar a padrão.

Importe `database/db.sql` em um banco vazio. Ele não recria tabelas existentes e não deve ser usado como forma de limpar um banco com dados. Faça uma cópia antes de qualquer reinicialização.

As contas de demonstração são fictícias e exclusivas para uso local; suas credenciais estão somente no [manual](MANUAL.md), não neste README. Não as reutilize em um sistema publicado.

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
REQUISITOS.md Requisitos funcionais, não funcionais e regras de negócio
MANUAL.md    Guia para quem vai usar o sistema
```

## Integrantes e contribuições registradas

Conforme a divisão inicial descrita em `doc/organizacao-projeto.md`:

- **Alana:** organização da equipe e do projeto, pesquisa sobre CRUD, documentação, licença e apoio à estilização.
- **Piettra:** README e desenvolvimento da página de cadastro, além de apoio à estilização.
- **Nicole:** página de login, estudo da identidade visual e apoio à estilização.

Essa divisão é a registrada no planejamento inicial; confirmem com a equipe se ela ainda representa as contribuições da versão final.

## Capturas da versão final

A atividade pede capturas da interface no README. Elas ainda precisam ser feitas na estação final e adicionadas ao repositório. Não use imagens de mockup como se fossem capturas do sistema executando.

## Preparação da entrega

O código local não confirma a organização física da bancada, o teste com uma pessoa de fora da equipe, o quadro GitHub Projects nem a Release final. Antes da avaliação, a equipe deve seguir o roteiro em [REQUISITOS.md](REQUISITOS.md), testar os dois perfis e registrar as dificuldades encontradas.
