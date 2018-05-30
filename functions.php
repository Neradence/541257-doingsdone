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
    array_unshift($projects, ["name" => 'Все', 'id' => 0]);

    mysqli_close($con);

    return $projects;

}

/**
 * Определяет, есть ли у пользователя права
 * на запрошенный проект
 *
 * @param int $user_id
 * @param int $project_id
 * @return bool
 */
function check_right_user_project (int $user_id, int $project_id): bool
{
    $con = connect_to_db();

    $sql = "SELECT COUNT(*) as count FROM projects WHERE user_id = ? AND id = ?";

    $values = [$user_id, $project_id];

    $stmt = db_get_prepare_stmt($con, $sql, $values);
    $result = db_get_result_stmt($stmt);

    mysqli_close($con);

    return intval($result[0]['count']) === 1;
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
              t.id as taskid,
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
 * @param null $filter
 * @return array
 */
function get_tasks_for_one_project(int $user_id, ?int $project_id, $filter = null): array
{
    $con = connect_to_db();

    $sql = "SELECT 
              id as taskid,
              name,
              deadline as date,
              (done_at is not null) as done,
              file as user_file
              FROM tasks
              WHERE user_id = ?";

    $values = [$user_id];

    if (!is_null($project_id)) {
        $sql = $sql . " AND project_id = ?";
        array_push($values, $project_id);
    }

    if (!empty($filter)) {
        switch ($filter)
        {
            case 'now':
                $sql = $sql . " AND date(deadline) = date(now())";
                break;
            case 'tomorrow':
                $sql = $sql . " AND date(deadline) = date(now() + INTERVAL 1 DAY)";
                break;
            case 'ago':
                $sql = $sql . " AND date(deadline) < date(now())";
                break;
            default:
                not_found_control ();
                exit();
        }
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
    $fproject = isset($_POST['project']) ? intval($_POST['project']) : 0;
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
            $file_url = '/uploads/' . $user_id . '-'. time() . '-'. $file_name;
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
 * Создаёт проект из формы и добавляет его в БД
 *
 * @param int $user_id
 * @return array
 */
function create_project_from_form (int $user_id): array
{
    $con = connect_to_db();

    $state = $_POST;

    $project_name = $_POST['name'] ?? null;

    if (empty($project_name)) {
        $state['_err'] = true;
        $state['form_err'] = 'Пожалуйста, укажите имя проекта.';
        return $state;
    }

    $sql = "INSERT
            INTO projects
            SET 
            name = ?, 
            user_id = ?";

    $values = [$project_name, $user_id];

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
 * Инвертирует статус задачи на выполнено/нет
 * по клику чекбокса
 *
 * @param $task_id
 * @param bool $check
 * @return bool
 */
function invert_done_task ($task_id, $check = false): bool
{
    $con = connect_to_db();

    if (empty($check)) {
        $sql_done = "UPDATE tasks SET done_at = NULL WHERE id='" . $task_id . "'";
    } else {
        $sql_done = "UPDATE tasks SET done_at = CURRENT_TIMESTAMP WHERE id='" . $task_id . "'";
    }

    $result = mysqli_query($con, $sql_done);

    if (!$result) {
        die("Ошибка добавления в БД.");
    }

    mysqli_close($con);

    return true;
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

    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $name = $_POST['name'] ?? null;

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
        $state['form_err'] = 'Пожалуйста, заполните форму правильно.';
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

/**
 * Аутентификация пользователя, возвращает
 * или массив ошибок, или массив данных пользователя из БД,
 * в случае успешной аутентификации устанавливает
 * в переменные сессии user данные пользователя
 *
 * @return array
 */
function auth_user () : array
{
    $con = connect_to_db();

    $state = $_POST;

    $required_fields = ['email', 'password'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $state[$field . '_err'] = 'Пожалуйста, введите данные.';
            $state['_err'] = true;
        }
    }

    if(isset($state['_err'])) {
        return ['successful' => false, 'state' => $state];
    }

    if (isset($_POST['email'], $_POST['password'])) {
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = $_POST['password'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $state['email_err'] = 'Пожалуйста, введите корректный email.';
        $state['_err'] = true;
        return ['successful' => false, 'state' => $state];
    }

    $sql = "SELECT * FROM users WHERE email = '" . $email . "'";
    $result = mysqli_query($con, $sql);

    $user = $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : null;
    if (!empty($user)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['user']['id'] = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;
        }
        else {
            $state['password_err'] = 'Неверный пароль';
            $state['_err'] = true;
        }
    }
    else {
        $state['email_err'] = 'Такой пользователь не найден';
        $state['_err'] = true;
    }

    if (isset($state['_err'])) {
        $state['form_err'] = 'Пожалуйста, заполните форму правильно.';
        return ['successful' => false, 'state' => $state];
    }

    mysqli_close($con);

    return ['successful' => true, 'user' => $user];
}