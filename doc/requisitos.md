# Requisitos do TremTech

Este documento reúne, em formato de consulta, os requisitos e as regras identificados na versão atual do sistema. Os status descrevem o código; não substituem a conferência na estação de demonstração.

## Escopo

O TremTech organiza cadastros e consultas de uma ferrovia em escala de Ferrorama. A versão atual inclui gerenciamento de usuários, trens, rotas e sensores; registro e acompanhamento de falhas; relatório de falhas; e monitoramento demonstrativo. Não há leitura de sensores físicos.

## Requisitos funcionais

| Código | Requisito | Situação atual |
| --- | --- | --- |
| RF-01 | Exibir uma landing page pública que apresente o TremTech e ofereça acesso ao login. | Implementado. O percurso é uma ilustração animada fixa. |
| RF-02 | Autenticar usuários cadastrados, manter sessão e permitir logout. | Implementado com verificação de senha por `password_verify`. |
| RF-03 | Diferenciar Administrador e Funcionário e proteger ações administrativas no servidor. | Implementado nos fluxos administrativos revisados. |
| RF-04 | Exibir na Home totais de usuários, trens, rotas e sensores, velocidade média cadastrada, resumo de falhas e funções auxiliares de clima e CEP. | Implementado. Clima e CEP dependem de serviços externos. |
| RF-05 | Permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir usuários, respeitando e-mail e vínculos. | Implementado. Senhas são armazenadas como hash. |
| RF-06 | Permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir trens, associando um usuário responsável. | Implementado. A tela exige responsável; o banco ainda aceita `id_usuario` nulo. |
| RF-07 | Permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir rotas com nome, descrição, distância, duração, data de criação e trem vinculado. | Implementado. A data é definida pelo banco. |
| RF-08 | Permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir sensores, associando-os a um trem e a uma rota desse trem. | Implementado. A relação entre trem e rota é validada no backend. |
| RF-09 | Permitir registrar falhas para sensores, filtrar por situação e resolver ou reabrir ocorrências; permitir ao Funcionário acompanhá-las. | Implementado. Falhas não são editáveis nem excluídas pela tela. |
| RF-10 | Permitir selecionar sensor no monitoramento, consultar valores demonstrativos e visualizar animação do percurso. | Implementado com limite: velocidade vem do cadastro do trem, temperatura é demonstrativa e o traçado é fixo. |
| RF-11 | Exibir relatório de falhas por sensor e mês, com a situação das ocorrências. | Implementado com limite: sem histórico de telemetria, gráficos ou exportação. |
| RF-12 | Consultar clima de Joinville e preencher endereço por CEP na Home. | Implementado com dependência de Open-Meteo e ViaCEP. |
| RF-13 | Manter dados cadastrados disponíveis após atualizar ou reabrir páginas. | Implementado por persistência em MySQL/MariaDB. Backup não é automático. |
| RF-14 | Adaptar as páginas a diferentes larguras de tela. | Há regras CSS responsivas; a conferência em todos os dispositivos depende de teste. |

## Regras de negócio

| Código | Regra |
| --- | --- |
| RN-01 | As telas internas exigem login. Landing page e login são públicos. |
| RN-02 | Apenas Administradores podem cadastrar, editar ou excluir usuários, trens, rotas e sensores, registrar falhas e alterar o estado de uma falha. Funcionários consultam as telas liberadas. |
| RN-03 | Os perfis aceitos são `Administrador` e `Funcionário`. Apenas um Administrador pode cadastrar outro Administrador ou alterar o perfil de uma conta. |
| RN-04 | O e-mail deve ser válido e único. Nova senha deve ter pelo menos oito caracteres e é armazenada como hash. Na edição, senha vazia mantém a atual. |
| RN-05 | A tela não permite excluir contas de Administrador. Antes de excluir um usuário responsável por trem, é necessário tratar o vínculo. |
| RN-06 | O trem exige nome com pelo menos três caracteres, velocidade média entre 1 e 600 km/h e responsável existente. O cadastro não possui tipo/categoria do trem. |
| RN-07 | A rota exige nome entre 3 e 100 caracteres, descrição preenchida de até 255 caracteres, distância maior que zero, duração inteira positiva em minutos e trem existente. O banco define a data de criação. |
| RN-08 | O sensor exige nome entre 3 e 100 caracteres, tipo Velocidade, Temperatura ou Status e vínculos existentes. A rota selecionada deve pertencer ao trem informado. |
| RN-09 | A falha exige descrição entre 5 e 200 caracteres e sensor cadastrado. Uma nova ocorrência inicia como não resolvida e recebe data/hora do banco. |
| RN-10 | Resolver uma falha define `resolvida = 1`; reabrir define `resolvida = 0`. Um alerta de velocidade não cria falha automaticamente. |
| RN-11 | Registros com dependências não devem ser excluídos sem tratar os vínculos: usuário/trem, trem/rota ou sensor, rota/sensor e sensor/falha. A interface ou as chaves estrangeiras impedem exclusões incompatíveis. |
| RN-12 | Velocidade acima de 100 km/h aparece como Alerta; até 100 km/h aparece como Normal. A temperatura demonstrativa varia de 35 °C a 38 °C e aparece como Normal. |
| RN-13 | O traçado, a ponte e os desvios do monitoramento são desenhados por pontos definidos no código. O desenho não é gerado pela geometria da rota cadastrada. |
| RN-14 | O relatório considera um sensor e um mês, do início do mês selecionado até o início do próximo mês. As ocorrências são contadas conforme seu estado atual. |

## Requisitos não funcionais

| Código | Requisito | Situação atual |
| --- | --- | --- |
| RNF-01 | Proteger senhas sem armazená-las em texto puro. | Usa `password_hash` e `password_verify`. |
| RNF-02 | Validar permissões no servidor e proteger operações de escrita. | Usa sessão, perfil e token CSRF nos fluxos administrativos revisados. |
| RNF-03 | Proteger consultas que recebem dados de entrada contra injeção SQL. | Consultas preparadas são usadas nas operações parametrizadas revisadas. |
| RNF-04 | Escapar dados inseridos antes de exibi-los em HTML. | As telas revisadas usam `htmlspecialchars`. |
| RNF-05 | Preservar integridade dos vínculos entre entidades. | Há validações e chaves estrangeiras; `trens.id_usuario` ainda permite nulo no esquema. |
| RNF-06 | Tornar telas, campos e mensagens compreensíveis para consulta e operação. | A interface e o manual oferecem esses elementos; falta validação formal com pessoa externa. |
| RNF-07 | Ajustar o layout para telas de diferentes tamanhos. | Existem breakpoints CSS; falta conferência formal em todos os dispositivos. |
| RNF-08 | Executar em ambiente compatível com PHP, MySQL/MariaDB, Apache e navegador. | Verificado neste projeto com PHP 8.2, MariaDB 10.4 e XAMPP. Outras versões não foram certificadas. |
| RNF-09 | Informar dependências de rede das funções auxiliares e recursos externos. | Clima, CEP e alguns ícones/imagens dependem de internet; cadastros usam serviços locais. |
| RNF-10 | Organizar código e arquivos para facilitar manutenção. | O projeto separa `public/`, `infra/`, `assets/`, `script/`, `database/` e `doc/`. |
| RNF-11 | Persistir dados e permitir proteger cópia antes de alterações no ambiente. | Dados ficam no MySQL/MariaDB; backup e restauração são manuais. |
| RNF-12 | Usar rótulos e marcação que auxiliem identificação de campos e mensagens. | Há rótulos e atributos ARIA em partes da interface; não houve auditoria WCAG. |
| RNF-13 | Responder ao volume previsto para demonstração local. | Destinado a volume reduzido; não foram feitos testes de carga ou medição de capacidade. |

## Conferências pendentes da equipe

Preparar e testar a bancada e seus periféricos, incluir capturas reais do sistema, atualizar o GitHub Projects, identificar a Release usada na apresentação e conduzir um teste com uma pessoa de fora da equipe. Registrar as dificuldades e os ajustes necessários.

## Roteiro de aceite

1. Entrar com as duas contas de demonstração e confirmar o perfil.
2. Como Administrador, cadastrar usuário, trem, rota e sensor e conferir as associações nas listagens.
3. Enviar uma combinação de trem e rota incompatível no cadastro de sensor e confirmar que o backend a recusa.
4. Registrar uma falha, filtrar, resolver e reabrir a ocorrência; conferir o resumo e o relatório mensal.
5. Tentar excluir registros ainda vinculados e confirmar que a exclusão é impedida sem apagar dados dependentes.
6. Como Funcionário, consultar as telas e tentar abrir diretamente uma ação administrativa; o servidor deve negar o acesso.
7. Atualizar as páginas e confirmar que os dados permanecem salvos.
8. Pedir a uma pessoa de fora da equipe para seguir o roteiro sem ajuda dos desenvolvedores e registrar dúvidas e dificuldades.
