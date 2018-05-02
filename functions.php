<?php

function print_content(string $path, array $array): string
{
    if (false === (file_exists($path))) {
        return "";
    }

    ob_start();

    $page = require_once($path);

    ob_end_flush();

    return $page;
}

?>