<?php

require_once './functions.php';

$page_content = print_content('./templates/index.php', ['tasks' => $do_list]);
$layout_content = print_content('./templates/layout.php', ['content' => $page_content, 'title' => 'Дела в порядке - Главная']);

print($layout_content);

?>