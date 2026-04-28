<?php
    $studentName = 'Максина Надежда Дмитриевна';
    $studentGroup = '241-353';
    $labNumber = 'Лабораторная работа №2';
    $variantNumber = 13;
    $realVariant = 3;

    $title = $studentName . ', группа ' . $studentGroup . ', ЛР№2, вариант ' . $variantNumber;

    /*
        Исходные данные для табулирования функции.
    */

    $startValue = -5; // начальное значение аргумента x
    $countValues = 30; // количество вычисляемых значений функции
    $step = 1; // шаг изменения аргумента

    $minStopValue = -100000; // минимальное значение функции для остановки вычислений
    $maxStopValue = 100000; // максимальное значение функции для остановки вычислений

    /*
        Тип верстки:
        A — простой текст через <br>
        B — маркированный список <ul>
        C — нумерованный список <ol>
        D — таблица
        E — блочная верстка

        Для проверки меняем только эту букву.
    */

    $type = 'B';
?>






<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">


    <title>
        <?php echo $title; ?>
    </title>


    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <img src="logo.png" alt="Логотип Московского Политеха" class="logo">

    <div class="header-text">


        <h1>
            <?php echo $studentName; ?>
        </h1>

        <h2>Группа: <?php echo $studentGroup; ?></h2>

        <h3>
            <?php echo $labNumber; ?>,
            вариант <?php echo $variantNumber; ?>
            (формула варианта <?php echo $realVariant; ?>)
        </h3>


    </div>
</header>

<main>
    <h1>Табулирование функции</h1>

    <section class="info-block">
        <h2>Исходные параметры</h2>

        <p>
            Начальное значение аргумента:
            <strong><?php echo $startValue; ?></strong><br>

            Количество вычисляемых значений:
            <strong><?php echo $countValues; ?></strong><br>

            Шаг изменения аргумента:
            <strong><?php echo $step; ?></strong><br>

            Минимальное значение функции для остановки:
            <strong><?php echo $minStopValue; ?></strong><br>

            Максимальное значение функции для остановки:
            <strong><?php echo $maxStopValue; ?></strong><br>

            Тип верстки:
            <strong><?php echo $type; ?></strong>
        </p>

        <div class="formula">
            f(x) = 3 * x³ + 2, если x ≤ 10<br>
            f(x) = 5 * x + 7, если 10 &lt; x &lt; 20<br>
            f(x) = x / (22 - x) - x, если x ≥ 20
        </div>
    </section>

    <section class="result-block">
        <h2>Результаты вычислений</h2>

        <?php
            $x = $startValue; // Присваиваем переменной $x начальное значение аргумента функции

            $values = []; // Массив для хранения всех числовых значений функции
            $calculatedCount = 0;  // Счётчик количества выполненных итераций

            /*
                Открываем нужную верстку перед циклом.
            */

            switch ($type) {
                case 'B':
                    echo '<ul>';
                    break;

                case 'C':
                    echo '<ol>';
                    break;

                case 'D':
                    echo '<table>';
                    echo '<tr>';
                    echo '<th>№</th>'; // номер строки
                    echo '<th>x</th>'; // значение аргумента
                    echo '<th>f(x)</th>'; // значение функции
                    echo '</tr>';
                    break;

                case 'E':
                    echo '<div class="flex">'; // Контейнер для блочной верстки (flex)
                    break;
            }

            /*
                Основной цикл.
                Используется for, потому что количество итераций известно заранее.

                выполняется заданное количество раз ($countValues)
                на каждой итерации:
                    - вычисляется значение функции
                    - увеличивается x на шаг
            */

            for ($i = 0; $i < $countValues; $i++, $x += $step) {

                /*
                    Вычисление функции по варианту 3:

                    f(x) = 3*x^3 + 2, если x <= 10
                    f(x) = 5*x + 7, если 10 < x < 20
                    f(x) = x / (22 - x) - x, если x >= 20

                    В третьей ветке есть деление.
                    При x = 22 знаменатель равен нулю:
                    22 - 22 = 0.
                    Поэтому вместо вычисления выводится "error".
                */

                if ($x <= 10) //первая функция
                {
                    $f = 3 * pow($x, 3) + 2;
                    $f = round($f, 3); // Округление до 3 знаков

                }
                

                elseif ($x < 20) //вторая функция
                {
                    $f = 5 * $x + 7;
                    $f = round($f, 3); // Округление до 3 знаков

                }


                else
                {
                    if ($x == 22)
                    {
                        $f = 'error'; // Деление на ноль → выводим error
                    }
                    else
                    {
                        $f = $x / (22 - $x) - $x;
                        $f = round($f, 3); // Округление до 3 знаков
                    }
                }

                /*
                    Статистика считается только по числовым значениям.
                    Значение "error" в сумму, минимум, максимум и среднее не включается.
                */

                if ($f !== 'error')
                {
                    $values[] = $f; //Добавляем значение функции в массив ТОЛЬКО если это число, строка error в статистике не учитывается
                }

                $calculatedCount++; // Увеличиваем счётчик выполненных итераций




                /*
                    Вывод текущей строки результата в зависимости от типа верстки.
                */

                switch ($type) {
                    case 'A':
                        echo 'f(' . $x . ') = ' . $f; // Простая текстовая верстка: вывод строки f(x)=y

                        if ($i < $countValues - 1) //если это не последнее, тогда делаем перенос на другую строку, чтобы в кучу не выводилось
                        {
                            echo '<br>';
                        }

                        break;
                    // общий кусок для B и C
                    case 'B':
                    case 'C':
                        echo '<li>f(' . $x . ') = ' . $f . '</li>'; // Для типов B и C используется одинаковый вывод элементов <li>, поэтому они объединены в один блок switch.
                        break;

                    case 'D':
                        // // Табличная верстка: каждая строка — это строка таблицы <tr>
                        echo '<tr>';
                        echo '<td>' . ($i + 1) . '</td>'; // Номер строки (счётчик + 1, т.к. $i начинается с 0)
                        echo '<td>' . $x . '</td>'; // Значение аргумента x
                        echo '<td>' . $f . '</td>';
                        echo '</tr>'; // Значение функции f(x)
                        break;

                    case 'E':
                        echo '<div class="box">'; // каждая строка выводится в отдельном div
                        echo 'f(' . $x . ') = ' . $f;
                        echo '</div>';
                        break;

                    default:
                        echo 'Ошибка: неизвестный тип верстки.';
                        break 2;
                }

                /*
                    Остановка вычислений при достижении указанных границ.
                    Для "error" проверку границ не выполняем.
                */

                if ($f !== 'error' && ($f >= $maxStopValue || $f <= $minStopValue)) {
                    break;
                }
            }

            /*
                Закрываем теги после цикла.
            */

            switch ($type) {
                case 'B':
                    echo '</ul>';
                    break;

                case 'C':
                    echo '</ol>';
                    break;

                case 'D':
                    echo '</table>';
                    break;

                case 'E':
                    echo '</div>';
                    break;
            }
        ?>
    </section>

    <section class="stats-block">
        <h2>Статистика</h2>

/*
    Блок вычисления и вывода статистики.

    Сначала проверяем:
    есть ли в массиве $values хотя бы одно значение.

    Это важно, чтобы не делить на ноль и не вызывать ошибки.
*/

        <?php
            if (count($values) > 0) {

    /*
        array_sum($values) — считает сумму всех элементов массива.

        round(..., 3) — округляет результат до 3 знаков после запятой.
    */

                $sum = round(array_sum($values), 3);
                $average = round($sum / count($values), 3);
                $minValue = min($values);
                $maxValue = max($values);

                echo '<p>Количество выполненных итераций: ' . $calculatedCount . '</p>';
                echo '<p>Количество числовых значений функции: ' . count($values) . '</p>';
                echo '<p>Сумма значений функции: ' . $sum . '</p>';
                echo '<p>Среднее арифметическое: ' . $average . '</p>';
                echo '<p>Минимальное значение функции: ' . $minValue . '</p>';
                echo '<p>Максимальное значение функции: ' . $maxValue . '</p>';
            } else {
                echo '<p>Нет числовых данных для вычисления статистики.</p>';
            }
        ?>
    </section>
</main>

<footer>
    Тип верстки: <?php echo $type; ?>
</footer>

</body>
</html>