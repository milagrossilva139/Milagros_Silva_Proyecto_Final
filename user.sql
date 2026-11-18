CREATE DATABASE sistema_usuarios_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;
USE sistema_usuarios_db;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(254) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    estado ENUM('activo', 'archivado') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_archivado TIMESTAMP NULL DEFAULT NULL
);