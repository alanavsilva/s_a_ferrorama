create database SA_FERRORAMA;
use SA_FERRORAMA;

create table usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    tipo ENUM('Administrador', 'Funcionário') NOT NULL,
    senha VARCHAR(255) NOT NULL
);

create table sensores (
    id_sensor INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    trem_vinculado VARCHAR(100) NOT NULL,
    direcao VARCHAR(50) NOT NULL,
    temperatura DECIMAL(5,2) NOT NULL,
    velocidade DECIMAL(5,2) NOT NULL
);

create table rotas (
    id_rotas INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    extensao FLOAT NOT NULL,
    tempo INT NOT NULL
);

create table trens (
    id_trem INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    velocidade_max FLOAT NOT NULL,
    tipo ENUM('Velocidade', 'Temperatura','Status') NOT NULL,
    id_rotas INT NOT NULL,

    FOREIGN KEY (id_rotas) REFERENCES rotas(id_rotas)
);

  CREATE TABLE falhas_operacionais (
    id_falha INT PRIMARY KEY AUTO_INCREMENT,
    id_sensor INT NOT NULL,
    descricao VARCHAR(200) NOT NULL,
    data_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolvida TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_sensor) REFERENCES sensores(id_sensor)
);