# Requisitos do TremTech — visão da versão atual

Este documento descreve o que a versão atual do TremTech faz e as regras que ela aplica. Foi escrito a partir da leitura das telas PHP, scripts, configuração e estrutura SQL do projeto, considerando também a etapa final da Situação de Aprendizagem Ferrorama.

## Escopo e situação

O TremTech apoia a demonstração e o gerenciamento de uma ferrovia em escala de Ferrorama. Seu escopo atual inclui cadastros, consulta de dados, registro manual de falhas, relatórios de falhas e uma tela demonstrativa de monitoramento.

Os estados abaixo descrevem o código, não substituem testes da equipe na estação final:

- **Implementado:** há fluxo de interface, processamento no servidor e acesso ao banco para a função descrita.
- **Implementado com limite:** a função existe, mas apresenta a limitação registrada neste documento.
- **Depende da equipe:** exige configuração, validação física, atualização externa ou teste com uma pessoa fora do grupo.

O projeto possui dois perfis: **Administrador** e **Funcionário**. Não há um terceiro perfil chamado “Usuário padrão”.

## Requisitos funcionais

| ID | Requisito | Situação e observação |
| --- | --- | --- |
| RF-01 | Apresentar uma página pública que explique o Ferrorama e permita abrir o login. | Implementado na landing page. O percurso nela é uma ilustração animada fixa. |
| RF-02 | Permitir entrar com e-mail e senha cadastrados e encerrar a sessão. | Implementado. Senhas são verificadas com `password_verify`; o logout encerra a sessão. |
| RF-03 | Distinguir Administrador e Funcionário, protegendo ações administrativas também no servidor. | Implementado nos cadastros e endpoints administrativos. Funcionários consultam as telas liberadas, sem executar as operações administrativas. |
| RF-04 | Exibir no início totais de usuários, trens, rotas e sensores, uma velocidade média de trem, clima, consulta de CEP e falhas. | Implementado. Clima e CEP dependem de serviços externos; os indicadores de cadastro são contagens do banco. |
| RF-05 | Cadastrar, consultar, pesquisar, editar e excluir usuários. | Implementado. A pesquisa aceita nome ou e-mail. O e-mail é único; a senha é gravada como hash. A exclusão segue as regras de vínculo. |
| RF-06 | Cadastrar, consultar, pesquisar, editar e excluir trens, associando um usuário responsável. | Implementado na tela. O cadastro/edição pede nome, velocidade média e responsável. O banco atual ainda permite `id_usuario` nulo; consulte a observação de integridade abaixo. |
| RF-07 | Cadastrar, consultar, pesquisar, editar e excluir rotas, vinculando-as a um trem. | Implementado. A rota tem nome, descrição, distância, duração em minutos e data de criação automática. |
| RF-08 | Cadastrar, consultar, pesquisar, editar e excluir sensores, associando-os a trem e rota. | Implementado. Tipo de dado: Velocidade, Temperatura ou Status. A rota deve pertencer ao trem selecionado. |
| RF-09 | Registrar falhas, consultar por estado e permitir resolver ou reabrir ocorrências. | Implementado. A falha é registrada manualmente para um sensor; Administradores fazem mudanças de estado, Funcionários acompanham. Não há edição de descrição nem exclusão de falhas pela interface. |
| RF-10 | Mostrar informações do sensor selecionado e uma representação visual do percurso. | Implementado com limite: velocidade, temperatura e rota exibidas são demonstrativas; o desenho do trajeto não é calculado a partir da geometria cadastrada. Não há leitura de hardware. |
| RF-11 | Gerar relatório de falhas por sensor e mês, mostrando ocorrências resolvidas e abertas. | Implementado com limite: o relatório consulta falhas; não gera histórico de telemetria, gráficos de medições ou arquivos exportáveis. |
| RF-12 | Consultar clima de Joinville e preencher endereço a partir de um CEP na Home. | Implementado com dependência externa. Clima usa Open-Meteo; CEP usa ViaCEP. Se a internet/API falhar, a função correspondente pode não apresentar dados. |
| RF-13 | Manter cadastros e falhas salvos no MySQL entre acessos. | Implementado pelo banco relacional e pelas operações PHP. A restauração de backup deve ser feita pela equipe; não há rotina automática de backup. |
| RF-14 | Apresentar navegação adaptada a diferentes larguras de tela. | Implementado por regras CSS responsivas; a cobertura de dispositivos precisa ser confirmada na estação e no teste de uso. |

## Regras de negócio

| ID | Regra |
| --- | --- |
| RN-01 | Somente pessoas autenticadas acessam as telas internas. A landing page e o login são públicos. |
| RN-02 | Apenas Administradores podem criar, editar ou excluir usuários, trens, rotas e sensores, além de registrar falhas ou mudar seu estado. Essa permissão é validada no backend. |
| RN-03 | Funcionários podem consultar as informações liberadas pelo sistema, incluindo acompanhamento de falhas, monitoramento e relatórios. Eles não podem efetuar as ações administrativas. |
| RN-04 | Os perfis aceitos são `Administrador` e `Funcionário`. A criação/alteração de perfil é uma ação administrativa. |
| RN-05 | O e-mail de usuário deve ser válido e não pode se repetir. Senhas novas devem ter pelo menos oito caracteres e são armazenadas com hash. |
| RN-06 | Um Administrador não pode excluir uma conta de Administrador pela tela de usuários. Um usuário associado como responsável a um trem não pode ser excluído até que o vínculo seja alterado. |
| RN-07 | O cadastro de trem exige responsável, nome com pelo menos três caracteres e velocidade média entre 1 e 600 km/h. O tipo de trem não faz parte do cadastro atual. |
| RN-08 | A rota exige nome entre 3 e 100 caracteres, descrição não vazia de até 255 caracteres, distância maior que zero, duração inteira positiva em minutos e trem existente. A data de criação é definida pelo banco. |
| RN-09 | O sensor exige nome entre 3 e 100 caracteres, um dos três tipos previstos, trem existente e rota existente pertencente ao mesmo trem. |
| RN-10 | A falha exige descrição entre 5 e 200 caracteres e sensor cadastrado. Ao criar, começa como não resolvida e recebe data/hora do banco. |
| RN-11 | Resolver uma falha marca `resolvida = 1`; reabri-la marca `resolvida = 0`. A falha não é gerada automaticamente quando o monitoramento exibe um alerta. |
| RN-12 | A exclusão preserva os vínculos: usuário responsável, trem com rota/sensor, rota com sensor e sensor com falha não devem ser removidos enquanto houver dependências. A interface impede alguns casos antes do `DELETE`; as chaves estrangeiras do banco bloqueiam os demais. |
| RN-13 | O monitoramento usa a velocidade média registrada no trem. Valores acima de 100 km/h são apresentados como alerta; valores até 100 km/h aparecem como normais. |
| RN-14 | A temperatura do monitoramento é demonstrativa, varia entre 35 °C e 38 °C conforme o sensor exibido e aparece com estado Normal. Ela não é uma leitura real nem aciona falha. |
| RN-15 | O percurso animado é uma linha pré-definida com ponte e desvios. A tela mostra o nome da rota vinculada ao sensor, mas a forma desenhada não vem dos campos da rota. |
| RN-16 | O relatório usa o sensor escolhido e o mês informado; considera o início do mês até o início do mês seguinte e conta cada falha como resolvida ou aberta conforme seu estado atual. |

## Requisitos não funcionais

| ID | Requisito | Situação atual |
| --- | --- | --- |
| RNF-01 — Segurança de senha | Não guardar senha em texto puro; validar a senha no login por hash. | Implementado com `password_hash` e `password_verify`. |
| RNF-02 — Acesso | Proteger operações administrativas no servidor e reduzir requisições indevidas. | Implementado com sessão, verificação de perfil e tokens CSRF nas ações de escrita protegidas. |
| RNF-03 — Proteção de consultas | Evitar injeção SQL em valores recebidos do usuário. | Consultas com parâmetros são usadas nos fluxos de login, pesquisa e escrita. As consultas sem parâmetros são fixas no código. |
| RNF-04 — Proteção de saída | Evitar que valores cadastrados sejam interpretados como HTML executável. | As telas usam escape de saída para valores exibidos. |
| RNF-05 — Integridade | Manter relações entre usuários, trens, rotas, sensores e falhas. | Há chaves estrangeiras e validações no backend. A coluna `trens.id_usuario` ainda aceita `NULL` no SQL, embora os formulários exijam responsável; alinhar a estrutura da base existente antes de considerar a regra garantida pelo banco. |
| RNF-06 — Usabilidade | Usar rótulos claros, mensagens de erro/sucesso e navegação consistente em português. | Implementado na interface; a facilidade para iniciantes ainda depende do teste externo pedido pela atividade. |
| RNF-07 — Responsividade | Adaptar o layout para telas menores e maiores. | Há breakpoints no CSS; não há evidência de teste formal em todos os tamanhos. |
| RNF-08 — Compatibilidade de execução | Executar em ambiente local com PHP, MySQL/MariaDB e navegador. | Verificado neste projeto com PHP 8.2 e MariaDB 10.4 via XAMPP. Outras versões não foram certificadas. |
| RNF-09 — Dependências externas | Informar quando uma função depende de internet. | Clima, CEP e alguns ícones/imagens usam serviços externos; o restante do sistema depende do Apache e MySQL locais. |
| RNF-10 — Manutenibilidade | Separar telas por área e centralizar autenticação e configuração do banco. | Estrutura organizada em `public/`, `infra/`, `database/`, `assets/`, `script/` e `doc/`. A configuração com credenciais locais fica fora do Git. |
| RNF-11 — Continuidade dos dados | Preservar registros entre reinicializações normais da aplicação. | Os registros ficam no MySQL. Não há backup/restauração automática; a equipe deve exportar a base antes de reinstalar ou limpar o banco. |
| RNF-12 — Acessibilidade | Permitir identificar campos e mensagens importantes. | Existem rótulos e atributos semânticos/ARIA em partes da interface; não foi feita auditoria formal de acessibilidade. |
| RNF-13 — Desempenho | Responder ao uso previsto na demonstração local. | O projeto é voltado a uma demonstração com volume pequeno/médio. Não há teste de carga nem limite de usuários medido. |

## Limites e pendências da entrega

- Os valores do monitoramento não vêm de sensores físicos. A atividade final permite demonstrar as funcionalidades e as regras previstas, mas a equipe deve explicar claramente essa limitação.
- A Home depende de Open-Meteo e ViaCEP para os blocos de clima e CEP; isso não substitui os dados operacionais do Ferrorama.
- O relatório atual é de falhas por sensor e mês, não um relatório completo de telemetria.
- A base SQL aceita `id_usuario` nulo em `trens`; as telas atuais exigem responsável. Se a regra precisar valer também em importações/manutenção direta do banco, ajustar a coluna e tratar dados existentes antes da alteração.
- O teste de PHP não substitui o checklist de CRUD com dois perfis nem o teste de uso por alguém de fora da equipe.
- Preparação da bancada, periféricos, atualização do GitHub Projects, Release final, screenshots atuais e teste externo precisam ser conferidos pela equipe antes da avaliação.

## Roteiro de aceite sugerido

1. Entrar com as duas contas de demonstração e confirmar que o perfil exibido é o esperado.
2. Com Administrador, cadastrar um usuário, trem, rota e sensor; confirmar as associações nas listagens.
3. Tentar cadastrar um sensor com rota de outro trem e confirmar que o backend recusa a combinação.
4. Criar uma falha, filtrá-la, resolvê-la e reabri-la; conferir os totais e o relatório por mês.
5. Tentar excluir registros que ainda têm dependências e confirmar que a relação é explicada sem apagar dados vinculados.
6. Com Funcionário, consultar as telas e tentar acessar diretamente uma ação administrativa; o servidor deve negar a operação.
7. Atualizar as páginas e confirmar que os registros continuam no banco.
8. Pedir a uma pessoa fora da equipe para repetir o roteiro sem instrução dos desenvolvedores e anotar dificuldades.
