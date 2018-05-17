<?php
declare(strict_types = 1);

require_once __DIR__.'/config.php';
require_once __DIR__.'/mysql_helper.php';

/**
 * Собирает содержимое в буфер и выводит его
 *
 * @param string $path
 * @param array $array
 *
 * @return string
 */
function render_content(string $path, array $array = []): string
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
 * @param int $id
 * @return array
 */
function get_projects_by_user_id(int $id): array
{
    $con = connect_to_db();

    $sql = "SELECT
              id, name
              FROM projects
              WHERE user_id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$id]);

    $projects = db_get_result_stmt($stmt);

    //добавляет в начало список Все, который нужен для перечня категорий
    array_unshift($projects, ["name" => 'Все']);

    mysqli_close($con);

    return $projects;

}

/**
 * Получает все задачи пользователя по его id
 *
 * @param int $id
 * @return array
 */
function get_tasks_by_user_id(int $id): array
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

    $stmt = db_get_prepare_stmt($con, $sql, [$id]);

    $tasks = db_get_result_stmt($stmt);

    mysqli_close($con);

    return $tasks;

}

/**
 * Возвращает задачи только для выбранной категории
 *
 * @param int $user_id
 * @param int|null $project_id
 * @return array
 */
function get_tasks_for_one_project(int $user_id, ?int $project_id): array
{
    $con = connect_to_db();

    $sql = "SELECT 
              name,
              deadline as date,
              (done_at is not null) as done
              FROM tasks
              WHERE user_id = ?";

    $values = [$user_id];

    if (!is_null($project_id)) {
        $sql = $sql . " AND project_id = ?";
        array_push($values, $project_id);
    }

    $stmt = db_get_prepare_stmt($con, $sql, $values);

    $tasks = db_get_result_stmt($stmt);

    mysqli_close($con);

    return $tasks;

}