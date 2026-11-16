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
    id_rotas INT NOT NULL,
    FOREIGN KEY (id_rotas) REFERENCES rotas(id_rotas)
);

CREATE TABLE leituras_sensores (
    id_leitura INT PRIMARY KEY AUTO_INCREMENT,
    id_sensor INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    data_leitura DATETIME NOT NULL,
    FOREIGN KEY (id_sensor) REFERENCES sensores(id_sensor) ON DELETE CASCADE
);

CREATE TABLE falhas_operacionais (
    id_falha INT PRIMARY KEY AUTO_INCREMENT,
    id_sensor INT NOT NULL,
    descricao VARCHAR(200) NOT NULL,
    data_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolvida TINYINT(1) NOT NULL DEFAULT 0,
    origem ENUM('sensor', 'usuario') NOT NULL DEFAULT 'sensor',
    id_usuario INT NULL,
    FOREIGN KEY (id_sensor) REFERENCES sensores(id_sensor),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
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

INSERT INTO sensores (id_sensor, nome_sensor, tipo_dado, id_rotas) VALUES
(1, 'Sensor de Velocidade Norte', 'Velocidade', 1),
(2, 'Sensor de Temperatura Central', 'Temperatura', 2),
(3, 'Sensor de Status Sul', 'Status', 3);

INSERT INTO leituras_sensores (id_leitura, id_sensor, valor, data_leitura) VALUES
(1, 1, 125.00, '2026-11-13 00:00:00'),
(2, 1, 25.00, '2026-11-14 00:00:00'),
(3, 1, 72.00, '2026-11-15 00:00:00'),
(4, 2, 55.00, '2026-11-13 00:00:00'),
(5, 2, 18.00, '2026-11-14 00:00:00'),
(6, 2, 35.00, '2026-11-15 00:00:00');

INSERT INTO falhas_operacionais (id_falha, id_sensor, descricao, data_registro, resolvida, origem, id_usuario) VALUES
(1, 3, 'Vibração irregular detectada no trecho sul.', '2026-11-13 00:00:00', 1, 'sensor', NULL),
(2, 3, 'Sinal de status interrompido no trecho sul.', '2026-11-14 00:00:00', 0, 'sensor', NULL),
(3, 3, 'Falha de comunicação no sensor de status.', '2026-11-15 00:00:00', 0, 'sensor', NULL);
