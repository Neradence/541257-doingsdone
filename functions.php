<?php
declare(strict_types = 1);

require_once __DIR__.'/config.php';

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
    if (! file_exists($path)) {
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
 * Проверяет, что до полученной даты осталось <= 24 часов
 *
 * @param string $date
 * @return bool
 */
function is_date_important(string $date): bool
{
    if (! strtotime($date)) {
        return false;
    }

    //чтобы получить количество часов
    $dates_subtraction = floor((strtotime($date) - time()) / (60*60));

    return ($dates_subtraction <= 24);
}

/**
 * Соединение с БД
 *
 * @return mysqli
 */
function connect_to_db(): mysqli
{
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$con) {
        die("Ошибка соедиения с базой данных.");
    }
    mysqli_set_charset($con, "utf8");

    return $con;
}

/**
 * Получает все категории для пользователя по его id
 *
 * @param $id
 * @return array
 */
function get_projects_by_user_id($id): array
{
    $con = connect_to_db();

    $sql = "SELECT
              name
              FROM projects
              WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {
        die("Ошибка MySQL ".mysqli_error($con)." в файле ".__FILE__." в строке № ".__LINE__);
    }

    mysqli_stmt_bind_param($stmt, 'd', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Ошибка MySQL " . mysqli_stmt_error($stmt)." в файле ".__FILE__." в строке № ".__LINE__);
    }

    $projects = mysqli_fetch_all($result,MYSQLI_ASSOC);

    //добавляет в начало список Все, который нужен для перечня категорий
    array_unshift($projects, ["name" => 'Все']);

    mysqli_close($con);

    return $projects;

}

/**
 * Получает все задачи пользователя по его id
 *
 * @param $id
 * @return array
 */
function get_tasks_by_user_id($id): array
{
    $con = connect_to_db();

    $sql = "SELECT
              t.name,
              t.deadline as date,
              p.name as category,
              (t.done_at is not null) as done
              FROM tasks t
              JOIN projects p
              ON t.project_id = p.id
              WHERE t.user_id = ?";
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {
        die("Ошибка MySQL ".mysqli_error($con)." в файле ".__FILE__." в строке № ".__LINE__);
    }

    mysqli_stmt_bind_param($stmt, 'd', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Ошибка MySQL " . mysqli_stmt_error($stmt)." в файле ".__FILE__." в строке № ".__LINE__);
    }

    $tasks = mysqli_fetch_all($result,MYSQLI_ASSOC);

    mysqli_close($con);

    return $tasks;

}