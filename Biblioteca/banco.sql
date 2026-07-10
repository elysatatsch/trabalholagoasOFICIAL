

CREATE DATABASE IF NOT EXISTS biblioteca
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE biblioteca;


CREATE TABLE IF NOT EXISTS usuario (
    id_usuario       INT          NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(100) NOT NULL,
    email     VARCHAR(150) NOT NULL,
    senha     CHAR(64)     NOT NULL COMMENT 'Hash 1234567 da senha',
    criado_em DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uq_email (email),
) ENGINE=InnoDB;



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
    nome_livro VARCHAR(200) NOT NULL,
    genero INT, 
    usuario_id INT,
    nota INT NOT NULL,
    capa VARCHAR(255) NULL, 
    statuss VARCHAR(200),
    FOREIGN KEY (genero) REFERENCES genero(id_genero),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario),
   
) ENGINE=InnoDB;

create table IF NOT EXISTS livro_autor(
    livro_id int,
    autor_id int,
    FOREIGN KEY (livro_id) REFERENCES livro(id_livro) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES autor(id_autor) ON DELETE CASCADE  
)ENGINE=InnoDB;

create table IF NOT EXISTS tropes(
    trope varchar(200),
    id_trope int AUTO_INCREMENT primary KEY 
)ENGINE=InnoDB;

create table if not exists livro_trope(
    livro_id int,
    tropes_id int,
    FOREIGN KEY (livro_id) REFERENCES livro(id_livro) ON DELETE CASCADE,
    FOREIGN KEY (tropes_id) REFERENCES tropes(id_trope) ON DELETE CASCADE  
)ENGINE=InnoDB;


INSERT INTO usuario (nome, email, senha) VALUES
(
    'elysa',
    'admin@email.com',
    SHA2('1234567',256)
);

INSERT INTO genero(nome_genero)
VALUES
('Romance'),
('Terror/Horror'),
('Ficção')
('fantasia'),
('suspense'),
('Biografia')
('Poema'),
('HQ/mangá');

INSERT INTO tropes (tropes) VALUES
('enimes to lovers'),
('friends to lovers'),
('Slow burn'),
('Only one bed'),
('Grumpy x Sunshine'),
('Clássico'),
('auto ajuda'),
('melancólico'),
('comedia');