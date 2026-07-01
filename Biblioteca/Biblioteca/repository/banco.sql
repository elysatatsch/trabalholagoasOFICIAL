-- ============================================================
--  PokéCRUD — Script de criação do banco de dados
--  Execute este arquivo no phpMyAdmin ou via terminal MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS biblioteca
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE biblioteca;

-- ------------------------------------------------------------
--  Tabela de usuários
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario       INT          NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(100) NOT NULL,
    email     VARCHAR(150) NOT NULL,
    senha     CHAR(64)     NOT NULL COMMENT 'Hash 1234567 da senha',
    criado_em DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uq_email (email)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Tabela de pokémons
-- ------------------------------------------------------------


CREATE TABLE IF NOT EXISTS autor(
    nome_autor varchar(200) not null,
    id_autor int PRIMARY KEY AUTO_INCREMENT

)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS genero (
    id_genero int PRIMARY KEY AUTO_INCREMENT,
    nome_genero varchar(200) not null
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS livro (
    id_livro INT PRIMARY KEY AUTO_INCREMENT, 
    nome_livro varchar(200) not null,
    genero int, 
    FOREIGN KEY (genero) REFERENCES genero(id_genero),
    capa varchar(200),
    usuario_id int,
    FOREIGN KEY (usuario_id)
    REFERENCES usuario(id_usuario),
    nota int not null,
    autor int
    FOREIGN KEY (autor)
    REFERENCES autor(id_autor),
) ENGINE=InnoDB;

create table IF NOT EXISTS livro_autor(
    livro_id int,
    autor_id int,
    FOREIGN KEY (livro_id) REFERENCES livro(id_livro) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES autor(id_autor) ON DELETE CASCADE  
)ENGINE=InnoDB;


-- ------------------------------------------------------------
--  Usuário de teste
--  Email: admin@email.com
--  Senha: 123456  (SHA256 = 8d969eef6ecad3c29a3a629280e686cf...)
-- ------------------------------------------------------------
INSERT INTO usuario (nome, email, senha) VALUES
(
    'elysa',
    'admin@email.com',
    SHA2('1234567',256)
);

-- ------------------------------------------------------------
--  Pokémons de exemplo vinculados ao usuário acima (id=1)
-- ------------------------------------------------------------
INSERT INTO genero(nome_genero)
VALUES
('Romance'),
('Terror'),
('Ficção');

INSERT INTO autor(nome_autor)
VALUES
('Machado de Assis'),
('Stephen King'),
('J.K Rowling');