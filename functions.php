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
              WHERE t.user_id = ?
              ORDER BY t.id DESC";

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

    $sql = $sql . " ORDER BY id DESC";

    $stmt = db_get_prepare_stmt($con, $sql, $values);

    $tasks = db_get_result_stmt($stmt);

    mysqli_close($con);

    return $tasks;

}

/**
 * Добавляет в БД задачу из полученных из формы данных
 *
 * @param int $user_id
 * @return array
 */
function create_task_from_form (int $user_id): array
{
    $con = connect_to_db();

    $state = $_POST;

    $fname = $_POST['name'] ?? '';
    $fproject = intval($_POST['project']) ?? '';
    $fdate = $_POST['date'] ?? '';
    $ffile = $_FILES['preview'] ?? [];

    $required_fields = ['name', 'project'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $state[$field . '_err'] = 'Необходимо заполнить';
            $state['form_err'] = true;
        }
    }

    $sql = "INSERT
            into tasks
            SET
            created_at = CURRENT_TIMESTAMP,
            name = ?,
            user_id = ?,
            project_id = ?";

    $values = [$fname, $user_id, $fproject];

    if (isset($ffile['size'], $ffile['error']) && $ffile['size'] !== 0 && $ffile['error'] === 0) {
            $file_name = $_FILES['preview']['name'];
            $file_url = '/uploads/' . $file_name;
            $file_path = ABSPATH . $file_url;

            move_uploaded_file($_FILES['preview']['tmp_name'], $file_path);

            $sql = $sql . ", file = ?";

            array_push($values, $file_url);
        }

    if ($fdate !== '') {
        $parsed_date = date_parse_from_format('Y-m-d H:i', $fdate);

        if ($parsed_date['error_count'] === 0) {
            $sql = $sql . ", deadline = ?";
            array_push($values, $fdate);
        }
        else {
            $state['date_err'] = 'Некорректный формат даты';
            $state['form_err'] = true;
        }

    }

    if (isset($state['form_err'])) {
        return $state;
    }

    $stmt = db_get_prepare_stmt($con, $sql, $values);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        die("Ошибка добавления в БД.");
    }

    header('Location: /index.php');

    mysqli_close($con);

    return [];
}

/**
 * Регистрирует нового пользователя
 * по данным из формы
 *
 * @return array
 */
function registration_new_user (): array
{
    $con = connect_to_db();

    $state = $_POST;

    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    $required_fields = ['email', 'password', 'name'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $state[$field . '_err'] = 'Пожалуйста, введите данные.';
            $state['_err'] = true;
        }
    }

    if (empty($state['email_err']) && (false === filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $state['email_err'] = 'Некорректный email';
        $state['_err'] = true;
    } else {
        $sql_for_email = "SELECT count(id)
                      FROM users
                      WHERE email = ?";

        $stmt = db_get_prepare_stmt($con, $sql_for_email, [$email]);
        $is_email_unique = db_get_num_rows_stmt($stmt);

        if (! $is_email_unique) {
            $state['email_err'] = 'Пользователь с таким email уже существует';
            $state['_err'] = true;
        }
    }

    if (isset($state['_err'])) {
        return $state;
    }

    $sql = "INSERT
            INTO users
            SET 
            created_at = CURRENT_TIMESTAMP, 
            email = ?, 
            name = ?, 
            password = ?";

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $values = [$email, $name, $hash_password];
    $stmt = db_get_prepare_stmt($con, $sql, $values);
    mysqli_stmt_execute($stmt);

    mysqli_close($con);

    return [];
}