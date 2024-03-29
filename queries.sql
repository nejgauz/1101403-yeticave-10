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

INSERT INTO users (dt_reg, email, name, password, contact)
VALUES ('2019-08-05', 'rich67@gmail.com', 'Ричард', '123456', 'тел. 89113181017');
INSERT INTO users (dt_reg, email, name, password, contact)
VALUES ('2019-07-14', 'melody@mail.com', 'Алиса', 'одуванчик', 'тел. 89215162805');
INSERT INTO users (dt_reg, email, name, password, contact)
VALUES ('2019-06-18', 'meloman@mail.com', 'Игорь', 'туса', 'тел. 89203872416');

INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (5, 1, NULL, '2019-08-10', '2014 Rossignol District Snowboard', 'Прекрасная доска для трюков. Яркий дизайн и надежность.', 'img/lot-1.jpg', 10999, '2019-10-16', 500);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (6, 1, NULL, '2019-07-30', 'DC Ply Mens 2016/2017 Snowboard', 'Сноуборд для настоящих любителей экстрима.', 'img/lot-2.jpg', 159999, '2019-11-25', 1000);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (5, 2, NULL, '2019-06-21', 'Крепления Union Contact Pro 2015 года размер L/XL', 'Надежные и прочные крепления, немного маломерят.', 'img/lot-3.jpg', 8000, '2019-10-30', 700);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (5, 3, NULL, '2019-08-01', 'Ботинки для сноуборда DC Mutiny Charocal', 'Крепкие ботинки, прослужат не один сезон.', 'img/lot-4.jpg', 10999, '2019-10-21', 400);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (6, 4, NULL, '2019-08-06', 'Куртка для сноуборда DC Mutiny Charocal', 'Теплая куртка с хорошей терморегуляцией. Поддерживает комфортную температуту тела.', 'img/lot-5.jpg', 7500, '2019-10-10', 100);
INSERT INTO lots (user_id, cat_id, win_id, dt_create, title, descr, image_path, st_price, dt_end, step)
VALUES (6, 6, NULL, '2019-08-08', 'Маска Oakley Canopy', 'Маска для защиты. Стильный дизайн и хорошее качество.', 'img/lot-6.jpg', 5400, '2019-10-16', 300);


INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (1, 2, NOW(), 164999);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (2, 5, NOW(), 8500);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (3, 6, NOW(), 5700);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (3, 2, NOW(), 170999);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (2, 1, NOW(), 11999);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (1, 3, NOW(), 9400);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (1, 4, NOW(), 11799);
INSERT INTO bids (user_id, lot_id, dt_create, price)
VALUES (2, 7, NOW(), 33500);

INSERT INTO stop_words SET value = 'и';
INSERT INTO stop_words SET value = 'или';
INSERT INTO stop_words SET value = 'у';
INSERT INTO stop_words SET value = 'в';
INSERT INTO stop_words SET value = 'на';
INSERT INTO stop_words SET value = 'из';
INSERT INTO stop_words SET value = 'под';
INSERT INTO stop_words SET value = 'около';
INSERT INTO stop_words SET value = 'он';
INSERT INTO stop_words SET value = 'оно';
INSERT INTO stop_words SET value = 'она';
INSERT INTO stop_words SET value = 'они';
INSERT INTO stop_words SET value = 'то';
INSERT INTO stop_words SET value = 'что';

SELECT name FROM categories;


SELECT title, st_price, image_path, c.name AS category_name
FROM lots AS l
LEFT JOIN categories AS c
ON c.id = l.cat_id
WHERE win_id IS NULL AND dt_end > NOW()
ORDER BY l.dt_create DESC LIMIT 9;

SELECT title, name FROM lots l JOIN categories c ON l.cat_id = c.id WHERE l.id = '1';
UPDATE lots SET title = '2015 Rossignol District Snowboard' WHERE id = '1';
SELECT price FROM bids WHERE user_id = '3' ORDER BY dt_create ASC;

