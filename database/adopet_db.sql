
CREATE SCHEMA IF NOT EXISTS adopet_db
    DEFAULT CHARACTER SET 'utf8'
    DEFAULT COLLATE 'utf8_general_ci';

USE adopet_db;

CREATE TABLE IF NOT EXISTS tutors (
    id INT(11) UNIQUE AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    email VARCHAR(128) UNIQUE NOT NULL,
    password VARCHAR(128) NOT NULL
);

CREATE TABLE IF NOT EXISTS responsible (
    id INT(11) UNIQUE AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    email VARCHAR(128) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS pets (
    id INT(11) UNIQUE AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    age VARCHAR(128) UNIQUE NOT NULL,
    size VARCHAR(128) NOT NULL,
    attributes VARCHAR(128) NOT NULL,
    city VARCHAR(128) NOT NULL,
    state_abbrev VARCHAR(128) NOT NULL
);

ALTER TABLE IF EXISTS tutors (
    ADD COLUMN picture VARCHAR(128) NOT NULL,
    ADD COLUMN phone VARCHAR(128) NOT NULL,
    ADD COLUMN city VARCHAR(128) NOT NULL,
    ADD COLUMN about VARCHAR(128) NOT NULL
);
