<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат сортировки — вариант 13</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<?php
/* ============================================================
   Лабораторная работа №7
   Вариант 13
   Второй файл: обработка массива и вывод процесса сортировки.

   Этот файл получает данные из формы методом POST:
   - element0, element1, element2... — элементы массива;
   - arrLength — количество элементов массива;
   - algorithm — выбранный алгоритм сортировки.
   ============================================================ */

/* ---------- Проверка числа ---------- */

/*
   Функция возвращает true, если переданное значение НЕ является числом.
   Разрешаем целые и дробные числа, а также отрицательные числа.
   Запятую пользователь может использовать как десятичный разделитель.
*/
function arg_is_not_Num($arg) {
    // Убираем лишние пробелы по краям строки.
    $arg = trim($arg);

    // Пустая строка не считается числом.
    if ($arg === '') {
        return true;
    }

    // Меняем запятую на точку, чтобы PHP понял дробное число.
    $arg = str_replace(',', '.', $arg);

    // is_numeric() проверяет, является ли строка числом.
    return !is_numeric($arg);
}

/*
   Функция переводит строку в число.
   Она нужна, чтобы сортировка работала именно с числами, а не со строками.
*/
function toNumber($value) {
    return (float) str_replace(',', '.', trim($value));
}

/* ---------- Вывод массива ---------- */

/*
   Функция выводит текущее состояние массива на экран.
   Используется на каждой итерации сортировки.

   $arr — массив;
   $iteration_num — номер итерации;
   $message — пояснение, что сейчас произошло.
*/
function printArrayState($arr, $iteration_num, $message = '') {
    echo '<div class="iteration">';
    echo '<span class="iteration-num">Итерация ' . $iteration_num . ':</span> ';

    // Перебираем массив и выводим каждый элемент вместе с его индексом.
    foreach ($arr as $key => $value) {
        echo '<div class="arr_element">' . $key . ': ' . htmlspecialchars((string)$value) . '</div>';
    }

    // Если есть пояснение, выводим его справа от массива.
    if ($message !== '') {
        echo '<span class="iteration-message">' . htmlspecialchars($message) . '</span>';
    }

    echo '</div>';
}

/* ---------- Сортировка выбором ---------- */

/*
   Суть сортировки выбором:
   ищем минимальный элемент в неотсортированной части массива
   и меняем его местами с первым элементом этой части.
*/
function sort_by_choice(&$arr, &$iter_count) {
    $n = count($arr);

    printArrayState($arr, $iter_count++, 'Начало сортировки выбором');

    // Внешний цикл выбирает позицию, куда нужно поставить минимум.
    for ($i = 0; $i < $n - 1; $i++) {
        $min_idx = $i;

        // Внутренний цикл ищет минимальный элемент справа от текущей позиции.
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$min_idx]) {
                $min_idx = $j;
            }

            printArrayState($arr, $iter_count++, 'Поиск минимального элемента');
        }

        // Если минимум найден не на текущем месте, меняем элементы местами.
        if ($min_idx != $i) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$min_idx];
            $arr[$min_idx] = $temp;

            printArrayState($arr, $iter_count++, 'Обмен элементов ' . $i . ' и ' . $min_idx);
        }
    }

    printArrayState($arr, $iter_count++, 'Сортировка выбором завершена');
}

/* ---------- Пузырьковая сортировка ---------- */

/*
   Суть пузырьковой сортировки:
   соседние элементы сравниваются друг с другом,
   и если они стоят неправильно — меняются местами.
*/
function sort_bubble(&$arr, &$iter_count) {
    $n = count($arr);

    printArrayState($arr, $iter_count++, 'Начало пузырьковой сортировки');

    // Внешний цикл отвечает за количество проходов по массиву.
    for ($i = 0; $i < $n - 1; $i++) {

        // Внутренний цикл сравнивает соседние элементы.
        for ($j = 0; $j < $n - $i - 1; $j++) {

            // Если левый элемент больше правого, меняем их местами.
            if ($arr[$j] > $arr[$j + 1]) {
                $temp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $temp;

                printArrayState($arr, $iter_count++, 'Обмен элементов ' . $j . ' и ' . ($j + 1));
            } else {
                printArrayState($arr, $iter_count++, 'Сравнение элементов ' . $j . ' и ' . ($j + 1) . ' без обмена');
            }
        }
    }

    printArrayState($arr, $iter_count++, 'Пузырьковая сортировка завершена');
}

/* ---------- Сортировка Шелла ---------- */

/*
   Сортировка Шелла похожа на сортировку вставками,
   но сначала сравнивает элементы на большом расстоянии,
   а потом постепенно уменьшает это расстояние до 1.
*/
function sort_shell(&$arr, &$iter_count) {
    $n = count($arr);

    printArrayState($arr, $iter_count++, 'Начало сортировки Шелла');

    // Начальный шаг — половина длины массива.
    $gap = floor($n / 2);

    // Пока шаг не стал меньше 1, продолжаем сортировку.
    while ($gap >= 1) {
        printArrayState($arr, $iter_count++, 'Текущий шаг = ' . $gap);

        // Перебираем элементы, начиная с позиции gap.
        for ($i = $gap; $i < $n; $i++) {
            $temp = $arr[$i];
            $j = $i;

            // Сдвигаем элементы, которые больше текущего.
            while ($j >= $gap && $arr[$j - $gap] > $temp) {
                $arr[$j] = $arr[$j - $gap];
                $j -= $gap;

                printArrayState($arr, $iter_count++, 'Сдвиг элемента');
            }

            // Вставляем элемент на найденное место.
            $arr[$j] = $temp;
            printArrayState($arr, $iter_count++, 'Вставка элемента на позицию ' . $j);
        }

        // Уменьшаем шаг в два раза.
        $gap = floor($gap / 2);
    }

    printArrayState($arr, $iter_count++, 'Сортировка Шелла завершена');
}

/* ---------- Сортировка садового гнома ---------- */

/*
   Суть алгоритма гнома:
   если текущий и предыдущий элементы стоят правильно — идем вперед,
   если неправильно — меняем их местами и делаем шаг назад.
*/
function sort_gnome(&$arr, &$iter_count) {
    $n = count($arr);
    $i = 1;

    printArrayState($arr, $iter_count++, 'Начало сортировки садового гнома');

    // Пока не дошли до конца массива.
    while ($i < $n) {

        // Если мы в начале массива или порядок правильный — идем вперед.
        if ($i == 0 || $arr[$i - 1] <= $arr[$i]) {
            $i++;
            printArrayState($arr, $iter_count++, 'Шаг вперед');
        }

        // Если порядок неправильный — меняем элементы и идем назад.
        else {
            $temp = $arr[$i];
            $arr[$i] = $arr[$i - 1];
            $arr[$i - 1] = $temp;
            $i--;

            printArrayState($arr, $iter_count++, 'Обмен элементов, шаг назад');
        }
    }

    printArrayState($arr, $iter_count++, 'Сортировка садового гнома завершена');
}

/* ---------- Быстрая сортировка ---------- */

/*
   Рекурсивная часть быстрой сортировки.
   Массив делится на части относительно опорного элемента.
*/
function quick_sort_recursive(&$arr, $left, $right, &$iter_count) {
    // Если левая граница дошла до правой, сортировать нечего.
    if ($left >= $right) {
        return;
    }

    // Опорный элемент берем из середины сортируемой части массива.
    $pivot = $arr[floor(($left + $right) / 2)];

    $i = $left;
    $j = $right;

    printArrayState($arr, $iter_count++, 'Разбиение: left=' . $left . ', right=' . $right . ', опора=' . $pivot);

    // Двигаем границы навстречу друг другу.
    while ($i <= $j) {

        // Слева ищем элемент, который должен быть правее опоры.
        while ($arr[$i] < $pivot) {
            $i++;
        }

        // Справа ищем элемент, который должен быть левее опоры.
        while ($arr[$j] > $pivot) {
            $j--;
        }

        // Если границы еще не пересеклись, меняем элементы местами.
        if ($i <= $j) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $temp;

            $i++;
            $j--;

            printArrayState($arr, $iter_count++, 'Обмен элементов при разбиении');
        }
    }

    // Рекурсивно сортируем левую часть.
    quick_sort_recursive($arr, $left, $j, $iter_count);

    // Рекурсивно сортируем правую часть.
    quick_sort_recursive($arr, $i, $right, $iter_count);
}

/*
   Отдельная функция-обертка для быстрой сортировки.
   Она нужна, чтобы не передавать вручную 0 и длину массива при вызове.
*/
function sort_quick(&$arr, &$iter_count) {
    printArrayState($arr, $iter_count++, 'Начало быстрой сортировки');

    quick_sort_recursive($arr, 0, count($arr) - 1, $iter_count);

    printArrayState($arr, $iter_count++, 'Быстрая сортировка завершена');
}

/* ============================================================
   Основная часть sort.php
   Здесь принимаем данные формы, проверяем их и запускаем сортировку.
   ============================================================ */

// Если данные не пришли из формы, сортировка невозможна.
if (!isset($_POST['element0']) || !isset($_POST['arrLength'])) {
    echo '<div class="warning">Ошибка: массив не задан, сортировка невозможна.</div>';
    echo '</div></body></html>';
    exit();
}

// Получаем количество элементов массива из скрытого поля.
$arrLength = (int) $_POST['arrLength'];

// Получаем выбранный алгоритм сортировки.
$algorithm = $_POST['algorithm'] ?? '';

// Список названий алгоритмов для красивого вывода на страницу.
$algorithm_names = [
    'choice' => 'Сортировка выбором',
    'bubble' => 'Пузырьковый алгоритм',
    'shell' => 'Алгоритм Шелла',
    'gnome' => 'Алгоритм садового гнома',
    'quick' => 'Быстрая сортировка',
    'php_sort' => 'Встроенная функция PHP sort()'
];

// Если алгоритм не найден в списке, выводим ошибку.
if (!isset($algorithm_names[$algorithm])) {
    echo '<div class="warning">Ошибка: выбран неизвестный алгоритм сортировки.</div>';
    echo '</div></body></html>';
    exit();
}

echo '<h1>Результат сортировки</h1>';
echo '<h2>Вариант 13</h2>';
echo '<h2>Алгоритм: ' . htmlspecialchars($algorithm_names[$algorithm]) . '</h2>';

// Массив исходных значений в текстовом виде.
$input_array = [];

// Список индексов элементов, которые не прошли проверку.
$invalid_elements = [];

// Считываем все element0, element1, element2... из $_POST.
for ($i = 0; $i < $arrLength; $i++) {
    $field_name = 'element' . $i;

    // Если поле существует, берем его значение.
    if (isset($_POST[$field_name])) {
        $value = trim($_POST[$field_name]);
        $input_array[] = $value;

        // Проверяем, является ли значение числом.
        if (arg_is_not_Num($value)) {
            $invalid_elements[] = $i;
        }
    }
}

// Выводим входные данные до сортировки.
echo '<h3>Входные данные:</h3>';
echo '<div>';
foreach ($input_array as $key => $value) {
    echo '<div class="arr_element">' . $key . ': ' . htmlspecialchars((string)$value) . '</div>';
}
echo '</div>';

// Если массив пустой, сортировать нечего.
if (empty($input_array)) {
    echo '<div class="warning">Ошибка: входные данные отсутствуют.</div>';
    echo '</div></body></html>';
    exit();
}

// Если есть нечисловые элементы, сортировка не выполняется.
if (!empty($invalid_elements)) {
    echo '<div class="warning">Ошибка валидации: следующие элементы не являются числами: ' . implode(', ', $invalid_elements) . '.</div>';
    echo '<div class="warning">Сортировка не выполнена.</div>';
    echo '</div></body></html>';
    exit();
}

// Если дошли сюда, все элементы корректные.
echo '<div class="success">Массив проверен: все элементы являются числами. Сортировка возможна.</div>';

// Преобразуем все элементы массива в числа.
$sort_array = array_map('toNumber', $input_array);

// Счетчик итераций. Нумерация сквозная для всех циклов алгоритма.
$iteration_count = 0;

// Засекаем время до начала сортировки.
$start_time = microtime(true);

// Запускаем выбранный алгоритм.
switch ($algorithm) {
    case 'choice':
        sort_by_choice($sort_array, $iteration_count);
        break;

    case 'bubble':
        sort_bubble($sort_array, $iteration_count);
        break;

    case 'shell':
        sort_shell($sort_array, $iteration_count);
        break;

    case 'gnome':
        sort_gnome($sort_array, $iteration_count);
        break;

    case 'quick':
        sort_quick($sort_array, $iteration_count);
        break;

    case 'php_sort':
        // У встроенной sort() внутренние итерации PHP не видны.
        // Поэтому показываем состояние до и после вызова функции.
        printArrayState($sort_array, $iteration_count++, 'Начало встроенной сортировки PHP sort()');
        sort($sort_array);
        printArrayState($sort_array, $iteration_count++, 'Встроенная сортировка PHP sort() завершена');
        break;
}

// Засекаем время после сортировки.
$end_time = microtime(true);

// Считаем длительность сортировки в секундах.
$execution_time = $end_time - $start_time;

// Выводим итоговое сообщение по требованию лабораторной.
echo '<div class="success">';
echo 'Сортировка завершена, проведено ' . $iteration_count . ' итераций. ';
echo 'Сортировка заняла ' . number_format($execution_time, 6) . ' секунд.';
echo '</div>';

// Выводим финальный отсортированный массив.
echo '<h3>Отсортированный массив:</h3>';
echo '<div>';
foreach ($sort_array as $key => $value) {
    echo '<div class="arr_element">' . $key . ': ' . htmlspecialchars((string)$value) . '</div>';
}
echo '</div>';

// Дополнительно сравниваем время выбранного алгоритма со встроенной PHP sort().
if ($algorithm !== 'php_sort') {
    $test_array = array_map('toNumber', $input_array);

    $php_start = microtime(true);
    sort($test_array);
    $php_time = microtime(true) - $php_start;

    echo '<hr>';
    echo '<h3>Сравнение с PHP sort()</h3>';
    echo '<div>Встроенная функция PHP sort() выполнила сортировку за ' . number_format($php_time, 6) . ' секунд.</div>';

    if ($php_time > 0) {
        if ($execution_time < $php_time) {
            echo '<div class="success">Выбранный алгоритм сработал быстрее встроенной функции.</div>';
        } else {
            echo '<div>Встроенная функция PHP sort() сработала быстрее примерно в ' . number_format($execution_time / $php_time, 2) . ' раз(а).</div>';
        }
    }
}
?>

</div>
</body>
</html>
