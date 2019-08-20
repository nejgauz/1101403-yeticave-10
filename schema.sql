CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;
USE yeticave;
CREATE TABLE bids (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    CHAR(255) NOT NULL,
    lot_id     CHAR(255) NOT NULL,
    dt_create  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    price      INT
);
CREATE TABLE lots (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    CHAR(255) NOT NULL,
    cat_id     CHAR(255) NOT NULL,
    win_id     CHAR(255),
    dt_create  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title      CHAR(255),
    descr      TEXT,
    image_path CHAR(255),
    st_price   INT,
    dt_end     TIMESTAMP,
    step       INT
);
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    dt_reg     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email      CHAR(255) UNIQUE,
    name       CHAR(255),
    password   CHAR(255),
    avat_path  INT,
    contact    INT
);
CREATE TABLE categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       CHAR(255) NOT NULL,
    symb_code  CHAR(255) NOT NULL
);
CREATE UNIQUE INDEX u_email ON users(email);
CREATE INDEX bid ON bids(price);
CREATE INDEX title ON lots(title);
CREATE INDEX step ON lots(step);
CREATE INDEX descr ON lots(descr);
