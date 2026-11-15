# Manual de utilização — TremTech

Este guia é para quem vai conhecer e usar o sistema na demonstração do Ferrorama. Os nomes dos menus correspondem aos que aparecem na aplicação.


### Instalação do projeto

1. Copie a pasta do projeto para a pasta pública do Apache. No XAMPP para Windows, normalmente é `C:\xampp\htdocs`.
2. Inicie Apache e MySQL pelo painel do XAMPP.
3. Abra o phpMyAdmin pelo painel ou pelo endereço local do XAMPP.
4. Importe `database/db.sql` para criar o banco `SA_FERRORAMA` e as tabelas. Faça isso uma vez em uma base vazia.
5. Copie `infra/conexao.local.example.php` para `infra/conexao.local.php` e preencha host, usuário, senha, nome do banco e porta conforme o MySQL da estação. Esse arquivo real é local e fica ignorado pelo Git.
6. Importe `database/usuarios_demo.sql` para adicionar as contas fictícias de demonstração.
7. Abra a landing page no navegador. Para a pasta instalada diretamente em `htdocs`, o formato do endereço é `http://localhost/<pasta-do-projeto>/public/Tela_landing/landing.php`. Também é possível abrir `http://localhost/<pasta-do-projeto>/index.php` para ir ao login. Acrescente a porta se o Apache estiver configurado para outra porta.

O projeto está neste caminho de exemplo: `C:\xampp\htdocs\alana_v_silva_2026\s_a_ferrorama`. Nesse caso, os endereços são `http://localhost:8080/alana_v_silva_2026/s_a_ferrorama/public/Tela_landing/landing.php` e `http://localhost:8080/alana_v_silva_2026/s_a_ferrorama/index.php`.

`database/db.sql` não apaga nem recria tabelas existentes. Antes de reinstalar ou limpar uma base que tenha dados, exporte uma cópia pelo phpMyAdmin. Não use as contas fictícias em um sistema publicado.

A landing page apresenta o TremTech e o botão **Acessar sistema** leva ao login.

## 2. Entrar e sair

Na tela de login, informe e-mail e senha. Para a demonstração local, as contas fictícias preparadas pelo arquivo `database/usuarios_demo.sql` são:

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Administrador | `admin.demo@example.com` | `TremTechDemo2026` |
| Funcionário | `funcionario.demo@example.com` | `TremTechDemo2026` |

Use essas contas somente na estação de demonstração. Elas não representam usuários reais.

Para sair, use **Logout** no menu ou cabeçalho e confirme a mensagem. A sessão é encerrada e o sistema volta ao login.

## 3. Perfis e permissões

| Perfil | Ações disponíveis |
| --- | --- |
| Administrador | Gerencia usuários, trens, rotas e sensores. Pode registrar falhas e resolvê-las ou reabri-las. Também consulta todas as telas. |
| Funcionário | Consulta os dados liberados pelo sistema, acompanha as falhas, abre o monitoramento e consulta relatórios. Não pode criar, editar, excluir ou alterar estados administrativos. |

As permissões também são verificadas no servidor. Se um Funcionário tentar enviar diretamente uma ação administrativa, o sistema deve negar a requisição.

## 4. Navegar e entender a Home

Use o menu para abrir **Home**, **Usuários**, **Sensores**, **Trens**, **Rotas**, **Monitoramento**, **Falhas** e **Relatórios**. A landing page é uma apresentação pública; depois do login, a Home é o painel interno.

Na Home:

- Os cartões de usuários, rotas, sensores e trens mostram quantos registros existem no banco.
- O cartão de velocidade percorre os trens cadastrados e troca o nome e o valor exibidos a cada três segundos. Esse número é a velocidade média cadastrada para o trem.
- O bloco de clima mostra condições de Joinville consultadas na Open-Meteo. Ele pode exibir uma mensagem de erro se não houver internet ou resposta da API.
- A consulta de CEP usa ViaCEP para preencher endereço, bairro, cidade e UF; informe oito números.
- A área de falhas permite consultar todas, resolvidas ou não resolvidas. Administradores também podem mudar o estado da ocorrência.
- Os atalhos variam conforme o perfil: Administradores recebem atalhos de cadastro; Funcionários recebem atalhos de consulta.

## 5. Cadastrar e consultar usuários — Administrador

1. Abra **Usuários**.
2. Use **Cadastrar usuário** para abrir o formulário.
3. Informe nome, e-mail, senha e perfil. A senha precisa ter pelo menos oito caracteres; o e-mail não pode já estar cadastrado.
4. Salve e confira a mensagem de sucesso e o novo registro na tabela.
5. Use a busca para localizar usuários por nome ou e-mail.
6. Use os ícones de ação para editar ou excluir. Na edição, deixe a nova senha vazia para manter a senha atual.

O sistema não permite excluir um Administrador pela tabela. Também impede excluir um Funcionário que ainda esteja vinculado como responsável por um trem. Primeiro altere o responsável pelo trem.

## 6. Cadastrar e consultar trens — Administrador

1. Abra **Trens** e selecione **Cadastrar trem**.
2. Informe o nome, a velocidade média em km/h e escolha um usuário responsável já cadastrado.
3. A velocidade aceita valores entre 1 e 600 km/h. O cadastro atual não tem campo de tipo/categoria do trem.
4. Salve e confirme o trem e o responsável na listagem. A busca ajuda a localizar registros.
5. Use editar para alterar dados ou responsável; confirme a exclusão quando necessário.

Um trem com rota ou sensor vinculado não deve ser excluído. Altere ou remova os vínculos dependentes primeiro; o banco protege as relações restantes.

## 7. Cadastrar e consultar rotas — Administrador

1. Abra **Rotas** e selecione **Cadastrar rota**.
2. Preencha nome, descrição, distância, tempo previsto em minutos e escolha um trem existente.
3. Distância e tempo devem ser positivos. A data de criação é registrada automaticamente pelo banco.
4. Salve e confira os campos na listagem. Use a busca para consultar as rotas.
5. Edite os detalhes ou o trem vinculado pela ação de edição.

Uma rota que tenha sensores vinculados não pode ser excluída. O sistema mostra uma mensagem e preserva os sensores e as falhas associadas.

## 8. Cadastrar e consultar sensores — Administrador

1. Abra **Sensores** e selecione **Cadastrar sensor**.
2. Informe um nome e selecione o tipo de dado: Velocidade, Temperatura ou Status.
3. Selecione o trem. A lista de rotas passa a mostrar as rotas pertencentes a esse trem.
4. Escolha a rota correta, salve e confira trem, rota e tipo na listagem.
5. Use a busca para localizar pelo nome, tipo, trem ou rota. A edição permite trocar os vínculos, mantendo a mesma regra trem/rota.

Um sensor com falhas operacionais vinculadas não pode ser excluído, pois isso apagaria a referência usada no histórico.

## 9. Registrar e acompanhar falhas

Administradores podem abrir **Falhas**, escolher **Cadastrar falha**, selecionar um sensor e descrever a ocorrência. A descrição deve ter entre 5 e 200 caracteres. Uma nova falha começa como **Não resolvida** e recebe a data/hora do banco.

Use o filtro de estado para consultar todas, resolvidas ou não resolvidas. Administradores podem marcar uma falha como resolvida ou reabri-la. Funcionários podem consultar e acompanhar a lista, sem alterar os registros.

As falhas são registradas manualmente. Um alerta de velocidade no monitoramento não cria uma ocorrência automaticamente.

## 10. Monitoramento e percurso

Abra **Monitoramento** e escolha um sensor. Com mais de um sensor cadastrado, o painel alterna entre eles a cada três segundos. A seleção manual atualiza os valores, mas a alternância automática continua.

- A velocidade apresentada vem do campo de velocidade média do trem associado ao sensor.
- Acima de 100 km/h, a tela mostra **Alerta**; até 100 km/h, mostra **Normal**.
- A temperatura exibida é demonstrativa, varia entre 35 °C e 38 °C conforme o sensor mostrado e aparece como **Normal**.
- O mapa anima um trem sobre uma linha ilustrativa que inclui ponte e desvios. O desenho é fixo; ele não é um mapa de coordenadas da rota cadastrada.
- A tela também apresenta falhas operacionais. Quando o perfil é Administrador, é possível resolver ou reabrir uma falha por essa tela.

Essas informações servem para a demonstração da interface. O sistema não está conectado a sensores físicos.

## 11. Consultar relatórios

Abra **Relatórios**, escolha um sensor e um mês, depois use **Gerar relatório**. O resultado apresenta as falhas daquele sensor no período, separa quantas estão resolvidas e abertas e lista a data, descrição e vínculos. O relatório é consultado na tela; não é salvo como um arquivo ou histórico de relatórios gerados.

## 12. Mensagens e situações comuns

- **Lista vazia:** cadastre os registros necessários como Administrador; sensores precisam de trem e rota, e falhas precisam de sensor.
- **Rota incompatível:** selecione primeiro o trem e depois uma rota ligada a ele.
- **Não pode excluir:** confira se existem rotas, sensores, falhas ou trens que dependem do registro.
- **Clima ou CEP indisponível:** confira a internet. Essas consultas externas não são necessárias para cadastrar e consultar os dados do Ferrorama.
- **Acesso negado:** confirme se entrou com o perfil Administrador para executar uma ação de gerenciamento.
- **Falha no banco:** confirme se MySQL está ativo e se `infra/conexao.local.php` contém os dados corretos.

## 13. Roteiro breve para a apresentação

1. Explique que o TremTech organiza os registros do Ferrorama.
2. Entre como Administrador e mostre os cadastros e as associações usuário–trem, trem–rota e trem/rota–sensor.
3. Demonstre uma falha, seu filtro, resolução e reabertura.
4. Abra monitoramento e relatório; explique que os valores e o mapa são demonstrativos.
5. Saia e entre como Funcionário. Mostre consultas e confirme que as ações administrativas não ficam disponíveis.
6. Faça logout.

Antes da avaliação, peça para alguém que não participou do desenvolvimento seguir esse roteiro sem ajuda. Registre dúvidas e corrija o que impedir essa pessoa de concluir as tarefas.
