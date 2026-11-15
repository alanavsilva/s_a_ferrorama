# XAMPP

### O que é XAMPP
O XAMPP é um pacote gratuito de ferramentas que cria um servidor web local no seu computador. Ele permite que você instale, teste e desenvolva sites e aplicativos (como projetos em PHP e bancos de dados) direto da sua máquina, sem precisar contratar ou depender de uma hospedagem na internet.

### Principais componentes do ambiente
Ele reúne os pilares essenciais para rodar sites e sistemas dinâmicos em um único pacote, composto pelo servidor web Apache, pelo banco de dados MariaDB/MySQL, pela linguagem PHP e pela interface phpMyAdmin

### Finalidade de cada componente

- **Apache** 
Sua função principal é receber as requisições feitas pelos navegadores e entregar as páginas e arquivos (como HTML e PHP) para que você possa visualizá-los e testá-los no seu próprio computador.

- **MySQL/MariaDB** 
É o sistema responsável por armazenar, organizar e gerenciar os dados (como usuários, senhas, posts e produtos) das suas aplicações web. Ele atua como o cérebro de banco de dados do seu servidor local.

- **PHP**
É o interpretador da linguagem. Ele é responsável por ler os scripts que você escreve (arquivos .php), processar as informações no servidor e gerar o código HTML que será exibido no navegador do usuário.

- **phpMyAdmin**
É uma ferramenta gráfica gratuita escrita em PHP que serve para administrar bancos de dados MySQL e MariaDB. No XAMPP, ele atua como uma interface web fácil de usar, permitindo que você gerencie seu banco de dados visualmente em vez de usar linhas de comando.

### Como realizar a instalação e configuração básica
Instalar e configurar o XAMPP é um processo simples e rápido. Acesse a Página Oficial do XAMPP, baixe a versão compatível com o seu sistema operacional e execute o instalador. Durante a instalação, escolha os componentes desejados, como Apache, MySQL e PHP. Após concluir, abra o painel de controle do XAMPP e inicie os módulos principais para começar a utilizar o ambiente de desenvolvimento local no seu computador.

### Importância do ambiente para desenvolvimento local
Um ambiente de desenvolvimento local como o XAMPP é essencial porque simula um servidor web diretamente no seu computador. Ele permite que você crie, teste e depure sites e sistemas dinâmicos (como PHP e MySQL) com segurança e rapidez, sem precisar pagar por hospedagem ou domínios durante a fase de criação.

### Aplicação no TremTech

O TremTech é executado localmente pelo Apache e usa PHP com MySQLi para acessar o banco MariaDB/MySQL. A cópia verificada para a entrega usa PHP 8.2 e MariaDB 10.4 no XAMPP. As instruções de instalação, banco e configuração local estão no `MANUAL.md`; a configuração com credenciais reais da estação não deve ser enviada ao repositório.
