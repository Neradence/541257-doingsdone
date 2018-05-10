/*users*/

INSERT INTO users
SET created_at = '2018-05-06 12:46:41', email = 'mary@email.com', name = 'Мария', password = 'password', contacts = '+7 000 000 00 00';

INSERT INTO users
SET created_at = '2018-04-01 06:13:34', email = 'blablabla@email.com', name = 'Константин', password = 'wordpass', contacts = '+7 111 111 11 11';

INSERT INTO users
SET created_at = '2018-04-20 23:28:07', email = 'mix@email.com', name = 'Екатерина', password = 'strong', contacts = '+7 222 222 22 22';

INSERT INTO users
SET created_at = '2018-10-05 22:57:07', email = 'lalaley@email.com', name = 'Григорий', password = 'secret', contacts = '+7 333 333 33 33';

/*projects*/

INSERT INTO projects
SET name = 'Работа', user_id = '2';

INSERT INTO projects
SET name = 'Котик', user_id = '1';

INSERT INTO projects
SET name = 'Личные встречи', user_id = '1';

INSERT INTO projects
SET name = 'Домашние дела', user_id = '3';

INSERT INTO projects
SET name = 'Домашние дела', user_id = '4';

INSERT INTO projects
SET name = 'Котик', user_id = '4';

INSERT INTO projects
SET name = 'Работа', user_id = '4';

/*tasks*/

INSERT INTO tasks
SET deadline = '2018-06-01 12:45:12', name = 'Собеседование в IT компании', user_id = '2', project_id = '1';

INSERT INTO tasks
SET deadline = '2018-05-04 02:18:45', name = 'Выполнить тестовое задание', user_id = '2', project_id = '1';

INSERT INTO tasks
SET deadline = '2018-04-20 04:56:11', name = 'Сделать задание первого раздела', user_id = '2', project_id = '1';

INSERT INTO tasks
SET deadline = '2018-05-22 16:36:05', name = 'Встреча с другом', user_id = '1', project_id = '4';

INSERT INTO tasks
SET name = 'Купить корм для кота', user_id = '3', project_id = '5';

INSERT INTO tasks
SET name = 'Заказать пиццу', user_id = '3', project_id = '5';

INSERT INTO tasks
SET name = 'Купить корм', user_id = '4', project_id = '7';

INSERT INTO tasks
SET deadline = '2018-05-22 16:36:05', name = 'Помыть полы', user_id = '4', project_id = '6';

INSERT INTO tasks
SET name = 'Купить ошейник', user_id = '4', project_id = '7';

INSERT INTO tasks
SET deadline = '2018-05-10 16:36:05', name = 'Заполнить таблицу', user_id = '4', project_id = '8';

/*select*/

/*получить список из всех проектов для одного пользователя*/
SELECT * FROM projects
WHERE user_id = 1;

/*получить список из всех задач для одного проекта*/
SELECT * FROM tasks
WHERE project_id = 1;

/*пометить задачу как выполненную*/
UPDATE tasks SET done_at = CURRENT_TIMESTAMP
WHERE id = 2;

/*получить все задачи для завтрашнего дня*/
SELECT * FROM tasks 
WHERE deadline BETWEEN (CURDATE() + INTERVAL 1 DAY) AND (CURDATE() + INTERVAL 2 DAY);

/*обновить название задачи по её идентификатору*/
UPDATE tasks SET name = 'Новое имя задачи'
WHERE id = 2;