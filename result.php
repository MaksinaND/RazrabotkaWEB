<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат анализа текста</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">

<?php
/* ============================================================
   Лабораторная работа № А-8
   Тема: Основы работы со строковыми данными в PHP. Кодировка.
   Анализ текста.

   Этот файл получает текст из формы index.html и выполняет анализ:
   1. выводит исходный текст;
   2. считает общее количество символов;
   3. считает буквы, заглавные и строчные буквы;
   4. считает знаки препинания;
   5. считает цифры;
   6. считает слова;
   7. считает вхождения каждого символа без учета регистра;
   8. выводит список слов и количество их повторений по алфавиту.

   Оба файла работают в кодировке UTF-8, поэтому русский и английский
   текст обрабатываются корректно.
   ============================================================ */


/* ---------- Функция перевода строки в нижний регистр ---------- */

// Для русского текста обычный strtolower() работает плохо,
// потому что кириллица в UTF-8 занимает больше одного байта.
// Поэтому сначала используем mb_strtolower(), если она доступна.
function textToLower($text) {
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($text, 'UTF-8');
    }

    // Запасной вариант для английских букв, если mbstring не подключен.
    return strtolower($text);
}


/* ---------- Функция разбиения текста на символы ---------- */

// Обычный доступ $text[$i] работает с байтами, а не с русскими буквами.
// Поэтому используем регулярное выражение /./us, которое разбивает
// UTF-8 строку именно на символы.
function getChars($text) {
    preg_match_all('/./us', $text, $matches);
    return $matches[0];
}


/* ---------- Функция удобного отображения специальных символов ---------- */

// Пробел, перенос строки и табуляцию не видно в таблице.
// Поэтому заменяем их понятными подписями.
function formatSpecialChar($ch) {
    switch ($ch) {
        case ' ':
            return 'ПРОБЕЛ';
        case "\n":
            return 'НОВАЯ СТРОКА';
        case "\r":
            return 'ВОЗВРАТ КАРЕТКИ';
        case "\t":
            return 'ТАБУЛЯЦИЯ';
        default:
            return $ch;
    }
}


/* ---------- Функция подсчета вхождений каждого символа ---------- */

function countChars($text) {
    // По заданию символы считаются без различия регистра.
    // Поэтому сначала переводим текст в нижний регистр.
    $text = textToLower($text);

    // Получаем массив отдельных символов.
    $chars = getChars($text);

    // В этом массиве ключ — символ, значение — количество повторений.
    $result = array();

    // Перебираем каждый символ и увеличиваем его счетчик.
    foreach ($chars as $ch) {
        if (isset($result[$ch])) {
            $result[$ch]++;
        } else {
            $result[$ch] = 1;
        }
    }

    return $result;
}


/* ---------- Функция подсчета слов ---------- */

function countWords($text) {
    // Слова считаем как последовательности букв и цифр.
    // \p{L} — любая буква любого языка, включая русские и английские.
    // \p{N} — цифры.
    preg_match_all('/[\p{L}\p{N}]+/u', $text, $matches);

    $words = array();

    foreach ($matches[0] as $word) {
        // Приводим слово к нижнему регистру, чтобы "Текст" и "текст"
        // считались одним и тем же словом.
        $word = textToLower($word);

        // Записываем количество повторений слова.
        if (isset($words[$word])) {
            $words[$word]++;
        } else {
            $words[$word] = 1;
        }
    }

    // Сортируем слова по алфавиту по ключам массива.
    ksort($words, SORT_STRING);

    return $words;
}


/* ---------- Функция вывода строки таблицы статистики ---------- */

function printStatRow($name, $value) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($name) . '</td>';
    echo '<td class="number-cell">' . htmlspecialchars((string)$value) . '</td>';
    echo '</tr>';
}


/* ---------- Главная функция анализа текста ---------- */

function analyzeText($text) {
    // Выводим исходный текст.
    // htmlspecialchars защищает HTML-страницу от случайных тегов внутри текста.
    // nl2br сохраняет переносы строк при выводе в браузере.
    echo '<h2>Исходный текст</h2>';
    echo '<div class="source-text">' . nl2br(htmlspecialchars($text)) . '</div>';

    // Общее количество символов, включая пробелы и переносы строк.
    // mb_strlen корректно считает русские буквы как один символ.
    if (function_exists('mb_strlen')) {
        $charCount = mb_strlen($text, 'UTF-8');
    } else {
        $charCount = count(getChars($text));
    }

    // Количество всех букв.
    preg_match_all('/\p{L}/u', $text, $lettersMatches);
    $lettersCount = count($lettersMatches[0]);

    // Количество заглавных букв.
    preg_match_all('/\p{Lu}/u', $text, $upperMatches);
    $upperCount = count($upperMatches[0]);

    // Количество строчных букв.
    preg_match_all('/\p{Ll}/u', $text, $lowerMatches);
    $lowerCount = count($lowerMatches[0]);

    // Количество знаков препинания.
    // \p{P} — все символы пунктуации: точка, запятая, тире и т.д.
    preg_match_all('/\p{P}/u', $text, $punctMatches);
    $punctCount = count($punctMatches[0]);

    // Количество цифр.
    preg_match_all('/\p{N}/u', $text, $digitMatches);
    $digitCount = count($digitMatches[0]);

    // Список слов и их количество.
    $words = countWords($text);
    $wordCount = array_sum($words);

    // Таблица с общей информацией о тексте.
    echo '<h2>Информация о тексте</h2>';
    echo '<table>';
    echo '<tr><th>Показатель</th><th>Значение</th></tr>';
    printStatRow('Количество символов в тексте, включая пробелы', $charCount);
    printStatRow('Количество букв', $lettersCount);
    printStatRow('Количество заглавных букв', $upperCount);
    printStatRow('Количество строчных букв', $lowerCount);
    printStatRow('Количество знаков препинания', $punctCount);
    printStatRow('Количество цифр', $digitCount);
    printStatRow('Количество слов', $wordCount);
    echo '</table>';

    // Таблица вхождений каждого символа.
    echo '<h2>Вхождения каждого символа без учета регистра</h2>';
    $chars = countChars($text);

    echo '<table class="symbol-table">';
    echo '<tr><th>Символ</th><th>Количество</th></tr>';

    foreach ($chars as $ch => $count) {
        $display = formatSpecialChar($ch);

        // Для специальных символов добавляем отдельный CSS-класс.
        $class = ($display !== $ch) ? ' class="special-symbol"' : '';

        echo '<tr>';
        echo '<td' . $class . '>' . htmlspecialchars($display) . '</td>';
        echo '<td class="number-cell">' . $count . '</td>';
        echo '</tr>';
    }

    echo '</table>';

    // Таблица слов и количества их повторений.
    echo '<h2>Список слов по алфавиту</h2>';
    echo '<table>';
    echo '<tr><th>Слово</th><th>Количество вхождений</th></tr>';

    foreach ($words as $word => $count) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($word) . '</td>';
        echo '<td class="number-cell">' . $count . '</td>';
        echo '</tr>';
    }

    echo '</table>';
}


/* ---------- Основная часть result.php ---------- */

// Проверяем, был ли передан текст из формы.
// Если поле data существует и после trim() не пустое, можно анализировать.
if (isset($_POST['data']) && trim($_POST['data']) !== '') {
    analyzeText($_POST['data']);
} else {
    // Если текста нет, по требованию выводим сообщение.
    echo '<div class="error">Нет текста для анализа</div>';
}
?>

<!--
    Кнопка "Другой анализ" по заданию должна быть ссылкой <a>.
    Она возвращает пользователя на первый документ index.html.
-->
<a class="button-link" href="index.html">Другой анализ</a>

</div>

</body>
</html>
