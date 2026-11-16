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



