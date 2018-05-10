<?php
declare(strict_types = 1);

require_once __DIR__.'/config.php';
require_once ABSPATH.'/functions.php';

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$con = mysqli_connect("localhost", "root", "", doingsdone);

if (!$con) {
    print("Ошибка подключения: " . mysqli_connect_error());
}

$id = 2;

$sql = "SELECT DISTINCT name FROM projects WHERE user_id = (?)";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'd', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$categories = gone_to_simple_array(mysqli_fetch_all($result));

$sql = "SELECT t.name, t.deadline as date, p.name as category, (t.done_at is not null) as done FROM tasks t JOIN projects p ON t.project_id = p.id WHERE t.user_id = (?)";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'd', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$do_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

$page_content = render_content(TEMPPATH.'/index.php',
    [
        'show_complete_tasks' => $show_complete_tasks,
        'do_list' => $do_list
    ]);
$layout_content = render_content(TEMPPATH.'/layout.php',
    [
        'content' => $page_content,
        'do_list' => $do_list,
        'categories' => $categories,
        'title' => 'Дела в порядке - Главная',
        'user_name' => 'Константин'
    ]);

print($layout_content);