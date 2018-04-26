CREATE DATABASE groupReads;
USE groupReads;

CREATE TABLE usuarios(
    id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    correo varchar(100) UNIQUE NOT NULL,
    nombre varchar(60) NOT NULL,
    apellidos varchar(100) NOT NULL,
    nick varchar(40) NOT NULL,
    fecha date NOT NULL,
    pwd varchar(255) NOT NULL,
    pais char(2) NOT NULL,
    tipo ENUM('n','a','c') NOT NULL
);

CREATE TABLE amigos(
    idUsuario smallint unsigned,
    idAmigo smallint unsigned,
    PRIMARY KEY (idUsuario,idAmigo),
    FOREIGN KEY (idUsuario) REFERENCES usuarios(id),
    FOREIGN KEY (idAmigo) REFERENCES usuarios(id)
);

CREATE TABLE generos(
    id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    nombre varchar(60) NOT NULL
);

CREATE TABLE clubes(
    id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    nombre varchar(60) NOT NULL,
    idCreador smallint unsigned,
    idGenero smallint unsigned NULL,
    FOREIGN KEY (idGenero) REFERENCES generos(id),
    FOREIGN KEY (idCreador) REFERENCES usuarios(id)
);

CREATE TABLE usuarios_clubes(
    idClub smallint unsigned,
    idUsuario smallint unsigned,
    PRIMARY KEY(idClub,idUsuario),
    FOREIGN KEY (idClub) REFERENCES clubes(id),
    FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
);

CREATE TABLE libros(
    isbn varchar(15) PRIMARY KEY,
    titulo varchar(80) NOT NULL,
    paginas smallint NOT NULL,
    fechaAlta date NOT NULL,
    idGenero smallint unsigned NOT NULL,
    FOREIGN KEY (idGenero) REFERENCES generos(id)
);

CREATE TABLE autores(
    id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    nombre_ape varchar(150) NOT NULL,
    fecha_nacimiento date NOT NULL,
    nacionalidad varchar(60) NOT NULL
);

CREATE TABLE autores_libros(
    idAutor smallint unsigned NOT NULL,
    isbnLibro varchar(15) NOT NULL,
    PRIMARY KEY (idAutor,isbnLibro),
    FOREIGN KEY (idAutor) REFERENCES autores(id),
    FOREIGN KEY (isbnLibro) REFERENCES libros(isbn)
);

CREATE TABLE libros_clubes(
    id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    idClub smallint unsigned NOT NULL,
    isbnLibro varchar(15) NOT NULL,
    finalizado boolean NOT NULL DEFAULT 0,
    fecha_fin date NOT NULL,
    FOREIGN KEY (idClub) REFERENCES clubes(id),
    FOREIGN KEY (isbnLibro) REFERENCES libros(isbn)
);

CREATE TABLE comentarios_libros(
    idComentario smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    idClub smallint unsigned NOT NULL,
    idUsuario smallint unsigned NOT NULL,
    isbnLibro varchar(15) NOT NULL,
    FOREIGN KEY (idClub) REFERENCES clubes(id),
    FOREIGN KEY (idUsuario) REFERENCES usuarios(id),
    FOREIGN KEY (isbnLibro) REFERENCES libros(isbn)
);

CREATE TABLE libros_para_votar(
    id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    idClub smallint unsigned NOT NULL,
    isbn1 varchar(15) NOT NULL,
    isbn2 varchar(15) NOT NULL,
    isbn3 varchar(15) NOT NULL,
    finalizado boolean DEFAULT 0 NOT NULL,
    FOREIGN KEY (idClub) REFERENCES clubes(id),
    FOREIGN KEY (isbn1) REFERENCES libros(isbn),
    FOREIGN KEY (isbn2) REFERENCES libros(isbn),
    FOREIGN KEY (isbn3) REFERENCES libros(isbn)
);

CREATE TABLE votaciones(
    id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
    idClub smallint unsigned NOT NULL,
    idUsuario smallint unsigned NOT NULL,
    voto varchar(15) NOT NULL,
    FOREIGN KEY (idClub) REFERENCES clubes(id),
    FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
);