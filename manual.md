# Manual do usuário — TremTech

**Sistema de monitoramento e gerenciamento ferroviário**

**Equipe:** Alana Veiga da Silva, Nicole Beloni da Silva e Piettra Nicole Cidral.

## 1. Sobre o sistema

O TremTech permite organizar usuários, trens, rotas e sensores, registrar leituras e falhas e consultar relatórios.

O sistema possui dois perfis:

- **Administrador:** pode consultar, cadastrar, editar e excluir registros, respeitando as regras do sistema. Também pode registrar leituras e falhas, resolver e reabrir ocorrências.
- **Funcionário:** pode consultar os registros, acompanhar o monitoramento e as falhas e gerar relatórios.

## 2. Como acessar o sistema

1. Abra o navegador.
2. Digite o endereço informado pela equipe.
3. No computador com a instalação atual, o endereço é:
   `http://localhost:8080/alana_v_silva_2026/s_a_ferrorama/index.php`
4. Se a instalação utilizar outra pasta ou porta, use o endereço correspondente.

O endereço com `localhost` funciona no próprio computador em que o sistema está instalado.

Para entrar, é necessário ter uma conta cadastrada pelo Administrador.

## 3. Como fazer login

1. Na tela de login, informe seu e-mail.
2. Digite sua senha.
3. Clique em **Entrar**.
4. Após o login, o sistema abrirá a página **Home**.

Se aparecer uma mensagem de erro, confira o e-mail e a senha.

Caso não tenha uma conta ou tenha esquecido a senha, procure o Administrador. O sistema não possui cadastro público nem recuperação de senha pela tela de login.

O link **Conheça o Ferrorama** abre a página de apresentação do projeto.

## 4. Como navegar pelo sistema

Use o menu para acessar as principais telas:

- **Home:** resumo das informações do sistema.
- **Usuários:** consulta e gerenciamento das contas.
- **Trens:** consulta e gerenciamento dos trens.
- **Rotas:** consulta e gerenciamento das rotas.
- **Sensores:** consulta e gerenciamento dos sensores.
- **Monitoramento:** consulta das leituras, do percurso ilustrativo e das falhas.
- **Falhas:** cadastro e acompanhamento das ocorrências.
- **Relatórios:** consulta das informações de um sensor em determinado dia.
- **Logout:** saída do sistema.

O nome e o perfil da pessoa conectada aparecem no cabeçalho.

### Ordem para realizar os cadastros

Para facilitar o uso, siga esta ordem:

1. Cadastre o usuário responsável.
2. Cadastre o trem e escolha esse responsável.
3. Cadastre a rota e escolha o trem.
4. Cadastre o sensor e escolha a rota.
5. Registre leituras ou falhas.
6. Consulte o monitoramento e os relatórios.

O sensor fica relacionado ao trem por meio da rota escolhida.

### Como buscar registros

1. Abra a tela desejada.
2. Digite uma informação no campo de busca.
3. Clique em **Buscar**.
4. Confira os resultados na lista.

Para mostrar todos os registros novamente, apague o texto da busca e clique em **Buscar**.

### Como editar registros

A edição é exclusiva do Administrador.

1. Localize o registro.
2. Clique no ícone de lápis.
3. Altere as informações.
4. Clique em **SALVAR ALTERAÇÕES**.

Para sair da edição sem salvar, clique em **Cancelar**.

### Como excluir registros

A exclusão é exclusiva do Administrador.

1. Localize o registro.
2. Clique no ícone de lixeira.
3. Confira a mensagem de confirmação.
4. Confirme a exclusão ou clique em **Cancelar**.

A exclusão não pode ser desfeita pela interface. Alguns registros não podem ser excluídos enquanto estiverem vinculados a outros dados.

## 5. Como cadastrar e consultar usuários

### Cadastrar um usuário

Somente o Administrador pode cadastrar usuários.

1. Abra **Usuários**.
2. Clique em **Cadastrar usuário**.
3. Preencha o nome.
4. Informe um e-mail válido.
5. Defina uma senha com pelo menos oito caracteres.
6. Escolha o perfil: **Administrador** ou **Funcionário**.
7. Clique em **CADASTRAR NOVO USUÁRIO**.
8. Confira a mensagem e o registro na lista.

O e-mail não pode estar sendo usado por outra conta.

### Consultar usuários

1. Abra **Usuários**.
2. Veja as contas na lista.
3. Para localizar uma pessoa, busque pelo nome ou e-mail.

A lista apresenta o nome, o e-mail e o perfil dos usuários.

### Editar ou excluir usuários

- Para editar, clique no lápis, altere os dados e salve.
- Se não quiser trocar a senha, deixe o campo de nova senha vazio.
- Para excluir, clique na lixeira e confirme.
- O sistema não permite excluir uma conta com perfil de Administrador.
- Um usuário responsável por um trem também não pode ser excluído. Primeiro, altere o responsável no cadastro do trem.

## 6. Como cadastrar e consultar trens

### Cadastrar um trem

Somente o Administrador pode cadastrar trens.

1. Abra **Trens**.
2. Clique em **Cadastrar trem**.
3. Informe o nome do trem, com pelo menos três caracteres.
4. Informe a velocidade média em km/h.
5. Escolha um usuário cadastrado como responsável.
6. Clique em **CADASTRAR NOVO TREM**.
7. Confira o registro na lista.

A velocidade média deve ficar entre 1 e 600 km/h.

### Consultar trens

1. Abra **Trens**.
2. Veja os registros na lista.
3. Para localizar um trem, busque pelo nome ou pelo responsável.

A lista apresenta o nome do trem, a velocidade média e o responsável.

### Editar ou excluir trens

- Para editar, clique no lápis.
- Altere os dados ou escolha outro responsável.
- Clique em **SALVAR ALTERAÇÕES**.
- Para excluir, clique na lixeira e confirme.
- Trens com rotas ou sensores vinculados não podem ser excluídos enquanto existirem esses vínculos.

## 7. Como cadastrar e consultar rotas

### Cadastrar uma rota

Somente o Administrador pode cadastrar rotas.

1. Abra **Rotas**.
2. Clique em **Cadastrar rota**.
3. Informe o nome da rota.
4. Escolha o trem vinculado.
5. Escreva a descrição.
6. Informe a **Extensão**, em quilômetros.
7. Informe o **Tempo estimado**, em minutos.
8. Clique em **CADASTRAR ROTA**.
9. Confira o registro na lista.

Regras do cadastro:

- O nome deve ter de 3 a 100 caracteres.
- A descrição é obrigatória e pode ter até 255 caracteres.
- A extensão deve ser maior que zero.
- O tempo deve ser um número inteiro, a partir de 1 minuto.
- O trem precisa estar cadastrado.
- A data de criação é preenchida automaticamente.

### Consultar rotas

1. Abra **Rotas**.
2. Veja os registros na lista.
3. Busque pelo nome da rota, pela descrição ou pelo nome do trem.

### Editar ou excluir rotas

- Para editar, clique no lápis, altere os dados e salve.
- Para excluir, clique na lixeira e confirme.
- Uma rota com sensores vinculados não pode ser excluída. Primeiro, é necessário alterar esses vínculos.

## 8. Como cadastrar e consultar sensores

### Cadastrar um sensor

Somente o Administrador pode cadastrar sensores.

1. Abra **Sensores**.
2. Clique em **Cadastrar sensor**.
3. Informe o nome do sensor.
4. Escolha o tipo: **Velocidade**, **Temperatura** ou **Status**.
5. Escolha uma **Rota vinculada**.
6. Clique em **CADASTRAR NOVO SENSOR**.
7. Confira o registro na lista.

O nome deve ter de 3 a 100 caracteres.

Não é necessário selecionar o trem separadamente. Ele é definido pela rota escolhida.

Os tipos de sensor são usados para:

- **Velocidade:** registrar valores em km/h.
- **Temperatura:** registrar valores em °C.
- **Status:** registrar falhas.

Cadastrar um sensor não cria uma leitura automaticamente.

### Consultar sensores

1. Abra **Sensores**.
2. Veja o nome, a rota e o tipo de cada sensor.
3. Para localizar um registro, busque pelo nome, tipo ou rota.

### Editar ou excluir sensores

- Para editar, clique no lápis, altere os dados e salve.
- Para excluir, clique na lixeira e confirme.
- Um sensor com falhas vinculadas não pode ser excluído.
- Quando um sensor é excluído, suas leituras numéricas também são apagadas.

## 9. Como entender a Home e o dashboard

A **Home** apresenta um resumo do sistema.

### Quantidade de registros

Os cartões mostram o total de:

- Usuários cadastrados.
- Trens cadastrados.
- Rotas cadastradas.
- Sensores cadastrados.

Esses números representam cadastros. Eles não indicam quantos equipamentos estão funcionando.

### Velocidade média

- O cartão mostra a velocidade média informada no cadastro do trem.
- O trem exibido muda a cada três segundos.
- Esse valor é diferente da leitura do sensor mostrada no monitoramento.
- Registrar uma leitura não altera a velocidade média do cadastro.

### Clima

- A área de clima apresenta informações do tempo em Joinville.
- Essa temperatura é do ambiente, não do trem.
- A consulta depende de um serviço externo e pode ficar indisponível.

### Consulta de CEP

1. Digite um CEP com oito números.
2. Clique em **Consultar CEP**.
3. Confira o endereço encontrado.

A consulta apenas apresenta o endereço. Ela não cadastra uma rota.

### Falhas

- **Resolvida:** ocorrência marcada como concluída.
- **Não resolvida:** ocorrência que continua em aberto.
- O filtro permite escolher quais ocorrências aparecem na lista.
- Os contadores mostram os totais do sistema, mesmo ao filtrar a lista.
- O Administrador pode resolver ou reabrir uma ocorrência.

## 10. Como acompanhar o monitoramento

1. Abra **Monitoramento**.
2. Escolha o **Sensor de velocidade**.
3. Escolha o **Sensor de temperatura**.
4. Confira os valores, as datas e os status apresentados.
5. Escolha uma **Rota** para identificar o trem e o percurso apresentado.
6. Consulte as falhas na mesma tela.

Os sensores e a rota são selecionados separadamente. Confira os nomes para saber a qual trem cada informação pertence.

### Velocidade

- Mostra a última leitura cadastrada para o sensor.
- A unidade é km/h.
- Acima de 100 km/h, aparece **Alerta**.
- Até 100 km/h, aparece **Normal**.
- O valor do cartão é arredondado para um número inteiro.

### Temperatura

- Mostra a última leitura cadastrada para o sensor.
- A unidade é °C.
- Acima de 40 °C, aparece **Alerta**.
- Até 40 °C, aparece **Normal**.
- O cartão apresenta uma casa decimal.

### Data da leitura

- Mostra a data informada no registro.
- Uma leitura antiga pode continuar aparecendo se não houver outra mais recente.
- A leitura com a data mais recente é usada no cartão.
- Se duas leituras tiverem a mesma data, aparece a cadastrada por último.

### Cartões com traços

- Traços no lugar dos valores indicam ausência de leitura disponível.
- Eles não significam valor zero ou situação normal.

### Percurso e localização

- O desenho do percurso é uma animação ilustrativa.
- A seleção da rota altera a identificação apresentada.
- A animação não mostra a posição real do trem nem reproduz o trajeto cadastrado.

### Origem das informações

- O sistema não recebe medições de sensores físicos.
- As leituras são registradas manualmente ou vêm dos dados de demonstração.
- Atualize a página para consultar novos registros.
- Um alerta de velocidade ou temperatura não cria uma falha automaticamente.
- O filtro das falhas considera a situação da ocorrência, não os sensores ou a rota selecionados nos cartões.

## 11. Como registrar leituras ou falhas

Essa função é exclusiva do Administrador.

1. Abra **Monitoramento**.
2. No fim da página, clique em **Registrar leitura ou falha**.
3. Escolha o **Sensor vinculado**.
4. Preencha as informações conforme o tipo do sensor.
5. Informe a **Data da leitura ou ocorrência**.
6. Salve e confira a mensagem ao retornar ao monitoramento.

### Para um sensor de velocidade

- Informe um valor entre 0 e 600 km/h.
- O formulário permite duas casas decimais.
- Clique em **SALVAR LEITURA**.

### Para um sensor de temperatura

- Informe um valor entre 0 e 150 °C.
- O formulário permite duas casas decimais.
- Clique em **SALVAR LEITURA**.

### Para um sensor de Status

- Escreva a descrição da falha.
- A descrição deve ter de 5 a 200 caracteres.
- Clique em **CADASTRAR FALHA**.
- A ocorrência será criada como não resolvida.

O formulário permite informar a data, mas não o horário. O registro fica com horário 00:00.

Confira os dados antes de salvar. A interface não oferece edição ou exclusão individual das leituras.

## 12. Como cadastrar e acompanhar falhas

### Cadastrar uma falha pela tela Falhas

Somente o Administrador pode cadastrar falhas.

1. Abra **Falhas**.
2. Clique em **Cadastrar falha**.
3. Escolha um sensor do tipo **Status**.
4. Escreva uma descrição com 5 a 200 caracteres.
5. Confirme o cadastro.
6. Confira a mensagem e a ocorrência na lista.

Nessa tela, a data e o horário são registrados automaticamente. A falha começa como não resolvida.

### Consultar falhas

1. Abra **Falhas**.
2. Use o filtro para consultar todas, somente resolvidas ou somente não resolvidas.
3. Confira a descrição, o sensor, o trem, a data e a situação.

As falhas também podem ser acompanhadas na Home e no Monitoramento.

### Resolver ou reabrir uma falha

Somente o Administrador pode alterar a situação.

- Clique em **Marcar como resolvida** para concluir uma ocorrência.
- Clique em **Reabrir** para colocar uma ocorrência resolvida novamente em aberto.

O Funcionário pode consultar as falhas, mas não pode cadastrá-las, resolvê-las ou reabri-las.

A interface não oferece exclusão de falhas.

## 13. Como consultar relatórios

1. Abra **Relatórios**.
2. Escolha um **Sensor**.
3. Selecione a data em **Dia do relatório**.
4. Clique em **Gerar relatório**.
5. Confira as informações apresentadas.

O relatório mostra o sensor, o tipo, o trem, a rota, o responsável pelo trem e a data consultada.

### Relatório de velocidade

- Apresenta as leituras do sensor no dia escolhido.
- Os valores aparecem em km/h, com duas casas decimais.

### Relatório de temperatura

- Apresenta as leituras do sensor no dia escolhido.
- Os valores aparecem em °C, com duas casas decimais.

### Relatório de Status

- Apresenta as falhas do sensor no dia escolhido.
- Mostra o total de ocorrências.
- Informa quantas estão resolvidas e quantas estão em aberto.
- Apresenta a descrição e a situação das falhas.

Se não houver registros, confira o sensor e o dia selecionados. A consulta é por dia, não pelo mês inteiro.

### Histórico de relatórios

- O sistema mantém até 20 consultas recentes durante a sessão.
- O histórico aparece quando existem consultas anteriores diferentes da atual.
- Ao fazer logout, esse histórico é apagado.
- O sistema não oferece botão para baixar ou exportar o relatório.
- Para consultar informações atualizadas, gere o relatório novamente.

## 14. Funções exclusivas do Administrador

Somente o Administrador pode:

- Cadastrar, editar e excluir usuários.
- Cadastrar, editar e excluir trens.
- Definir ou alterar o responsável por um trem.
- Cadastrar, editar e excluir rotas.
- Cadastrar, editar e excluir sensores.
- Registrar leituras de velocidade e temperatura.
- Cadastrar falhas.
- Marcar falhas como resolvidas.
- Reabrir falhas.

As exclusões precisam respeitar os vínculos entre os registros.

Funcionários não podem executar essas ações, mesmo tentando acessar diretamente o endereço de uma função administrativa.

## 15. Como fazer logout

1. Clique em **Logout**.
2. Na mensagem **Sair do sistema?**, clique em **Sair**.
3. O sistema encerrará a sessão e abrirá a tela de login.

Para permanecer conectado, clique em **Continuar no sistema**.

Faça logout ao terminar, principalmente em computadores compartilhados.

## 16. O que fazer quando algo não funcionar

### Não consigo entrar

- Confira o e-mail e a senha.
- Se o problema continuar, procure o Administrador.

### Não aparecem registros

- Apague o texto da busca e clique em **Buscar**.
- Confira se existem registros cadastrados.

### Não consigo cadastrar ou editar

- Confira se seu perfil é Administrador.
- Preencha os campos obrigatórios.
- Leia a mensagem apresentada pelo sistema.

### Não consigo excluir

- Confira se o registro está vinculado a outros dados.
- Consulte as regras de exclusão da seção correspondente.

### A rota não aparece no cadastro do sensor

- Confira se o trem e a rota já foram cadastrados.

### O sensor não aparece no cadastro de falhas

- Confira se existe um sensor do tipo **Status**.

### O monitoramento mostra traços

- Confira o sensor selecionado.
- Peça ao Administrador para registrar uma leitura.

### A leitura não aparece como a mais recente

- Confira o sensor e a data informada.
- Atualize a página.
- Uma leitura com data anterior não substitui a de data mais recente.

### O relatório está vazio

- Confira o sensor e o dia selecionados.
- Verifique se existem leituras ou falhas para essa data.

### Clima ou CEP não carrega

- Confira a conexão com a internet.
- Tente novamente mais tarde.

### Aparece “Acesso negado”

- A função exige perfil de Administrador.

### Aparece “Requisição inválida”

- Abra novamente a tela.
- Se necessário, faça logout e login antes de repetir a operação.

### O sistema não abre ou apresenta erro de banco de dados

- Procure a pessoa responsável pela instalação.

## 17. Preparação do sistema — equipe responsável

Esta seção é destinada a quem instala o sistema.

1. Coloque a pasta do projeto dentro de `htdocs`, no XAMPP.
2. Inicie **Apache** e **MySQL**.
3. Em uma instalação nova, importe `database/db.sql` pelo phpMyAdmin.
4. Confira a configuração do banco em `infra/conexao.php`.
5. Abra o endereço do projeto no navegador.
6. Forneça uma conta válida para quem utilizará o sistema.

Não importe novamente o arquivo SQL completo sobre um banco já utilizado. Para instalações antigas, confira as atualizações das tabelas, incluindo a tabela de leituras.

As senhas das contas de demonstração não estão escritas em texto no arquivo SQL. A equipe deve fornecer as credenciais de acesso.

## 18. Dicas e suporte

- Confira as informações antes de salvar.
- Verifique o registro antes de confirmar uma exclusão.
- Confira a rota e o sensor escolhidos.
- Observe a data das leituras.
- Não compartilhe sua senha.
- Faça logout ao terminar.

Para dificuldades de acesso ou problemas no sistema, procure o Administrador ou a equipe de desenvolvimento:

- Alana Veiga da Silva.
- Nicole Beloni da Silva.
- Piettra Nicole Cidral.
