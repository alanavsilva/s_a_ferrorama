# PDO

> **Aplicação no TremTech:** este arquivo registra um estudo sobre PDO. A aplicação atual não usa PDO; a conexão é feita com MySQLi em `infra/conexao.php`, com consultas preparadas por `prepare()` e `bind_param()`. Portanto, a pesquisa não deve ser lida como a lista de tecnologias efetivamente adotadas.

### O que é PDO?
A extensão PHP Data Objects (PDO) define uma interface leve e consistente para acessar bancos de dados no PHP. Cada driver de banco de dados que implementa a interface PDO pode expor recursos específicos do banco de dados como funções de extensão comuns. Observe que não é possível executar nenhuma função de banco de dados usando apenas a extensão PDO; um driver PDO de banco de dados deve ser usado para acessar o servidor de banco de dados.

### Para que serve?
PDO (PHP Data Objects) serve como uma camada de abstração para conectar e manipular bancos de dados em aplicações escritas na linguagem PHP. Em vez de usar funções específicas para cada sistema (como MySQL, PostgreSQL ou SQLite), o programador usa uma interface única e padronizada.

### Como funciona?
É uma extensão do PHP que funciona como uma camada de abstraçõa para acesso a bancos de dados relacional.

### Caracteristicas:
- Flexibilidade – Como o PDO carrega o driver específico do banco de dados em tempo de execução, não é preciso reconfigurar o PHP sempre que um banco de dados diferente for usado.

- Desempenho – O PDO está escrito em C e compilado no PHP, o que lhe garante um aumento considerável no desempenho em relação a soluções escritas em PHP.

- Consistência de código – No PDO não existe a inconsistência de código, pois é oferecida apenas uma interface unificada que é está disponível para qualquer banco de dados.

- Características de orientação de objetos – Possui recursos de orientação de objetos, o que resulta em uma comunicação mais poderosa e eficiente com banco de dados.

### PDO vantagens:
- Funciona com 12 drivers de bancos de dados diferentes (4D, MS SQL Server, Firebird/Interbase, MySQL, Oracle, ODBC/DB2, PostgreSQL, SQLite, Informix, IBM, CUBRID);

- API Orientada a objetos;

- Possui parâmetros nomeados;

- Possui prepared statements do lado cliente

### PDO desvantagens:
- Não tão veloz quanto MySQLi;

- Por padrão, ele simula prepared statements (você pode ativar a versão nativa ao configurar a conexão dele com o banco, mas caso a versão nativa não funcione por algum motivo, ele volta a simular os prepared statements sem disparar erros ou avisos)

### MySQL vantagens:
- API Orientada a objetos e procedural;

- Performace elevada;

- Sintaxe relativamente mais simples (e similar a antiga API mysql_*);

### MySQL desvantagens:
- Só funciona com bancos MySQL;

- Não possui parâmetros nomeados;

- Não possui prepared statements do lado cliente;

### Prepared Statements;
É uma instrução preparada (também conhecida como instrução parametrizada) éum recurso de banco de dados que pré-compila modelos de código SQL e os separa dos dados do usuárioServe como uma prática fundamental para a construção de aplicações web seguras e eficientes.

### Quando utilizar PDO?
O PDO (PHP Data Objects) deve ser utilizado em aplicações PHP quando você precisa de uma interface segura, orientada a objetos e flexível para conectar a aplicação a diferentes sistemas de banco de dados. Pode ser utilizado em troca de banco de dados, segurança contra injeção de sql, uso de orientação a objetos e controle de transações.

### Fontes utilizadas: 
https://www.php.net/manual/pt_BR/book.pdo.php
https://www.treinaweb.com.br/blog/o-que-e-pdo-no-php
https://www.devmedia.com.br/introducao-ao-php-data-objects-pdo/25318
https://www.w3schools.com/sql/sql_prepared_statements.asp
https://www.devmedia.com.br/php-pdo-como-se-conectar-ao-banco-de-dados/37211

