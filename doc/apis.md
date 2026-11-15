# APIs (Application Programming Interfaces)

## O que é uma API e qual é sua finalidade?

API é uma interface que permite a comunicação entre diferentes sistemas. Sua finalidade é permitir que aplicações compartilhem informações e funcionalidades sem precisar acessar diretamente o código umas das outras.

## Como funciona a comunicação entre sistemas por meio de APIs?

A comunicação acontece quando um sistema envia uma solicitação para outro por meio de uma API. O sistema que recebe a solicitação processa os dados e retorna uma resposta.

## O que são APIs REST?

APIs REST são interfaces que seguem regras para permitir a comunicação entre sistemas pela internet, utilizando principalmente o protocolo HTTP. São muito utilizadas em aplicações web.

## O papel dos métodos HTTP, como GET, POST, PUT, PATCH e DELETE.

- **GET:** consulta informações.
- **POST:** cadastra novas informações.
- **PUT:** atualiza completamente um registro.
- **PATCH:** atualiza parte de um registro.
- **DELETE:** exclui informações.

## O que são requisições e respostas HTTP?

Requisições HTTP são solicitações enviadas de um sistema para outro. As respostas HTTP são os retornos enviados pelo servidor, informando se a solicitação foi realizada corretamente ou se ocorreu algum erro.

## O que são endpoints?

Endpoints são endereços específicos de uma API que permitem acessar determinadas informações ou funcionalidades.

## Como funciona a troca de dados no formato JSON?

JSON é um formato utilizado para organizar e transmitir informações entre sistemas. Os dados são organizados em pares de chave e valor.

## Uso de APIs no TremTech

Na versão atual, a Home consulta o clima de Joinville na Open-Meteo usando uma requisição HTTP GET em PHP. A consulta de CEP chama a ViaCEP pelo JavaScript do navegador e recebe endereço em JSON.

Os cadastros principais não são uma API REST pública: os formulários enviam dados às páginas PHP, que validam a sessão e gravam no MySQL/MariaDB. O sistema não envia nem recebe leituras de sensores físicos.

