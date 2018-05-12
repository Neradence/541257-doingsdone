<?php
declare(strict_types = 1);

require_once __DIR__.'/config.php';
require_once ABSPATH.'/functions.php';

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$con -> set_charset(utf8);

if (!$con) {
    die($error_message);
}

$id = 4;

$page_content = render_content(TEMPPATH.'/index.php',
    [
        'show_complete_tasks' => $show_complete_tasks,
        'do_list' => get_tasks_by_user_id($con, $id),
    ]);
$layout_content = render_content(TEMPPATH.'/layout.php',
    [
        'content' => $page_content,
        'do_list' => get_tasks_by_user_id($con, $id),
        'categories' => get_projects_by_user_id($con, $id),
        'title' => 'Дела в порядке - Главная',
        'user_name' => 'Константин'
    ]);

print($layout_content);