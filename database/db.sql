CREATE DATABASE IF NOT EXISTS SA_FERRORAMA;
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
    CONSTRAINT fk_rotas_trens
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

INSERT INTO usuarios (id_usuario, nome, email, tipo, senha) VALUES
(1, 'Alana Veiga', 'alana@exemplo.com', 'Administrador', '$2y$10$bXVcpJfCXaNrKVunKTAEsOaFtuKi2Yfv5SmWhNZ9mjOqLl15fcs2S'),
(2, 'Nicole Beloni', 'nicole@exemplo.com', 'Funcionário', '$2y$10$xhmKTckVojt1IQEzHqBfmOHqPa/g5cYZV7vgb/vHhtdVKStR8d85C'),
(3, 'Piettra Cidral', 'piettra@exemplo.com', 'Funcionário', '$2y$10$CwPl0pjF6fLWaGpw25O3zuwpMxWripMrVQkpRbjP1aPRWh40BCSG6');

INSERT INTO trens (id_trem, nome, velocidade_media, id_usuario) VALUES
(1, 'Trem Norte', 72, 2),
(2, 'Trem Central', 95, 3),
(3, 'Trem Sul', 64, 2);

INSERT INTO rotas (id_rotas, nome, descricao, extensao, tempo, id_trem) VALUES
(1, 'Rota Norte', 'Percurso de demonstração da região norte.', 12.5, 25, 1),
(2, 'Rota Central', 'Percurso de demonstração da região central.', 18.0, 35, 2),
(3, 'Rota Sul', 'Percurso de demonstração da região sul.', 9.5, 20, 3);

INSERT INTO sensores (id_sensor, nome_sensor, tipo_dado, id_trem, id_rotas) VALUES
(1, 'Sensor Norte', 'Velocidade', 1, 1),
(2, 'Sensor Central', 'Temperatura', 2, 2),
(3, 'Sensor Sul', 'Status', 3, 3);

INSERT INTO falhas_operacionais (id_falha, id_sensor, descricao, resolvida) VALUES
(1, 1, 'Velocidade acima do limite.', 0),
(2, 2, 'Temperatura elevada no percurso.', 1),
(3, 3, 'Leitura de status instável.', 0);
