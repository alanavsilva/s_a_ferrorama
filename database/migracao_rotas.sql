USE SA_FERRORAMA;

-- Execute uma vez em um banco que já possui a tabela rotas.
-- A coluna id_trem começa permitindo NULL para que as rotas antigas
-- possam ser associadas a um trem sem atribuir um trem incorreto automaticamente.
ALTER TABLE rotas
    ADD COLUMN descricao VARCHAR(255) NOT NULL DEFAULT '' AFTER nome,
    ADD COLUMN data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN id_trem INT NULL;

ALTER TABLE rotas
    ADD CONSTRAINT fk_rotas_trens
    FOREIGN KEY (id_trem) REFERENCES trens(id_trem);

-- Associe cada rota antiga ao trem correto. Exemplo (ajuste os IDs):
-- UPDATE rotas SET id_trem = 1 WHERE id_rotas = 1;
-- UPDATE rotas SET id_trem = 2 WHERE id_rotas = 2;
--
-- Depois de associar todas as rotas, torne a relação obrigatória:
-- ALTER TABLE rotas MODIFY id_trem INT NOT NULL;
