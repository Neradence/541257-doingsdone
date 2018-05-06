/*users*/

INSERT INTO users
SET created_at = '2018-05-06 12:46:41', email = 'mary@email.com', name = 'Мария', password = 'password', contacts = '+7 000 000 00 00';

INSERT INTO users
SET created_at = '2018-04-01 06:13:34', email = 'blablabla@email.com', name = 'Константин', password = 'wordpass', contacts = '+7 111 111 11 11';

INSERT INTO users
SET created_at = '2018-04-20 23:28:07', email = 'mix@email.com', name = 'Екатерина', password = 'strong', contacts = '+7 222 22 22 22';

/*projects*/

INSERT INTO projects
SET name = 'Работа', user_id = '2';

INSERT INTO projects
SET name = 'Котик', user_id = '1';

INSERT INTO projects
SET name = 'Личные встречи', user_id = '1';

INSERT INTO projects
SET name = 'Домашние дела', user_id = '3';

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

/*select*/

/*получить список из всех проектов для одного пользователя*/
SELECT p.name, u.name FROM projects p JOIN users u ON p.user_id = u.id
WHERE user_id = 1;

/*получить список из всех задач для одного проекта*/
SELECT t.name, p.name FROM tasks t JOIN projects p ON t.project_id = p.id
WHERE project_id = 1;

/*пометить задачу как выполненную*/
UPDATE tasks SET done_at = CURRENT_TIMESTAMP
WHERE id = 2;

/*получить все задачи для завтрашнего дня*/
SELECT name FROM tasks 
WHERE (deadline > '2018-05-07 00:00:00') and (deadline < '2018-05-08 00:00:00');

/*обновить название задачи по её идентификатору*/
UPDATE tasks SET name = 'Новое имя задачи'
WHERE id = 2;