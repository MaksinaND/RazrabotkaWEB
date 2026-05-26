/* ============================================================
   JavaScript для ЛР7
   Этот файл отвечает за добавление новых полей массива
   без перезагрузки страницы.
   ============================================================ */

/*
   Функция setHTML() устанавливает HTML-содержимое элемента.
   В современных браузерах достаточно innerHTML
*/
function setHTML(element, txt) {
    // Если свойство innerHTML доступно, просто записываем в него HTML-код.
    if (element.innerHTML !== undefined) {
        element.innerHTML = txt;
    }

    // Запасной вариант для старых браузеров.
    else {
        var range = document.createRange();
        range.selectNodeContents(element);
        range.deleteContents();
        var fragment = range.createContextualFragment(txt);
        element.appendChild(fragment);
    }
}

/*
   Функция addElement() добавляет в таблицу одну новую строку
   с номером элемента и полем ввода.

   table_name — id таблицы, куда нужно добавить строку.
*/
function addElement(table_name) {
    // Получаем таблицу с полями массива по ее id.
    var table = document.getElementById(table_name);

    // Количество строк таблицы равно номеру нового элемента.
    // Например, если уже есть element0, то новая строка получит index = 1.
    var index = table.rows.length;

    // Добавляем новую строку в конец таблицы.
    var row = table.insertRow(index);

    // Создаем две ячейки: первая для номера, вторая для поля ввода.
    var numberCell = row.insertCell(0);
    var inputCell = row.insertCell(1);

    // Назначаем CSS-классы для оформления.
    numberCell.className = 'element_index';
    inputCell.className = 'element_row';

    // В первой ячейке показываем номер элемента массива.
    setHTML(numberCell, index);

    // Во второй ячейке создаем поле с уникальным именем elementX.
    // Это важно: PHP потом будет читать element0, element1, element2 и т.д.
    setHTML(inputCell, '<input type="text" name="element' + index + '" placeholder="Введите число">');

    // Обновляем скрытое поле arrLength, чтобы PHP знал длину массива.
    document.getElementById('arrLength').value = table.rows.length;
}

/*
   Функция submitForm() отправляет форму на обработку.
   Отправка идет в sort.php, потому что это указано в action формы.
*/
function submitForm() {
    document.getElementById('sortForm').submit();
}
