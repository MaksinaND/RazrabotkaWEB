<?php
/* ============================================================
   Модуль add.php
   Здесь находится форма добавления контакта и обработка INSERT.
   ============================================================ */

require_once 'db.php';

// Если была нажата кнопка добавления записи, обрабатываем форму.
if (isset($_POST['button']) && $_POST['button'] == 'Добавить запись') {
    // Подключаемся к базе данных.
    $mysqli = connectDB();

    // Подготавливаем SQL-запрос с псевдопеременными.
    // Это безопаснее, чем просто склеивать строку запроса.
    $stmt = mysqli_prepare($mysqli, 'INSERT INTO contacts
        (surname, name, patronymic, gender, birth_date, phone, address, email, comment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

    // Передаем значения из формы в подготовленный запрос.
    mysqli_stmt_bind_param(
        $stmt,
        'sssssssss',
        $_POST['surname'],
        $_POST['name'],
        $_POST['patronymic'],
        $_POST['gender'],
        $_POST['birth_date'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['email'],
        $_POST['comment']
    );

    // Выполняем запрос и выводим сообщение по результату.
    if (mysqli_stmt_execute($stmt)) {
        echo '<div class="ok">Запись добавлена</div>';
    } else {
        echo '<div class="error">Ошибка: запись не добавлена</div>';
    }
}
?>

<h2>Добавление записи</h2>

<!-- Форма статически задается в этом модуле и отправляется на index.php?p=add -->
<form class="form-box" name="form_add" method="post" action="?p=add">
    <div class="form-row">
        <label>Фамилия</label>
        <input type="text" name="surname" required>
    </div>

    <div class="form-row">
        <label>Имя</label>
        <input type="text" name="name" required>
    </div>

    <div class="form-row">
        <label>Отчество</label>
        <input type="text" name="patronymic">
    </div>

    <div class="form-row">
        <label>Пол</label>
        <select name="gender">
            <option value="Ж">Ж</option>
            <option value="М">М</option>
        </select>
    </div>

    <div class="form-row">
        <label>Дата рождения</label>
        <input type="date" name="birth_date">
    </div>

    <div class="form-row">
        <label>Телефон</label>
        <input type="text" name="phone">
    </div>

    <div class="form-row">
        <label>Адрес</label>
        <input type="text" name="address">
    </div>

    <div class="form-row">
        <label>E-mail</label>
        <input type="email" name="email">
    </div>

    <div class="form-row">
        <label>Комментарий</label>
        <textarea name="comment"></textarea>
    </div>

    <div class="form-row">
        <label></label>
        <input type="submit" name="button" value="Добавить запись">
    </div>
</form>
