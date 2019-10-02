CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;
USE yeticave;
CREATE TABLE bids (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    lot_id     INT NOT NULL,
    dt_create  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    price      INT
);
CREATE TABLE lots (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT DEFAULT NULL,
    cat_id     INT NOT NULL,
    win_id     INT DEFAULT NULL,
    dt_create  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title      CHAR(64),
    descr      TEXT(65535),
    image_path CHAR(255),
    st_price   INT,
    dt_end     TIMESTAMP,
    step       INT
);
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    dt_reg     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email      CHAR(64) UNIQUE,
    name       CHAR(64),
    password   CHAR(255),
    contact    CHAR(255)
);
CREATE TABLE categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       CHAR(64) NOT NULL,
    symb_code  CHAR(64) NOT NULL
);
CREATE TABLE stop_words (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    value      CHAR(255)
);

CREATE FULLTEXT INDEX lot_search ON lots(title, descr);
