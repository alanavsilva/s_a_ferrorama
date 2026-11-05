# APIs (Application Programming Interfaces)

## O que é uma API e qual é sua finalidade?

## Como funciona a comunicação entre sistemas por meio de APIs?

## O que são APIs REST?

## O papel dos métodos HTTP, como GET, POST, PUT, PATCH e DELETE.

## O que são requisições e respostas HTTP?

## O que são endpoints?

## Como funciona a troca de dados no formato JSON?

## Diferenças entre uma API e um sistema web tradicional.

## Como uma aplicação PHP pode consumir uma API?

Uma aplicação PHP pode consumir uma API por meio de requisições HTTP, utilizando ferramentas como cURL. O PHP envia uma solicitação ao servidor da API, que retorna os dados, geralmente em formato JSON. Depois, o PHP interpreta essas informações e pode utilizá-las para exibir dados ou realizar operações no sistema.

https://www.php.net/manual/pt_BR/ 

## Como uma API pode disponibilizar informações para outros sistemas?

Uma API disponibiliza informações por meio de endpoints, que são endereços específicos utilizados para acessar determinados recursos.

Por exemplo:

- GET /produtos: consulta os produtos cadastrados.

- GET /usuarios: consulta os usuários.

- POST /produtos: cadastra um novo produto.

Dessa forma, diferentes sistemas podem compartilhar informações sem precisar acessar diretamente o banco de dados uns dos outros, facilitando a integração entre aplicações.

https://www.rfc-editor.org/rfc/rfc9110.html

## Importância da autenticação, autorização e segurança no consumo de APIs.

A segurança é essencial para proteger informações e impedir acessos indevidos. Existem três conceitos importantes:

- Autenticação: verifica a identidade de quem está acessando a API.

- Autorização: determina quais recursos e funcionalidades podem ser utilizados.

- Segurança: protege os dados contra vazamentos, ataques e alterações não autorizadas.

Alguns mecanismos utilizados são:

- HTTPS, para proteger a comunicação.

- Tokens de acesso, para controlar o acesso aos recursos.

- Controle de permissões, para limitar as funcionalidades disponíveis.

Esses mecanismos aumentam a confiabilidade e a proteção dos sistemas.

https://owasp.org/projects/api-security-project

## Exemplos de utilização de APIs em sistemas empresariais.

As APIs são utilizadas em empresas para integrar diferentes sistemas e automatizar processos. No setor financeiro, permitem consultar transações e realizar pagamentos; no comércio eletrônico, consultar produtos e estoques; na logística, rastrear entregas; e nos recursos humanos, integrar informações de funcionários e folha de pagamento. Essas aplicações facilitam o compartilhamento de dados e aumentam a eficiência das operações.

https://aws.amazon.com/what-is/api