USE yeticave;
INSERT INTO categories (name, symb_code)
VALUES ('Доски и лыжи', 'boards');
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
VALUES ('2019-08-05', 'rich67@gmail.com', 'Ричард', '123456', NULL, '89113181017');
INSERT INTO users (dt_reg, email, name, password, avat_path, contact)
VALUES ('2019-07-14', 'melody@mail.com', 'Алиса', 'qr8j0', NULL, '89215162805');
INSERT INTO users (dt_reg, email, name, password, avat_path, contact)
VALUES ('2019-06-18', 'meloman@mail.com', 'Игорь', 'hg098j', NULL, '89203872416');

INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (1, 1, NULL, '2019-08-10', '2014 Rossignol District Snowboard', NULL, 'img/lot-1.jpg', 10999, '2019-08-16', 500);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (2, 1, NULL, '2019-07-30', 'DC Ply Mens 2016/2017 Snowboard', NULL, 'img/lot-2.jpg', 159999, '2019-08-25', 1000);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (3, 2, NULL, '2019-06-21', 'Крепления Union Contact Pro 2015 года размер L/XL', NULL, 'img/lot-3.jpg', 8000, '2019-08-30', 700);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (3, 3, NULL, '2019-08-01', 'Ботинки для сноуборда DC Mutiny Charocal', NULL, 'img/lot-4.jpg', 10999, '2019-09-21', 400);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (1, 4, NULL, '2019-08-06', 'Куртка для сноуборда DC Mutiny Charocal', NULL, 'img/lot-5.jpg', 7500, '2019-08-27', 100);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (1, 6, NULL, '2019-08-08', 'Маска Oakley Canopy', NULL, 'img/lot-6.jpg', 5400, '2019-09-03', 300);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (1, 1, NULL, NOW(), 'Snowboard Burton FW18', NULL, 'img/lot-7.jpg', 32340, '2019-10-30', 500);

INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (1, 2, NOW(), 164999);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (2, 5, NOW(), 8500);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (3, 6, NOW(), 5700);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (3, 2, NOW(), 170999);

SELECT name FROM categories;

SELECT l.id, title, st_price, image_path, categories.name as category_name, max_bid.max_price as curren_price
FROM lots as l
LEFT JOIN categories
ON categories.id = l.cat_id
JOIN
(SELECT lot_id, MAX(price) as max_price FROM bids GROUP BY lot_id) as max_bid
ON max_bid.lot_id = l.id
WHERE win_id is NULL;

SELECT title, st_price, image_path, c.name AS category_name
FROM lots AS l
LEFT JOIN categories AS c
ON c.id = l.cat_id
WHERE win_id IS NULL AND dt_end > NOW()
ORDER BY l.dt_create DESC LIMIT 9;

SELECT title, name FROM lots l JOIN categories c ON l.cat_id = c.id WHERE l.id = '1';
UPDATE lots SET title = '2015 Rossignol District Snowboard' WHERE id = '1';
SELECT price FROM bids WHERE user_id = '3' ORDER BY dt_create ASC;

