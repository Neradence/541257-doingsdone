<?php

declare(strict_types = 1);

require_once __DIR__.'/config.php';
require_once __DIR__.'/mysql_helper.php';

/**
 * рендеринг главной страницы для неавторизованных пользователей
 */
function guest_control ()
{
    $page_content = render_content(TEMPPATH.'/guest.php');
    $layout_content = render_content(TEMPPATH.'/guest-layout.php',
    [
        'background' => true,
        'title' => 'Дела в порядке - Welcome!',
        'content' => $page_content,
        'formstate' => auth_control()
    ]);

    print ($layout_content);
}

/**
 * рендеринг страницы регистрации
 * и обработка формы регистрации
 */
function registration_control ()
{
    $auth_form_state = auth_control();

    if (isset($_POST['form_type']) && $_POST['form_type'] === 'add_user') {
        $form_state = registration_new_user();
        if (!isset($form_state['_err'])) {
            $auth_form_state['show'] = true;
        }
    }

    $page_content = render_content(TEMPPATH.'/registration.php',
        [
            'formstate' => $form_state ?? []
        ]);
    $layout_content = render_content(TEMPPATH.'/guest-layout.php',
        [
            'content' => $page_content,
            'title' => 'Дела в порядке - Регистрация',
            'formstate' => $auth_form_state
        ]);

    print($layout_content);
}

/**
 * обработка формы аутентификации пользователя
 *
 * @return array
 */
function auth_control () : array
{
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'login_user') {
        $auth_res = auth_user();

        if ($auth_res['successful'])
        {
            header("Location: /index.php");
            exit;
        }
        else
        {
            return $auth_res['state'];
        }
    }
    return [];
}

/**
 * рендеринг главной страницы для авторизованного пользователя,
 * показывает проекты и задачи
 */
function index_control ()
{
    $id = $_SESSION['user']['id'];

    if (isset($_POST['form_type']) && $_POST['form_type'] === 'add_task') {
        $form_state = create_task_from_form($id);
    }
    $project_id = isset($_GET['proj']) ? intval($_GET['proj']) : null;
    $projects = get_tasks_for_one_project($id, $project_id);
    if (0 === $project_id || 0 === count($projects)) {
        not_found_control ($id);
        return;
    }
    $page_content = render_content(TEMPPATH.'/index.php',
        [
            'show_complete_tasks' => SHOW_COMPLETE_TASKS,
            'do_list' => $projects,
            'id' => $id
        ]);
    $layout_content = render_content(TEMPPATH.'/layout.php',
        [
            'content' => $page_content,
            'do_list' => get_tasks_by_user_id($id),
            'categories' => get_projects_by_user_id($id),
            'title' => 'Дела в порядке - Главная',
            'user_name' => $_SESSION['user']['name'],
            'formstate' => $form_state
        ]);

    print($layout_content);
}

/**
 * рендеринг 404 страницы для авторизованного пользователя
 */
function not_found_control ()
{
    http_response_code(404);
    $page_content = render_content(TEMPPATH.'/404.php');

    if (isset($_SESSION['user'])) {
        $id = $_SESSION['user']['id'];
        $layout_content = render_content(TEMPPATH . '/layout.php',
            [
                'content' => $page_content,
                'do_list' => get_tasks_by_user_id($id),
                'categories' => get_projects_by_user_id($id),
                'title' => 'Дела в порядке - 404',
                'user_name' => $_SESSION['user']['name']
            ]);
    } else {
        $layout_content = render_content(TEMPPATH.'/guest-layout.php',
            [
                'background' => true,
                'title' => 'Дела в порядке - 404',
                'content' => $page_content,
                'formstate' => auth_control()
            ]);
    }
    print($layout_content);
}

/**
 * выход из аккаунта пользователя,
 * возврат на главную страницу
 */
function logout_control ()
{
    unset($_SESSION['user']);

    header("Location: /index.php");
}

/**
 * рендер ошибки неавторизованного доступа
 */
function notrules_control ()
{
    http_response_code(401);

    $page_content = render_content(TEMPPATH.'/401.php');

    if (isset($_SESSION['user'])) {
        $id = $_SESSION['user']['id'];
        $layout_content = render_content(TEMPPATH . '/layout.php',
            [
                'content' => $page_content,
                'do_list' => get_tasks_by_user_id($id),
                'categories' => get_projects_by_user_id($id),
                'title' => 'Дела в порядке - 401',
                'user_name' => $_SESSION['user']['name']
            ]);
    } else {
        $layout_content = render_content(TEMPPATH.'/guest-layout.php',
            [
                'background' => true,
                'title' => 'Дела в порядке - 401',
                'content' => $page_content,
                'formstate' => auth_control()
            ]);
    }
    print($layout_content);
}