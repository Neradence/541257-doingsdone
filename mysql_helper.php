<?php

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        die("Ошибка MySQL ".mysqli_error($link)." в файле ".__FILE__." в строке № ".__LINE__);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}

/**
 * Из подготовленнного stmt запроса возвращает результат в виде набора ассоциативных массивов
 *
 * @param $stmt
 * @return array
 */
function db_get_result_stmt($stmt): array
{
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Ошибка MySQL " . mysqli_stmt_error($stmt)." в файле ".__FILE__." в строке № ".__LINE__);
    }

    return mysqli_fetch_all($result,MYSQLI_ASSOC);
}