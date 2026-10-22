# Segurança dos dados

## Dados que precisam de proteção

O sistema TREMTECH manipula dados de usuários, principalmente nome, e-mail, perfil e senha. As senhas são informações confidenciais e não devem ser armazenadas em texto puro.

## Medidas implementadas

### 1. Proteção de senhas e credenciais

As senhas cadastradas não são mais gravadas diretamente no banco. O PHP utiliza `password_hash()` com `PASSWORD_DEFAULT` para gerar um hash seguro antes do armazenamento.

No login, a senha informada é verificada com `password_verify()`.

A senha original nunca é exibida no frontend nem retornada pelas consultas de listagem.

### 2. Proteção contra SQL Injection

As operações que recebem dados do usuário utilizam consultas preparadas com `prepare()` e `bind_param()`.

Isso foi aplicado principalmente ao login, cadastro, pesquisa, edição e exclusão de usuários.

### 3. Validação e sanitização

O servidor valida os dados recebidos antes de realizar operações no banco. São verificadas, entre outras condições:

- campos obrigatórios;
- formato do e-mail;
- tamanho mínimo da senha;
- tipo de usuário permitido;
- identificadores numéricos.

A validação no JavaScript continua sendo útil para a interface, mas a segurança não depende dela.

### 4. Controle de acesso

O sistema utiliza sessão para identificar o usuário autenticado.

As páginas que exigem login verificam a sessão no servidor.

A tela de gerenciamento de usuários exige perfil de `administrador` por meio de uma verificação no PHP.

### 5. Proteção do cadastro de Administrador

O campo `tipo` do formulário não é considerado uma autorização.

Quando o formulário solicita `Administrador`, o servidor verifica o perfil armazenado na sessão. Somente um usuário cuja sessão possui `tipo = administrador` pode criar outro Administrador.

Assim, alterar o HTML ou enviar manualmente `tipo=Administrador` não concede o privilégio.

### 6. Proteção contra requisições indevidas

As operações de alteração e exclusão utilizam token CSRF.

A exclusão de usuários foi alterada de `GET` para `POST`, evitando que uma simples abertura de URL execute uma operação administrativa.

Também foi adicionada uma proteção para impedir que o administrador exclua a própria conta pela tela de exclusão.

### 7. Proteção das informações no frontend

A listagem de usuários não consulta nem exibe a coluna de senha.

Dados exibidos na página são escapados com `htmlspecialchars()` para reduzir o risco de XSS.

### 8. Mensagens de erro

As mensagens exibidas ao usuário não mostram consultas SQL, credenciais do banco ou detalhes internos da aplicação.

Detalhes técnicos da conexão com o banco são registrados no log do servidor, enquanto o usuário recebe uma mensagem genérica.

### 9. Sessão

Após o login bem-sucedido, o identificador da sessão é regenerado com `session_regenerate_id(true)`.

O cookie da sessão utiliza `HttpOnly` e `SameSite=Lax`. Em HTTPS, a opção `Secure` também é ativada.

## Criptografia

Não foi aplicada criptografia reversível às senhas porque senhas não precisam ser recuperadas pelo sistema. Para esse dado, o mecanismo adequado é o hash de senha com `password_hash()`.

A criptografia deve ser utilizada somente quando existir necessidade de recuperar o conteúdo original de uma informação sensível. Não deve ser usada apenas para esconder dados no código ou no frontend.

## Observação para o banco existente

Se já existirem usuários cadastrados com senhas em texto puro, essas senhas precisam ser redefinidas para que passem a ser armazenadas com hash. O código de login desta versão espera hashes gerados por `password_hash()`.
