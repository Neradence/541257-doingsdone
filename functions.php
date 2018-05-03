<?php

/**
 * Собирает содержимое в буфер и выводит его
 *
 * @param string $path
 * @param array $array
 *
 * @return string
 */
function render_content(string $path, array $array): string
{
    if (false === (file_exists($path))) {
        return "";
    }

    ob_start();
    extract($array, EXTR_OVERWRITE);
    require_once($path);
    return ob_get_clean();
}

/**
 * Считает количество проектов из одной категории
 *
 * @param array $projects
 * @param string $name
 *
 * @return int
 */
function projects_count(array $projects, string $name): int
{
    if ('Все' === $name) {
        return count($projects);
    }

    $count = 0;

    foreach ($projects as $key) {
        if (isset($key['category']) && ($name === $key['category'])) {
            $count++;
        }
    }

    return $count;

}

/**
 * Считает количество оставшихся до указанной даты часов, false - если их меньше 24, и задача переходит в важные
 *
 * @param string $date
 * @return int
 */
function important_date(string $date): int
{
    if ('Нет' === $date) {
        return false;
    }

    //делим на количество секунд в часах, чтобы получить количество часов и, если осталось <= 24, выводить предупреждение
    $dates_subtraction = floor((strtotime($date) - strtotime(date("d-m-Y"))) / 3600);

    return ($dates_subtraction <= 24);
}