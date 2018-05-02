<?php

define('ABSPATH', __DIR__);
require_once ABSPATH.'./functions.php';

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

//одномерный массив с перечнем категорий
$categories = ["Все", "Входящие", "Учёба", "Работа", "Домашние дела", "Авто"];

//двумерный массив с задачами
$do_list = [
    [
        "name" => "Собеседование в IT компании",
        "date" => "01.06.2018",
        "category" => "Работа",
        "done" => false,
    ],

    [
        "name" => "Выполнить тестовое задание",
        "date" => "25.05.2018",
        "category" => "Работа",
        "done" => false,
    ],

    [
        "name" => "Сделать задание первого раздела",
        "date" => "21.04.2018",
        "category" => "Учёба",
        "done" => true,
    ],

    [
        "name" => "Встреча с другом",
        "date" => "22.04.2018",
        "category" => "Входящие",
        "done" => false,
    ],

    [
        "name" => "Купить корм для кота",
        "date" => "Нет",
        "category" => "Домашние дела",
        "done" => false,
    ],

    [
        "name" => "Заказать пиццу",
        "date" => "Нет",
        "category" => "Домашние дела",
        "done" => false,
    ]

];

$page_content = render_content(ABSPATH.'./templates/index.php', ['show_complete_tasks' => $show_complete_tasks, 'do_list' => $do_list]);
$layout_content = render_content(ABSPATH.'./templates/layout.php', ['content' => $page_content, 'do_list' => $do_list, 'categories' => $categories, 'title' => 'Дела в порядке - Главная', 'user_name' => 'Константин']);

print($layout_content);