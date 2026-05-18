<?php
/* ============================================================
   Лабораторная работа № А-6
   Тема: Использование форм для передачи данных в PHP.
   Математический тест.

   Эта страница работает в двух режимах:
   1. Если форма ещё не отправлена — показывается форма.
   2. Если форма отправлена — данные обрабатываются и выводится результат.

   Все делается в одном файле index.php.
   ============================================================ */


/* ---------- Функция преобразования введенного значения в число ---------- */

// Пользователь может ввести дробное число через запятую: 2,5.
// PHP нормально понимает дроби через точку: 2.5.
// Поэтому функция заменяет запятую на точку и переводит строку в число.
function toFloat($val) {
    return floatval(str_replace(',', '.', $val));
}


/* ---------- Проверяем, была ли отправлена форма ---------- */

// Если в массиве $_POST есть элемент A,
// значит пользователь нажал кнопку "Проверить"
// и форма отправила данные на сервер.
$form_sent = isset($_POST['A']);

// В эту переменную будем собирать HTML-код отчета.
// Потом этот отчет можно вывести на страницу и использовать для письма.
$out_text = "";

// В этой переменной будет храниться правильный ответ,
// который вычислит программа.
$result = null;


/* ---------- Обработка данных формы ---------- */

// Этот блок выполняется только после отправки формы.
if ($form_sent) {

    // Получаем значение A из формы и переводим его в число.
    $A = toFloat($_POST['A']);

    // Получаем значение B из формы и переводим его в число.
    $B = toFloat($_POST['B']);

    // Получаем значение C из формы и переводим его в число.
    $C = toFloat($_POST['C']);

    // Проверяем поле "Ваш ответ".
    // Если оно не пустое — переводим ответ пользователя в число.
    // Если пустое — записываем null, чтобы потом вывести сообщение,
    // что задача самостоятельно решена не была.
    $user_result = $_POST['user_result'] !== "" ? toFloat($_POST['user_result']) : null;

    // Получаем выбранную задачу из выпадающего списка.
    // Например: mean, perimeter, area и т.д.
    $task = $_POST['TASK'];

    // Здесь будет храниться обычное русское название выбранной задачи.
    $task_name = "";

    // switch проверяет, какую задачу выбрал пользователь,
    // и в зависимости от этого выполняет нужную формулу.
    switch ($task) {

        case 'mean':
            // Среднее арифметическое считается как сумма A, B, C,
            // деленная на количество чисел, то есть на 3.
            $result = round(($A + $B + $C) / 3, 2);
            $task_name = "Среднее арифметическое";
            break;

        case 'perimeter':
            // Периметр треугольника — это сумма трех сторон.
            $result = round($A + $B + $C, 2);
            $task_name = "Периметр треугольника";
            break;

        case 'area':
            // Площадь треугольника считаем по формуле Герона.
            // Сначала находим полупериметр.
            $p = ($A + $B + $C) / 2;

            // Проверяем, может ли существовать треугольник
            // с такими сторонами.
            // Если сумма двух сторон меньше или равна третьей,
            // треугольник построить нельзя.
            if ($A + $B <= $C || $A + $C <= $B || $B + $C <= $A) {
                $result = null;
            } else {
                // Если треугольник существует, считаем площадь.
                $result = round(sqrt($p * ($p - $A) * ($p - $B) * ($p - $C)), 2);
            }

            $task_name = "Площадь треугольника";
            break;

        case 'volume':
            // Объем параллелепипеда равен произведению трех измерений.
            $result = round($A * $B * $C, 2);
            $task_name = "Объем параллелепипеда";
            break;

        case 'max':
            // Функция max() выбирает наибольшее из трех чисел.
            $result = max($A, $B, $C);
            $task_name = "Максимальное значение";
            break;

        case 'sum_sq':
            // Сумма квадратов — это A² + B² + C².
            $result = round($A * $A + $B * $B + $C * $C, 2);
            $task_name = "Сумма квадратов";
            break;
    }

    /* ---------- Формирование отчета ---------- */

    // Начинаем формировать HTML-отчет с заголовка.
    $out_text .= "<h2>Результаты теста</h2>";

    // Добавляем ФИО студента.
    // htmlspecialchars() нужен, чтобы безопасно вывести текст в HTML.
    $out_text .= "<p><b>ФИО:</b> " . htmlspecialchars($_POST['FIO']) . "</p>";

    // Добавляем номер группы.
    $out_text .= "<p><b>Группа:</b> " . htmlspecialchars($_POST['GROUP']) . "</p>";

    // Если пользователь заполнил поле "Немного о себе",
    // добавляем этот текст в отчет.
    if (!empty($_POST['ABOUT'])) {
        // nl2br() сохраняет переносы строк из textarea.
        $out_text .= "<p><b>Немного о себе:</b><br>" . nl2br(htmlspecialchars($_POST['ABOUT'])) . "</p>";
    }

    // Добавляем название выбранной задачи.
    $out_text .= "<p><b>Тип задачи:</b> " . $task_name . "</p>";

    // Добавляем входные числа A, B и C.
    $out_text .= "<p><b>Входные данные:</b> A = $A, B = $B, C = $C</p>";

    // Если пользователь не написал свой ответ.
    if ($user_result === null) {
        $out_text .= "<p><b>Задача самостоятельно решена не была</b></p>";

        // Если программа смогла посчитать ответ — выводим его.
        if ($result !== null) {
            $out_text .= "<p><b>Вычисленный программой результат:</b> $result</p>";
        }

        // Если программа не смогла посчитать ответ.
        else {
            $out_text .= "<p><b>Вычисленный программой результат:</b> невозможно вычислить</p>";
        }
    }

    // Если пользователь ввел свой ответ.
    else {
        // Выводим ответ пользователя.
        $out_text .= "<p><b>Ваш ответ:</b> $user_result</p>";

        // Если правильный ответ был вычислен.
        if ($result !== null) {
            $out_text .= "<p><b>Вычисленный программой результат:</b> $result</p>";

            // Сравниваем ответ пользователя и ответ программы.
            // abs() берет модуль разницы.
            // Погрешность 0.01 нужна для дробных ответов.
            if (abs($result - $user_result) < 0.01) {
                $out_text .= "<p class='success'><b>Тест пройден</b></p>";
            } else {
                $out_text .= "<p class='error'><b>Ошибка: тест не пройден</b></p>";
            }
        }

        // Если ответ посчитать нельзя.
        else {
            $out_text .= "<p><b>Вычисленный программой результат:</b> невозможно вычислить</p>";
            $out_text .= "<p class='error'><b>Ошибка: тест не пройден</b></p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №6</title>

    <style>
        /* Настройки всей страницы */
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Белый блок, внутри которого находится форма или результат */
        .container {
            width: 650px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-sizing: border-box;
        }

        /* Убираем лишний отступ сверху у заголовка */
        h2 {
            margin-top: 0;
        }

        /* Одна строка формы: слева подпись, справа поле */
        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        /* Подпись к элементу формы */
        .form-row label {
            width: 180px;
            font-weight: bold;
        }

        /* Общий стиль для текстовых полей, email, списков и textarea */
        input[type="text"],
        input[type="email"],
        select,
        textarea {
            flex: 1;
            padding: 8px;
            box-sizing: border-box;
        }

        /* Настройки многострочного поля "Немного о себе" */
        textarea {
            height: 80px;
            resize: vertical;
        }

        /* Чекбокс не должен растягиваться на всю ширину */
        input[type="checkbox"] {
            width: auto;
        }

        /* Кнопка "Проверить" */
        input[type="submit"] {
            padding: 10px 18px;
            border: 1px solid #000;
            background: #1976d2;
            color: white;
            cursor: pointer;
        }

        /* Изменение цвета кнопки при наведении */
        input[type="submit"]:hover {
            background: #125aa0;
        }

        /* Блок с e-mail сначала скрыт */
        #mail_block {
            display: none;
        }

        /* Зеленый текст успешного прохождения теста */
        .success {
            color: green;
            font-weight: bold;
        }

        /* Красный текст ошибки */
        .error {
            color: red;
            font-weight: bold;
        }

        /* Ссылка-кнопка "Повторить тест" */
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 14px;
            border: 1px solid #000;
            background: #1976d2;
            color: white;
            text-decoration: none;
        }

        /* Изменение цвета ссылки-кнопки при наведении */
        .btn:hover {
            background: #125aa0;
        }

        /* Версия для печати: более простой внешний вид */
        .print {
            background: white;
            border: none;
            box-shadow: none;
            font-size: 16px;
        }

        /* Версия для браузера: более оформленный внешний вид */
        .browser {
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>

    <script>
        // Функция вызывается при нажатии на флажок
        // "Отправить результат теста по e-mail".
        function toggleMail() {
            // Получаем блок с полем e-mail.
            let block = document.getElementById('mail_block');

            // Получаем сам флажок.
            let checkbox = document.getElementById('send_mail');

            // Если флажок включен — показываем поле e-mail.
            if (checkbox.checked) {
                block.style.display = 'flex';
            }

            // Если флажок выключен — скрываем поле e-mail.
            else {
                block.style.display = 'none';
            }
        }
    </script>
</head>
<body>

<?php
/* ---------- Вывод результата после отправки формы ---------- */

// Если форма была отправлена, выводим результаты теста.
if ($form_sent) {

    // Получаем выбранную версию вывода: browser или print.
    $view = $_POST['VIEW'];

    // Открываем контейнер.
    // Класс зависит от выбранной версии отображения.
    echo "<div class='container " . htmlspecialchars($view) . "'>";

    // Выводим готовый отчет.
    echo $out_text;

    // Если пользователь отметил флажок отправки на e-mail.
    if (isset($_POST['send_mail'])) {

        // Готовим текст письма.
        // strip_tags() убирает HTML-теги.
        // str_replace() заменяет <br> на перенос строки.
        $mail_text = strip_tags(str_replace("<br>", "\n", $out_text));

        // Заголовки письма.
        // From нужен, чтобы PHP не выдавал предупреждение.
        $headers = "From: test@example.com\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        // Пытаемся отправить письмо.
        // Символ @ скрывает предупреждения, если локальная почта не настроена.
        @mail(
            $_POST['MAIL'],
            "Результат теста",
            $mail_text,
            $headers
        );

        // По заданию выводим сообщение, что результаты отправлены.
        echo "<p><b>Результаты теста были автоматически отправлены на e-mail:</b> " . htmlspecialchars($_POST['MAIL']) . "</p>";
    }

    // Кнопка "Повторить тест" нужна только для версии браузера.
    if ($view == 'browser') {

        // Передаем ФИО и группу через GET-параметры,
        // чтобы они снова появились в форме.
        echo "<a class='btn' href='?fio=" . urlencode($_POST['FIO']) . "&group=" . urlencode($_POST['GROUP']) . "'>Повторить тест</a>";
    }

    // Закрываем контейнер результата.
    echo "</div>";
}

/* ---------- Вывод формы при первой загрузке ---------- */

// Если форма еще не отправлялась, показываем форму.
else {

    // Генерируем случайное число A от 0 до 100.
    $A = mt_rand(0, 100);

    // Генерируем случайное число B от 0 до 100.
    $B = mt_rand(0, 100);

    // Генерируем случайное число C от 0 до 100.
    $C = mt_rand(0, 100);

    // Если пользователь нажал "Повторить тест",
    // сюда попадет ранее введенное ФИО.
    $fio = $_GET['fio'] ?? '';

    // Сюда попадет ранее введенная группа.
    $group = $_GET['group'] ?? '';
?>

    <div class="container browser">
        <h2>Математический тест</h2>

        <!-- Форма отправляет данные методом POST на эту же страницу -->
        <form method="post">

            <!-- Однострочное поле для ввода ФИО -->
            <div class="form-row">
                <label>ФИО</label>
                <input type="text" name="FIO" value="<?= htmlspecialchars($fio) ?>" required>
            </div>

            <!-- Однострочное поле для ввода номера группы -->
            <div class="form-row">
                <label>Номер группы</label>
                <input type="text" name="GROUP" value="<?= htmlspecialchars($group) ?>" required>
            </div>

            <!-- Поле для значения A -->
            <div class="form-row">
                <label>Значение A</label>
                <input type="text" name="A" value="<?= $A ?>" required>
            </div>

            <!-- Поле для значения B -->
            <div class="form-row">
                <label>Значение B</label>
                <input type="text" name="B" value="<?= $B ?>" required>
            </div>

            <!-- Поле для значения C -->
            <div class="form-row">
                <label>Значение C</label>
                <input type="text" name="C" value="<?= $C ?>" required>
            </div>

            <!-- Поле, куда пользователь вводит свой предполагаемый ответ -->
            <div class="form-row">
                <label>Ваш ответ</label>
                <input type="text" name="user_result">
            </div>

            <!-- Многострочное поле для информации о студенте -->
            <div class="form-row">
                <label>Немного о себе</label>
                <textarea name="ABOUT"></textarea>
            </div>

            <!-- Список выбора математической задачи -->
            <div class="form-row">
                <label>Задача</label>
                <select name="TASK">
                    <option value="area">Площадь треугольника</option>
                    <option value="perimeter">Периметр треугольника</option>
                    <option value="volume">Объем параллелепипеда</option>
                    <option value="mean">Среднее арифметическое</option>
                    <option value="max">Максимальное значение</option>
                    <option value="sum_sq">Сумма квадратов</option>
                </select>
            </div>

            <!-- Флажок отправки результата на e-mail -->
            <div class="form-row">
                <label></label>
                <div>
                    <input type="checkbox" id="send_mail" name="send_mail" onclick="toggleMail()">
                    <label for="send_mail">Отправить результат теста по e-mail</label>
                </div>
            </div>

            <!-- Блок с полем e-mail. Сначала он скрыт через CSS -->
            <div class="form-row" id="mail_block">
                <label>Ваш e-mail</label>
                <input type="email" name="MAIL">
            </div>

            <!-- Список выбора внешнего вида результата -->
            <div class="form-row">
                <label>Версия</label>
                <select name="VIEW">
                    <option value="browser">Версия для просмотра в браузере</option>
                    <option value="print">Версия для печати</option>
                </select>
            </div>

            <!-- Кнопка отправки формы -->
            <div class="form-row">
                <label></label>
                <input type="submit" value="Проверить">
            </div>

        </form>
    </div>

<?php
}
?>

</body>
</html>