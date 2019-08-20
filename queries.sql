USE yeticave;
INSERT INTO categories (name, symb_code)
VALUES ('Доски и лыжи', 'boards')
INSERT INTO categories (name, symb_code)
VALUES ('Крепления', 'attachment');
INSERT INTO categories (name, symb_code)
VALUES ('Ботинки', 'boots');
INSERT INTO categories (name, symb_code)
VALUES ('Одежда', 'clothing');
INSERT INTO categories (name, symb_code)
VALUES ('Инструменты', 'tools');
INSERT INTO categories (name, symb_code)
VALUES ('Разное', 'other');

INSERT INTO users (dt_reg, email, name, password, avat_path, contact)
VALUES ('1564991241', 'rich67@gmail.com', 'Ричард', '123456', NULL, '+7(911)318-10-17');
INSERT INTO users (dt_reg, email, name, password, avat_path, contact)
VALUES ('1563134421', 'melody@mail.com', 'Алиса', 'qr8j0', NULL, '+7(921)516-28-05');
INSERT INTO users (dt_reg, email, name, password, avat_path, contact)
VALUES ('1560864000', 'meloman@mail.com', 'Игорь', 'hg098j', NULL, '+7(920)387-24-16');

INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES ('1', '1', NULL, '1565784921', '2014 Rossignol District Snowboard', NULL, 'img/lot-1.jpg', '10999', '1565902800', '500');
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES ('2', '1', NULL, '1563726000', 'DC Ply Mens 2016/2017 Snowboard', NULL, 'img/lot-2.jpg', '159999', '1566745200', '1000');
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES ('3', '2', NULL, '1561814400', 'Крепления Union Contact Pro 2015 года размер L/XL', NULL, 'img/lot-3.jpg', '8000', '1567112400', '700');
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES ('3', '3', NULL, '1564672500', 'Ботинки для сноуборда DC Mutiny Charocal', NULL, 'img/lot-4.jpg', '10999', '1569078000', '400');
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES ('1', '4', NULL, '1565449200', 'Куртка для сноуборда DC Mutiny Charocal', NULL, 'img/lot-5.jpg', '7500', '1566853200', '100');
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES ('1', '6', NULL, '1565089200', 'Маска Oakley Canopy', NULL, 'img/lot-6.jpg', '5400', '1567458000', '300');

INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES ('1', '2', NOW(), 164999);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES ('2', '5', NOW(), 8500);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES ('3', '6', NOW(), 5700);


