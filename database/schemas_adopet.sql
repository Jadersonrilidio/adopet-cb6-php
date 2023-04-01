
-- DB INIT SCHEMAS FOR SQLITE

CREATE TABLE tutors (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    picture TEXT,
    phone TEXT,
    city TEXT,
    about TEXT,
    created_at TEXT NOT NULL
);


-- DB INIT SCHEMAS FOR MYSQL

CREATE SCHEMA IF NOT EXISTS adopet_db
    DEFAULT CHARACTER SET = 'utf8'
    DEFAULT COLLATE = 'utf8_general_ci';

USE adopet_db;

CREATE TABLE IF NOT EXISTS tutors (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    email VARCHAR(128) UNIQUE NOT NULL,
    password VARCHAR(128) NOT NULL,
    picture VARCHAR(256) NULL,
    phone CHAR(11) NULL,
    city VARCHAR(128) NULL,
    about TEXT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

-- DB INIT SCHEMAS FOR POSTGRE

