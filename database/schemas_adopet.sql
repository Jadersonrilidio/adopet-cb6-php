
--------------------------------
-- DB INIT SCHEMAS FOR SQLITE --
--------------------------------

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    email_verified BOOLEAN NOT NULL,
    password TEXT NOT NULL,
    picture TEXT,
    phone TEXT,
    city TEXT,
    about TEXT,
    role INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS pets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    species INTEGER NOT NULL,
    size INTEGER NOT NULL,
    status INTEGER NOT NULL,
    birth_date TEXT NOT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    picture_url TEXT NOT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS adoptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    pet_id INTEGER NOT NULL,
    status INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

-- species ENUM { dog, cat }
-- size ENUM { Mini, Small, Medium, Large, Giant }
-- status ENUM { New, Available, Adopted, Quarantine, Removed, Suspended }


-------------------------------
-- DB INIT SCHEMAS FOR MYSQL --
-------------------------------

CREATE SCHEMA IF NOT EXISTS adopet_db
    DEFAULT CHARACTER SET = 'utf8'
    DEFAULT COLLATE = 'utf8_general_ci';

USE adopet_db;

CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    email VARCHAR(128) UNIQUE NOT NULL,
    email_verified BOOLEAN NOT NULL,
    password VARCHAR(128) NOT NULL,
    picture VARCHAR(128) NULL,
    phone CHAR(11) NULL,
    city VARCHAR(128) NULL,
    about TEXT NULL,
    role ENUM(0, 1, 2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS pets (
    id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    name VARCHAR(64) NOT NULL,
    description VARCHAR(128) NOT NULL,
    species ENUM(0, 1, 2, 3) NOT NULL,
    size ENUM(0, 1, 2, 3, 4, 5) NOT NULL,
    status ENUM(0, 1, 2, 3, 4) NOT NULL,
    birth_date DATE NOT NULL,
    city VARCHAR(128) NOT NULL,
    state ENUM('AC','AM','AP','AL','MG','...') NOT NULL,
    picture_url VARCHAR(128) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS adoptions (
    id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    pet_id INT(11) NOT NULL,
    status ENUM(0, 1, 2, 3, 4) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

---------------------------------
-- DB INIT SCHEMAS FOR POSTGRE --
---------------------------------
