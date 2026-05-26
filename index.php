<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ЛР7 — сортировка массива, вариант 13</title>

    <!-- Подключаем отдельный CSS-файл для оформления формы и результата -->
    <link rel="stylesheet" href="styles.css">

    <!-- Подключаем JavaScript-файл: он добавляет новые поля массива без перезагрузки -->
    <script src="script.js"></script>
</head>
<body>

    <div class="page">
        <h1>Лабораторная работа №7</h1>
        <h2>Ввод данных и сортировка массивов — вариант 13</h2>

        <!--
            Форма отправляет данные во второй PHP-файл sort.php.
            method="POST" нужен, чтобы передать массив и выбранный алгоритм.
            target="_blank" открывает результат сортировки в новой вкладке,
            как требуется в лабораторной работе.
        -->
        <form id="sortForm" action="sort.php" method="POST" target="_blank">

            <p class="hint">
                Введите элементы массива. Чтобы добавить новое поле, нажмите кнопку
                «Добавить еще один элемент».
            </p>

            <!--
                В этой таблице находятся поля ввода элементов массива.
                Изначально есть только одно поле element0.
                Остальные поля добавляются JavaScript-функцией addElement().
            -->
            <table id="elements" class="input-table">
                <tr>
                    <!-- Слева выводится номер элемента массива -->
                    <td class="element_index">0</td>

                    <!-- Само поле ввода первого элемента массива -->
                    <td class="element_row">
                        <input type="text" name="element0" placeholder="Введите число">
                    </td>
                </tr>
            </table>

            <!--
                Скрытое поле хранит количество элементов массива.
                JavaScript обновляет его каждый раз после добавления нового поля.
                Во втором файле sort.php по этому числу программа понимает,
                сколько элементов нужно считать из $_POST.
            -->
            <input type="hidden" id="arrLength" name="arrLength" value="1">

            <div class="form-row">
                <label for="algorithm">Выберите алгоритм сортировки:</label>

                <!--
                    Селектор алгоритма сортировки.
                    В лабораторной требуется 6 вариантов: выбором, пузырьком,
                    Шелла, садового гнома, быстрая и встроенная PHP sort().
                -->
                <select id="algorithm" name="algorithm">
                    <option value="choice">Сортировка выбором</option>
                    <option value="bubble">Пузырьковый алгоритм</option>
                    <option value="shell">Алгоритм Шелла</option>
                    <option value="gnome">Алгоритм садового гнома</option>
                    <option value="quick">Быстрая сортировка</option>
                    <option value="php_sort">Встроенная функция PHP sort()</option>
                </select>
            </div>

            <div class="buttons">
                <!--
                    Обычная кнопка, НЕ отправляет форму.
                    Она только добавляет еще одно поле для элемента массива.
                -->
                <input type="button" value="Добавить еще один элемент" onclick="addElement('elements');">

                <!--
                    Эта кнопка запускает проверку и отправку формы.
                    Форма отправляется в sort.php.
                -->
                <input type="button" value="Сортировать массив" onclick="submitForm();">
            </div>

        </form>
    </div>

</body>
</html>
