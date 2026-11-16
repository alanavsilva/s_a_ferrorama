# Manual do usuário — TremTech

**Sistema de monitoramento e gerenciamento ferroviário**  
**Versão:** 1.0  
**Equipe:** Alana Veiga da Silva, Nicole Beloni da Silva e Piettra Nicole Cidral

Este manual explica, passo a passo e sem exigir conhecimentos técnicos, como entrar no TremTech, consultar as informações e realizar as tarefas permitidas para cada perfil.
## 1. Conheça o sistema

O TremTech organiza informações de usuários, trens, rotas, sensores e falhas operacionais. A página pública apresenta o projeto. Depois do login, o menu leva às telas internas.

O sistema possui dois perfis:

- **Administrador:** pode cadastrar, consultar, editar e excluir registros conforme as regras de cada tela. Também pode registrar falhas e alterar seu estado.
- **Funcionário:** pode consultar as informações disponibilizadas, acompanhar falhas, usar o monitoramento e consultar relatórios. Não pode fazer alterações administrativas.

As opções do menu e os botões disponíveis podem variar conforme o perfil conectado.

## 2. Entrar no sistema

1. Abra o endereço do TremTech fornecido pela equipe.
2. Na tela de login, digite seu **e-mail** e sua **senha**.
3. Clique em **Entrar**.
4. Se os dados estiverem corretos, a página inicial será aberta.
## 3. Usar a página inicial

Depois do login, a **Home** mostra um resumo do sistema e atalhos para outras telas.

- Os cartões mostram a quantidade de usuários, rotas, sensores e trens cadastrados.
- O cartão de velocidade percorre os trens cadastrados. Ele troca o trem exibido periodicamente e mostra a velocidade média registrada no cadastro.
- A área de clima consulta informações externas para Joinville. Se a consulta não estiver disponível, a página pode apresentar uma mensagem de erro.
- A consulta de CEP aceita oito números e, quando encontra o endereço, preenche logradouro, bairro, cidade e estado.
- A área de falhas permite consultar ocorrências por situação. O Administrador também pode resolvê-las ou reabri-las.
- Os atalhos de cadastro aparecem para o Administrador. Para o Funcionário, aparecem opções de consulta compatíveis com seu perfil.

Para abrir outra tela, clique no nome correspondente no menu: **Home**, **Usuários**, **Sensores**, **Trens**, **Rotas**, **Monitoramento**, **Falhas** ou **Relatórios**.
## 4. Consultar e gerenciar usuários

A tela **Usuários** mostra as contas cadastradas. O Administrador também pode cadastrar, editar e excluir usuários.

### Cadastrar um usuário

1. Abra **Usuários**.
2. Clique em **Cadastrar usuário** para abrir o formulário.
3. Informe o nome, o e-mail, a senha e o perfil.
4. Clique no botão de cadastro do formulário.
5. Confira a mensagem e procure o novo registro na lista.

O e-mail deve ser válido e não pode estar cadastrado em outra conta. A senha deve ter pelo menos oito caracteres.

### Buscar, editar ou excluir

- Para buscar, digite o nome ou o e-mail no campo **Buscar** e clique em **Buscar**.
- Para editar, use o ícone de lápis na linha da pessoa. Se não quiser trocar a senha, deixe o campo de nova senha vazio.
- Para excluir, use o ícone de lixeira e confirme a ação.

O sistema não permite excluir um Administrador pela tela. Também impede excluir um usuário que ainda seja responsável por um trem. Nesse caso, primeiro edite o trem e escolha outro responsável.

Funcionários podem consultar os registros, mas não veem nem executam as ações administrativas.
## 5. Consultar e gerenciar trens

A tela **Trens** apresenta os trens cadastrados. Para cada trem, o sistema registra o nome, a velocidade média e o usuário responsável.

### Cadastrar um trem

1. Abra **Trens**.
2. Clique em **Cadastrar trem**.
3. Informe o nome e a velocidade média em km/h.
4. Escolha um usuário já cadastrado como responsável.
5. Envie o formulário e confira o resultado na lista.

A velocidade aceita valores de 1 a 600 km/h. O formulário não possui campo de categoria ou tipo de trem.

### Buscar, editar ou excluir

- Use a busca por nome ou responsável para localizar um trem.
- Use o ícone de lápis para alterar os dados ou trocar o responsável.
- Use a lixeira e confirme para excluir.

Um trem que esteja ligado a uma rota ou sensor não pode ser removido enquanto houver registros dependentes. Altere ou remova os vínculos necessários antes de tentar novamente.

## 6. Consultar e gerenciar rotas

A tela **Rotas** mostra as rotas e o trem relacionado a cada uma.

### Cadastrar uma rota

1. Abra **Rotas**.
2. Clique em **Cadastrar rota**.
3. Informe o nome e a descrição.
4. Informe a distância em quilômetros e o tempo previsto em minutos.
5. Escolha um trem cadastrado e envie o formulário.
6. Confira a rota na lista. A data de criação é registrada pelo sistema.

A distância e o tempo devem ser maiores que zero. A rota precisa estar vinculada a um trem existente.

### Buscar, editar ou excluir

- Digite o nome da rota no campo de busca e clique em **Buscar**.
- Use o lápis para editar as informações ou trocar o trem relacionado.
- Use a lixeira e confirme para excluir.

Uma rota com sensores vinculados não pode ser excluída. O vínculo precisa ser resolvido antes.

## 7. Consultar e gerenciar sensores

A tela **Sensores** apresenta os sensores cadastrados e seus vínculos com trem e rota.

### Cadastrar um sensor

1. Abra **Sensores**.
2. Clique em **Cadastrar sensor**.
3. Informe o nome e escolha o tipo de dado: **Velocidade**, **Temperatura** ou **Status**.
4. Escolha um trem.
5. Escolha uma rota pertencente ao mesmo trem.
6. Envie o formulário e confira a lista.

A combinação de trem e rota precisa corresponder. Se escolher uma rota de outro trem, o sistema não aceitará o cadastro.

### Buscar, editar ou excluir

- Busque pelo nome, tipo, trem ou rota.
- Use o lápis para alterar os dados ou os vínculos, mantendo a correspondência entre trem e rota.
- Use a lixeira e confirme para excluir.

Um sensor ligado a uma falha não pode ser excluído, pois a falha usa esse registro como referência.
 ## 8. Registrar e acompanhar falhas

A tela **Falhas** reúne ocorrências registradas. Uma falha precisa estar relacionada a um sensor.

### Registrar uma falha — Administrador

1. Abra **Falhas**.
2. Clique em **Cadastrar falha**.
3. Escolha o sensor relacionado.
4. Escreva a descrição da ocorrência, com pelo menos cinco e no máximo duzentos caracteres.
5. Envie o formulário.

A falha começa como **Não resolvida** e recebe a data e o horário do registro. As falhas são registradas manualmente; um alerta de velocidade não cria uma falha sozinho.

### Consultar e alterar a situação

- Use o filtro para ver todas as falhas, somente as resolvidas ou somente as não resolvidas.
- O Administrador pode clicar em **Marcar como resolvida** para concluir uma ocorrência.
- Para uma ocorrência já resolvida, o Administrador pode clicar em **Reabrir**.
- O Funcionário pode consultar e acompanhar as falhas, mas não pode registrar nem alterar sua situação.

## 9. Acompanhar o monitoramento

1. Abra **Monitoramento**.
2. Escolha um sensor na lista, se quiser consultar um específico.
3. Observe os cartões de velocidade e temperatura, o estado operacional e o desenho do percurso.
4. Consulte a lista de falhas apresentada na mesma tela. O Administrador pode resolver ou reabrir ocorrências por ali.

**Importante:** o sistema não recebe dados de sensores físicos. A velocidade mostrada vem da velocidade média cadastrada para o trem relacionado. A temperatura é demonstrativa e varia entre 35 °C e 38 °C. O estado da velocidade aparece como **Alerta** acima de 100 km/h e como **Normal** até esse valor. O percurso com ponte e desvios é uma animação ilustrativa fixa; não representa a posição real de um trem nem é montado a partir da rota cadastrada.

Esses dados servem para demonstrar a tela. Não os use como informação real para tomar decisões sobre uma operação ferroviária.

## 10. Consultar relatórios

1. Abra **Relatórios**.
2. Escolha um sensor cadastrado.
3. Escolha o mês que deseja consultar.
4. Clique em **Gerar relatório**.
5. Confira o total de falhas, as quantidades resolvidas e não resolvidas e a lista de ocorrências do período.

O relatório reúne falhas do sensor escolhido no mês selecionado. Ele é exibido na tela; o sistema não salva um arquivo de relatório nem apresenta um histórico de relatórios gerados.
## 11. Sair do sistema

Clique em **Logout** no menu ou no cabeçalho. Quando aparecer a confirmação, escolha a opção para sair. A sessão será encerrada e o sistema voltará para a tela de login.

Em computadores compartilhados, sempre faça logout ao terminar de usar o sistema.
## 12. Se algo não funcionar

- **Não consigo entrar:** confira o e-mail e a senha. Se o problema continuar, procure o Administrador.
- **Não aparece nenhum registro:** a lista pode estar vazia ou o filtro de busca pode não encontrar correspondências. Limpe a busca e tente novamente.
- **Não consigo cadastrar ou editar:** essas ações são exclusivas do Administrador. Confira também se os dados obrigatórios foram preenchidos.
- **Não consigo excluir:** o registro pode estar relacionado a outros dados. Veja as orientações de exclusão da tela correspondente.
- **Não consigo vincular uma rota ao sensor:** confira se a rota pertence ao trem que selecionou.
- **Clima ou CEP não carrega:** confira a conexão com a internet e tente novamente mais tarde. Essas consultas externas não impedem o uso dos cadastros principais.
- **Aparece erro de banco de dados ou a página não abre:** peça ajuda à pessoa responsável pela instalação do TremTech.
- **Os dados de monitoramento parecem estranhos:** lembre-se de que velocidade e temperatura são demonstrativas e não vêm de sensores físicos.
## 13. Dicas de uso

- Confira os dados antes de salvar alterações.
- Verifique o registro selecionado antes de confirmar uma exclusão.
- Escolha vínculos corretos entre usuários, trens, rotas e sensores.
- Não compartilhe sua senha.
- Use somente as ações disponíveis para seu perfil.
- Trate o monitoramento e o percurso como demonstrações da interface.
- Faça logout ao terminar.

## 14. Instalação local — equipe responsável

Esta seção é destinada a quem prepara o projeto no computador da demonstração.

1. Coloque a pasta do projeto dentro da pasta `htdocs` do XAMPP.
2. Inicie **Apache** e **MySQL** no painel do XAMPP.
3. Abra o phpMyAdmin e importe `database/db.sql` para criar o banco e carregar os dados de demonstração.
4. A conexão do projeto está configurada em `infra/conexao.php`. Se o MySQL da máquina usar outros dados, a pessoa responsável pela instalação deve ajustar essa configuração localmente.
5. Abra `http://localhost/<pasta-do-projeto>/index.php` no navegador. Se o Apache estiver usando outra porta, inclua a porta no endereço.

## 15. Equipe e suporte

**Sistema:** TremTech  
**Equipe de desenvolvimento:** Alana Veiga da Silva, Nicole Beloni da Silva e Piettra Nicole Cidral.

Para dificuldades de acesso, dúvidas sobre os cadastros ou problemas de instalação, procure a equipe ou a pessoa responsável pela demonstração. Os canais de e-mail e telefone não foram definidos no projeto.







