<?php
/* ============================================================
   ЛР9 / В-1. Записная книжка.
   Главный файл сайта.

   В браузере открывается только index.php.
   Остальные PHP-файлы подключаются как модули.
   ============================================================ */

// Подключаем модуль меню. Он нужен на каждой странице.
require_once 'menu.php';

// Если параметр p не передан, по умолчанию показываем просмотр.
$page = $_GET['p'] ?? 'viewer';

// Разрешенные страницы сайта.
$allowedPages = array('viewer', 'add', 'edit', 'delete');

// Если параметр неправильный, возвращаемся к просмотру.
if (!in_array($page, $allowedPages)) {
    $page = 'viewer';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ЛР9 — Записная книжка</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="wrapper">
    <h1>Записная книжка</h1>

    <!-- Выводим меню, которое возвращает функция из menu.php -->
    <?= getMenu(); ?>

    <div id="content">
        <?php
        // Если выбран просмотр, подключаем viewer.php и вызываем функцию вывода таблицы.
        if ($page == 'viewer') {
            require_once 'viewer.php';

            // Получаем номер страницы пагинации.
            $pg = $_GET['pg'] ?? 0;

            // Получаем тип сортировки.
            $sort = $_GET['sort'] ?? 'byid';

            // Выводим таблицу записей.
            echo getFriendsList($sort, $pg);
        }

        // Остальные модули сами выводят свой HTML-код.
        elseif ($page == 'add') {
            include 'add.php';
        }
        elseif ($page == 'edit') {
            include 'edit.php';
        }
        elseif ($page == 'delete') {
            include 'delete.php';
        }
        ?>
    </div>
</div>
</body>
</html>
