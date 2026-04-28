<?php
    /*
        Лабораторная работа №3
        Использование GET-параметров в ссылках.
        Виртуальная клавиатура.
        Студент: Максина Надежда Дмитриевна
        Группа: 241-353
        Вариант: 13
        Вся функциональность реализована через PHP и GET-параметры.
    */

    $studentName = 'Максина Надежда Дмитриевна';
    $studentGroup = '241-353';
    $labTitle = 'Лабораторная работа №3';
    $variant = 'Вариант 13';
    $pageTitle = $studentName . ' — ' . $studentGroup . ' — ' . $labTitle . ' — ' . $variant;


    /*
        Если параметр store не передан, значит это первая загрузка страницы
        или был нажат сброс. Тогда результат должен быть пустым.
    */

    if (!isset($_GET['store'])) {
        $_GET['store'] = '';
    }


    /*
        Если параметр count не передан, значит кнопки ещё не нажимались.
        Поэтому счётчик нажатий равен нулю.
    */

    if (!isset($_GET['count'])) {
        $_GET['count'] = 0;
    }

    /*
        Если параметр key передан, значит была нажата цифровая кнопка.
        Значение key добавляется к текущей строке результата.
        После этого счётчик нажатий увеличивается на 1.
    */

if (isset($_GET['key'])) {
    if ($_GET['key'] == 'reset') {
        $_GET['store'] = '';
    } else {
        $_GET['store'] .= $_GET['key'];
    }

    $_GET['count']++;
}

    $store = $_GET['store']; // Сохраняем текущее значение результата в отдельную переменную для удобства.
    $count = $_GET['count']; // Сохраняем количество нажатий в отдельную переменную для удобства.
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <img src="logo.png" alt="Логотип университета" class="logo">

    <div class="header-text">
        <strong><?php echo $studentName; ?></strong><br>
        Группа: <?php echo $studentGroup; ?><br>
        <?php echo $labTitle; ?>, <?php echo $variant; ?>
    </div>
</header>

<main>
    <h1>Виртуальная клавиатура</h1>

    <p class="description">
        Нажатия кнопок передаются через GET-параметры.
        Предыдущее значение результата хранится в параметре <code>store</code>,
        а общее количество нажатий — в параметре <code>count</code>.
    </p>

    <div class="result">
        
        <?php echo htmlspecialchars($store); 
                    /*
                Выводим текущую строку результата.
                htmlspecialchars нужен, чтобы безопасно вывести данные из адресной строки.
                    */
            ?> 
    </div>

    <div class="keyboard">
        <?php
            /*
                Цикл формирует кнопки от 1 до 9.
                Каждая кнопка — это ссылка с GET-параметрами:
                key — нажатая цифра,
                store — текущий результат,
                count — текущее число нажатий.
            */
            for ($i = 1; $i <= 9; $i++) {
                echo '<a class="btn" href="?key=' . $i .
                     '&store=' . htmlspecialchars($store) .
                     '&count=' . $count . '">' . $i . '</a>';

                if ($i == 5)
                        /*
                    После кнопки 5 добавляем перенос строки,
                    чтобы клавиатура визуально была похожа на пример из задания.
                        */
                    {
                    echo '<br>';
                }
            }
            /*
                Отдельно формируем кнопку 0.
                Она также передаёт key, store и count через ссылку.
            */
            echo '<a class="btn" href="?key=0' .
                 '&store=' . htmlspecialchars($store) .
                 '&count=' . $count . '">0</a>';
        ?>
    </div>
    <!-- Сброс открывает страницу без GET-параметров, поэтому результат очищается. -->
    <a class="reset" href="?key=reset&store=<?php echo htmlspecialchars($store); ?>&count=<?php echo $count; ?>">СБРОС</a>
</main>

<footer>
    Общее число нажатий кнопок: <?php echo $count; ?>
</footer>

</body>
</html>
