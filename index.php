<?php
declare(strict_types = 1);

session_start();

require_once __DIR__.'/config.php';
require_once ABSPATH.'/functions.php';
require_once ABSPATH.'/controllers.php';

$page = $_GET['page'] ?? '';

if (empty($page) && empty($_SESSION['user']['id'])) {
    $page = 'guest-index';
} else if (empty($page)) {
    $page = 'index';
}

$form_state = null;
switch ($page)
{
    case 'guest-index':
        guest_control();
        break;

    case 'index':
        if (!isset($_SESSION['user'])) {
            notrules_control();
            exit;
        }
        index_control();
        break;

    case 'registration':
        registration_control();
        break;

    case 'auth':
        auth_control();
        break;

    case 'logout':
        logout_control();
        break;

    case '404':
    default:
        not_found_control();
        break;
}