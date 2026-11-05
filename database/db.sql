CREATE DATABASE SA_FERRORAMA;
USE SA_FERRORAMA;

CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    tipo ENUM('Administrador', 'Funcionário') NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE trens (
    id_trem INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    velocidade_media FLOAT NOT NULL,
    tipo ENUM('Velocidade', 'Temperatura', 'Status') NOT NULL,
    id_usuario INT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE rotas (
    id_rotas INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    extensao FLOAT NOT NULL,
    tempo INT NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_trem INT NOT NULL,
    FOREIGN KEY (id_trem) REFERENCES trens(id_trem)
);

CREATE TABLE sensores (
    id_sensor INT PRIMARY KEY AUTO_INCREMENT,
    nome_sensor VARCHAR(100) NOT NULL,
    tipo_dado ENUM('Velocidade', 'Temperatura', 'Status') NOT NULL,
    id_trem INT NOT NULL,
    id_rotas INT NOT NULL,
    FOREIGN KEY (id_trem) REFERENCES trens(id_trem),
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
