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

RF-42 — O sistema deve impedir a exclusão de rotas que possuam sensores vinculados.

RF-43 — O sistema deve listar os sensores cadastrados.

RF-44 — O sistema deve permitir que o administrador cadastre sensores.

RF-45 — O sistema deve permitir associar um sensor a um trem e a uma rota desse mesmo trem.

RF-46 — O sistema deve recusar associações entre sensores, trens e rotas incompatíveis.

RF-47 — O sistema deve permitir a busca de sensores por nome, tipo, trem ou rota.

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

RF-60 — O sistema deve exibir uma temperatura demonstrativa para o sensor selecionado.

RF-61 — O sistema deve indicar alerta de velocidade quando a velocidade exibida superar 100 km/h.

RF-62 — O sistema deve atualizar os dados exibidos ao trocar o sensor monitorado.

RF-63 — O sistema deve exibir a rota associada ao sensor monitorado.

RF-64 — O sistema deve mover um marcador sobre a ilustração fixa do trajeto na tela de monitoramento.

RF-65 — O sistema deve permitir selecionar um sensor para consulta de relatório.

RF-66 — O sistema deve permitir selecionar o mês do relatório.

RF-67 — O sistema deve listar as falhas do sensor selecionado no mês escolhido.

RF-68 — O sistema deve exibir nos relatórios a descrição, a data e a situação das falhas.

RF-69 — O sistema deve exibir nos relatórios as quantidades de falhas resolvidas e não resolvidas.

## Regras de negócio

RN-01 — Somente Administradores podem realizar ações administrativas de cadastro, edição, exclusão e gerenciamento de falhas.

RN-02 — Funcionários podem consultar as informações liberadas pelo sistema.

RN-03 — O sistema aceita os perfis Administrador e Funcionário.

RN-04 — O e-mail de cada usuário deve ser válido e único.

RN-05 — A senha cadastrada deve ter pelo menos oito caracteres.

RN-06 — Cada trem deve ter um usuário responsável existente.

RN-07 — A velocidade média de um trem deve estar entre 1 e 600 km/h.

RN-08 — A distância e a duração de uma rota devem ser positivas.

RN-09 — Sensores devem usar os tipos Velocidade, Temperatura ou Status.

RN-10 — A rota associada a um sensor deve pertencer ao trem associado a esse sensor.

RN-11 — Uma nova falha deve começar como não resolvida.

RN-12 — Velocidade acima de 100 km/h deve ser indicada como alerta.

RN-13 — Um alerta de velocidade não deve criar uma falha automaticamente.

RN-14 — O sistema deve impedir exclusões que deixem registros dependentes sem vínculo válido.

RN-15 — O relatório deve considerar as falhas do sensor selecionado dentro do mês escolhido.

## Requisitos não funcionais

RNF-01 — O sistema deve manter o mesmo padrão visual nas telas administrativas.

RNF-02 — O sistema deve adaptar a apresentação das telas a diferentes larguras de computador.

RNF-03 — O sistema deve apresentar seus textos e mensagens em português.

RNF-04 — O sistema deve preservar os vínculos entre os dados cadastrados.

RNF-05 — O sistema deve proteger as senhas dos usuários contra leitura direta no banco de dados.

RNF-06 — O sistema deve apresentar os dados em tabelas com títulos que identifiquem cada informação.

RNF-07 — O sistema deve manter os mesmos nomes para as opções de navegação em todas as telas.

RNF-08 — O sistema deve identificar visualmente as ações de cadastro, edição e exclusão.

RNF-09 — O sistema deve informar quando uma consulta não retornar registros.

RNF-10 — O sistema deve informar quando ocorrer uma falha ao carregar ou salvar dados.