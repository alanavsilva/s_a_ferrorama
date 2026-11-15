# Perfis de acesso do TremTech

O banco de dados e a aplicação trabalham com dois perfis: `Administrador` e `Funcionário`. Os perfis são definidos no cadastro de usuários.

## Administrador

O Administrador pode cadastrar, consultar, pesquisar, editar e excluir usuários, trens, rotas e sensores, respeitando os vínculos existentes. Também pode registrar falhas e resolver ou reabrir ocorrências. A Home, o monitoramento e os relatórios ficam disponíveis para consulta.

O servidor verifica o perfil antes de aceitar ações administrativas. O valor enviado pelo formulário não concede privilégios por conta própria.

## Funcionário

O Funcionário consulta as informações liberadas nas telas de usuários, trens, rotas e sensores, acompanha falhas, acessa o monitoramento e consulta relatórios. Não pode cadastrar, editar, excluir nem mudar o estado de falhas.

## Proteções e regras

- As telas internas exigem login.
- Endpoints de edição e exclusão exigem perfil de Administrador no backend.
- Os formulários de criação verificam o perfil também no processamento do POST.
- Operações de escrita protegidas verificam um token CSRF.
- Funcionários não podem alterar seu próprio perfil ou se conceder acesso administrativo por alteração do formulário.
- A interface esconde as ações que o perfil não pode realizar, mas a decisão final é feita no servidor.
- Um Administrador não pode excluir contas de Administrador pela tela de usuários. Contas vinculadas a trens também precisam ser desvinculadas antes de excluir.

## O que esta descrição não significa

Relatórios não são exclusivos do Administrador na aplicação atual. Funcionários podem consultá-los; o que permanece exclusivo do Administrador são as alterações e operações de gerenciamento descritas acima.
