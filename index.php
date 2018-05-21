<?php
declare(strict_types = 1);

require_once __DIR__.'/config.php';
require_once ABSPATH.'/functions.php';

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$id = 4;

$form_state = null;
if (isset($_POST['form_type'])) {
    switch ($_POST['form_type'])
    {
        case 'add_task':
            $form_state = create_task_from_form($id);
            break;
    }
}

$project_id = isset($_GET['proj']) ? intval($_GET['proj']) : null;
$projects = get_tasks_for_one_project($id, $project_id);

if (0 === $project_id || 0 === count($projects)) {
    http_response_code(404);

    $page_content = render_content(TEMPPATH.'/404.php');
    $layout_content = render_content(TEMPPATH.'/layout.php',
        [
            'content' => $page_content,
            'do_list' => get_tasks_by_user_id($id),
            'categories' => get_projects_by_user_id($id),
            'title' => 'Дела в порядке - 404',
            'user_name' => 'Константин'
        ]);
    print($layout_content);
    exit();
}

$page_content = render_content(TEMPPATH.'/index.php',
    [
        'show_complete_tasks' => $show_complete_tasks,
        'do_list' => $projects,
        'id' => $id
    ]);
$layout_content = render_content(TEMPPATH.'/layout.php',
    [
        'content' => $page_content,
        'do_list' => get_tasks_by_user_id($id),
        'categories' => get_projects_by_user_id($id),
        'title' => 'Дела в порядке - Главная',
        'user_name' => 'Константин',
        'formstate' => $form_state
    ]);

print($layout_content);