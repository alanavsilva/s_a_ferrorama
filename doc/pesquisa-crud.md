# Pesquisa sobre CRUD

### Conceito de CRUD

CRUD é uma sigla para **C** = Create (Criar), **R** = Read (Ler), **U** = Update (Atualizar) e **D** = Delete (Apagar).

Mas vai muito além de uma sigla técnica, é um padrão universal de desenvolvimento de sistemas, que surgiu como forma de organizar logicamente as interações entre usuário e banco de dados.  

### Operações da sigla CRUD

#### Create (Criar)
Esta etapa envolve a criação de novos dados em um sistema, criar ou adicionar novas entradas.

#### Read (Ler)
A função “Read” é o que permite aos sistemas acessar e exibir dados. Ela é usada cada vez que solicitamos informações, como ao verificar o saldo em uma conta bancária online. Essencialmente, essa função garante que os dados armazenados possam ser recuperados e visualizados quando necessário. 

#### Update (Atualizar)
“Update” refere-se à atualização de dados existentes. Esta função é crucial para manter a relevância e a precisão das informações em um banco de dados. 

#### Delete (Apagar)
Esta função é usada para remover dados de um sistema. Ela deve ser manuseada com cuidado, pois uma vez que os dados são apagados, podem não ser recuperáveis.

### Aplicação no TremTech atual

Nesta versão, o CRUD é usado nos cadastros de usuários, trens, rotas e sensores. As operações são feitas pela interface administrativa, processadas em PHP e armazenadas no MySQL/MariaDB.

- **Create (Criar):** o Administrador cadastra usuários, trens, rotas e sensores. Também registra falhas manualmente para um sensor.
- **Read (Consultar):** os usuários autenticados consultam as informações permitidas pelo perfil. As listas oferecem pesquisa em cadastros, e a área de falhas permite filtrar pelo estado.
- **Update (Atualizar):** o Administrador edita os cadastros e pode resolver ou reabrir falhas. A edição respeita os vínculos entre usuários e trens, trens e rotas, e sensores e trens.
- **Delete (Excluir):** o Administrador pode excluir cadastros quando não há registros dependentes que precisem deles. Chaves estrangeiras e validações preservam as relações.

O monitoramento apresenta a velocidade média cadastrada para o trem associado e a temperatura calculada pelo sistema. Consumo de energia, localização real e criação automática de falhas não fazem parte do CRUD atual.

