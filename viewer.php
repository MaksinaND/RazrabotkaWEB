<?php
/* ============================================================
   Модуль viewer.php
   Содержит функцию getFriendsList($sort, $page).
   Она выводит записи из базы данных в виде таблицы.
   Также здесь реализованы сортировка и пагинация.
   ============================================================ */

require_once 'db.php';

function getFriendsList($sort, $page) {
    // Подключаемся к базе данных.
    $mysqli = connectDB();

    // На одной странице должно быть не больше 10 записей.
    $perPage = 10;

    // Номер страницы должен быть целым числом и не меньше нуля.
    $page = max(0, (int)$page);

    // Определяем поле сортировки в зависимости от параметра sort.
    if ($sort == 'surname') {
        $orderBy = 'surname ASC, name ASC';
    } elseif ($sort == 'birth') {
        $orderBy = 'birth_date ASC';
    } else {
        $orderBy = 'id ASC';
        $sort = 'byid';
    }

    // Сначала узнаем общее количество записей.
    $countResult = mysqli_query($mysqli, 'SELECT COUNT(*) AS total FROM contacts');

    // Если запрос не выполнился, возвращаем сообщение об ошибке.
    if (!$countResult) {
        return '<div class="error">Ошибка запроса: ' . h(mysqli_error($mysqli)) . '</div>';
    }

    // Получаем количество записей из результата SQL-запроса.
    $countRow = mysqli_fetch_assoc($countResult);
    $total = (int)$countRow['total'];

    // Если записей нет, выводим сообщение.
    if ($total == 0) {
        return '<div class="error">В таблице нет данных</div>';
    }

    // Считаем количество страниц пагинации.
    $pages = (int)ceil($total / $perPage);

    // Если пользователь указал слишком большую страницу, показываем последнюю.
    if ($page >= $pages) {
        $page = $pages - 1;
    }

    // Вычисляем смещение для SQL LIMIT.
    $offset = $page * $perPage;

    // Запрашиваем только 10 записей для текущей страницы.
    $sql = "SELECT * FROM contacts ORDER BY $orderBy LIMIT $offset, $perPage";
    $result = mysqli_query($mysqli, $sql);

    // Если запрос не выполнился, возвращаем ошибку.
    if (!$result) {
        return '<div class="error">Ошибка выборки: ' . h(mysqli_error($mysqli)) . '</div>';
    }

    // Начинаем формировать таблицу.
    $html = '<h2>Записная книжка</h2>';
    $html .= '<table>';
    $html .= '<tr>';
    $html .= '<th>ID</th><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Пол</th>';
    $html .= '<th>Дата рождения</th><th>Телефон</th><th>Адрес</th><th>E-mail</th><th>Комментарий</th>';
    $html .= '</tr>';

    // Перебираем записи из базы и выводим каждую как строку таблицы.
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>' . h($row['id']) . '</td>';
        $html .= '<td>' . h($row['surname']) . '</td>';
        $html .= '<td>' . h($row['name']) . '</td>';
        $html .= '<td>' . h($row['patronymic']) . '</td>';
        $html .= '<td>' . h($row['gender']) . '</td>';
        $html .= '<td>' . h($row['birth_date']) . '</td>';
        $html .= '<td>' . h($row['phone']) . '</td>';
        $html .= '<td>' . h($row['address']) . '</td>';
        $html .= '<td>' . h($row['email']) . '</td>';
        $html .= '<td>' . h($row['comment']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    // Если страниц больше одной, формируем пагинацию.
    if ($pages > 1) {
        $html .= '<div id="pages">Страницы: ';

        for ($i = 0; $i < $pages; $i++) {
            // Текущая страница выводится не ссылкой, а span.
            if ($i == $page) {
                $html .= '<span>' . ($i + 1) . '</span>';
            } else {
                // В ссылке сохраняем текущий тип сортировки.
                $html .= '<a href="?p=viewer&sort=' . h($sort) . '&pg=' . $i . '">' . ($i + 1) . '</a>';
            }
        }

        $html .= '</div>';
    }

    return $html;
}
?>
