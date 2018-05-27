<?php
declare(strict_types = 1);

require_once __DIR__.'/config.php';
require_once ABSPATH.'/functions.php';
require_once ABSPATH.'/controllers.php';

$id = 0;
//здесь будет функция аутентификации
if (empty($id)) {
    $page = 'registration';
} else {
    $page = $_GET['page'] ?? 'index';
}

$form_state = null;
switch ($page)
{
    case 'index':
        index_control($id);
        break;

    case 'registration':
        registration_control();
        break;

    case 'auth':
        auth_control();
        break;

    case '404':
    default:
        not_found_control($id);
        break;
}