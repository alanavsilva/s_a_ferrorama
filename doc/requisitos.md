## Requisitos funcionais

- **RF-01 —** O sistema deve apresentar uma página pública com informações do TremTech e acesso ao login.
- **RF-02 —** O sistema deve autenticar usuários, manter a sessão e permitir logout.
- **RF-03 —** O sistema deve aplicar permissões diferentes para Administradores e Funcionários.
- **RF-04 —** O sistema deve apresentar na Home os totais de usuários, trens, rotas e sensores.
- **RF-05 —** O sistema deve permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir usuários.
- **RF-06 —** O sistema deve permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir trens com um usuário responsável.
- **RF-07 —** O sistema deve permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir rotas vinculadas a trens.
- **RF-08 —** O sistema deve permitir ao Administrador cadastrar, consultar, pesquisar, editar e excluir sensores vinculados a trens e rotas.
- **RF-09 —** O sistema deve permitir ao Administrador registrar, resolver e reabrir falhas; Funcionários devem poder consultá-las.
- **RF-10 —** O sistema deve permitir selecionar um sensor e visualizar informações demonstrativas no monitoramento.
- **RF-11 —** O sistema deve apresentar relatórios de falhas por sensor e mês.
- **RF-12 —** O sistema deve consultar o clima e permitir consulta de endereço por CEP na Home.
- **RF-13 —** O sistema deve salvar os dados cadastrados no banco de dados.
- **RF-14 —** O sistema deve adaptar as páginas a diferentes tamanhos de tela.

## Regras de negócio

- **RN-01 —** O sistema deve exigir login para acessar as telas internas.
- **RN-02 —** Somente Administradores podem realizar ações de cadastro, edição, exclusão e gerenciamento de falhas.
- **RN-03 —** O sistema deve aceitar os perfis Administrador e Funcionário.
- **RN-04 —** O sistema deve validar o nome, o e-mail e a senha dos usuários; o e-mail deve ser único.
- **RN-05 —** O sistema não deve permitir excluir Administradores nem usuários vinculados a trens.
- **RN-06 —** O sistema deve validar o nome, a velocidade e o usuário responsável de cada trem.
- **RN-07 —** O sistema deve validar o nome, a descrição, a distância, o tempo e o trem de cada rota.
- **RN-08 —** O sistema deve aceitar sensores dos tipos Velocidade, Temperatura ou Status e validar os vínculos com trem e rota.
- **RN-09 —** O sistema deve validar a descrição e o sensor associado a cada falha.
- **RN-10 —** O sistema deve iniciar novas falhas como não resolvidas.
- **RN-11 —** O sistema deve impedir exclusões que comprometam registros vinculados.
- **RN-12 —** O sistema deve exibir alerta quando a velocidade do trem ultrapassar 100 km/h.
- **RN-13 —** O sistema deve apresentar no monitoramento o percurso ilustrativo com ponte e desvios.
- **RN-14 —** O sistema deve filtrar o relatório por sensor e mês.

## Requisitos não funcionais

- **RNF-01 —** O sistema deve armazenar senhas protegidas por hash.
- **RNF-02 —** O sistema deve verificar as permissões no servidor.
- **RNF-03 —** O sistema deve usar consultas preparadas para proteger o banco de dados.
- **RNF-04 —** O sistema deve tratar os dados antes de exibi-los nas páginas.
- **RNF-05 —** O sistema deve preservar os vínculos entre usuários, trens, rotas, sensores e falhas.
- **RNF-06 —** O sistema deve apresentar telas e mensagens fáceis de entender.
- **RNF-07 —** O sistema deve se adaptar a diferentes tamanhos de tela.
- **RNF-08 —** O sistema deve funcionar com PHP, MySQL/MariaDB e Apache.
- **RNF-09 —** O sistema deve informar quando funções que dependem de internet estiverem indisponíveis.
- **RNF-10 —** O projeto deve manter organizados os arquivos de telas, infraestrutura, estilos, scripts, banco e documentação.
- **RNF-11 —** O sistema deve manter os dados salvos e permitir cópias de segurança do banco.
- **RNF-12 —** O sistema deve identificar campos e mensagens com rótulos claros.
- **RNF-13 —** O sistema deve atender ao uso previsto para a demonstração local.
