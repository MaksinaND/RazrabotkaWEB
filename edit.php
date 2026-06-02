<?php
/* ============================================================
   Модуль edit.php
   Здесь находится список записей для выбора и форма редактирования.
   ============================================================ */

require_once 'db.php';

$mysqli = connectDB();

// Если пользователь отправил форму редактирования, сначала обновляем данные.
if (isset($_POST['button']) && $_POST['button'] == 'Изменить запись') {
    // ID записи берем из скрытого поля формы.
    $id = (int)$_POST['id'];

    // Подготавливаем запрос UPDATE.
    $stmt = mysqli_prepare($mysqli, 'UPDATE contacts SET
        surname=?, name=?, patronymic=?, gender=?, birth_date=?, phone=?, address=?, email=?, comment=?
        WHERE id=?');

    // Подставляем данные из формы в запрос.
    mysqli_stmt_bind_param(
        $stmt,
        'sssssssssi',
        $_POST['surname'],
        $_POST['name'],
        $_POST['patronymic'],
        $_POST['gender'],
        $_POST['birth_date'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['email'],
        $_POST['comment'],
        $id
    );

    // Выполняем обновление.
    if (mysqli_stmt_execute($stmt)) {
        echo '<div class="ok">Данные изменены</div>';
        // После изменения делаем эту запись текущей.
        $_GET['id'] = $id;
    } else {
        echo '<div class="error">Ошибка: данные не изменены</div>';
    }
}

// Получаем краткий список всех записей для ссылок выбора.
$listResult = mysqli_query($mysqli, 'SELECT id, surname, name FROM contacts ORDER BY surname ASC, name ASC');

if (!$listResult || mysqli_num_rows($listResult) == 0) {
    echo '<div class="error">В базе нет записей для редактирования</div>';
    return;
}

// Определяем текущую запись: из GET-параметра id или первая по списку.
$currentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$firstId = 0;
$links = '<div class="record-links">';

// Сначала соберем все записи списка в массив, чтобы удобно выбрать первую.
$records = array();
while ($row = mysqli_fetch_assoc($listResult)) {
    $records[] = $row;
    if ($firstId == 0) {
        $firstId = (int)$row['id'];
    }
}

// Если запись не выбрана, текущей считаем первую запись.
if ($currentId == 0) {
    $currentId = $firstId;
}

// Формируем ссылки на записи.
foreach ($records as $row) {
    $id = (int)$row['id'];
    $title = h($row['surname'] . ' ' . $row['name']);

    // Текущая запись выделяется не ссылкой, а span.
    if ($id == $currentId) {
        $links .= '<span>' . $title . '</span>';
    } else {
        $links .= '<a href="?p=edit&id=' . $id . '">' . $title . '</a>';
    }
}
$links .= '</div>';

// Получаем полные данные текущей записи отдельным запросом.
$stmt = mysqli_prepare($mysqli, 'SELECT * FROM contacts WHERE id=?');
mysqli_stmt_bind_param($stmt, 'i', $currentId);
mysqli_stmt_execute($stmt);
$currentResult = mysqli_stmt_get_result($stmt);
$current = mysqli_fetch_assoc($currentResult);

if (!$current) {
    echo '<div class="error">Выбранная запись не найдена</div>';
    return;
}
?>

<h2>Редактирование записи</h2>

<!-- Список ссылок с фамилиями и именами из базы -->
<?= $links ?>

<!-- Форма похожа на форму добавления, но поля заполнены текущей записью -->
<form class="form-box" name="form_edit" method="post" action="?p=edit">
    <input type="hidden" name="id" value="<?= h($current['id']) ?>">

    <div class="form-row">
        <label>Фамилия</label>
        <input type="text" name="surname" value="<?= h($current['surname']) ?>" required>
    </div>

    <div class="form-row">
        <label>Имя</label>
        <input type="text" name="name" value="<?= h($current['name']) ?>" required>
    </div>

    <div class="form-row">
        <label>Отчество</label>
        <input type="text" name="patronymic" value="<?= h($current['patronymic']) ?>">
    </div>

    <div class="form-row">
        <label>Пол</label>
        <select name="gender">
            <option value="Ж" <?= $current['gender'] == 'Ж' ? 'selected' : '' ?>>Ж</option>
            <option value="М" <?= $current['gender'] == 'М' ? 'selected' : '' ?>>М</option>
        </select>
    </div>

    <div class="form-row">
        <label>Дата рождения</label>
        <input type="date" name="birth_date" value="<?= h($current['birth_date']) ?>">
    </div>

    <div class="form-row">
        <label>Телефон</label>
        <input type="text" name="phone" value="<?= h($current['phone']) ?>">
    </div>

    <div class="form-row">
        <label>Адрес</label>
        <input type="text" name="address" value="<?= h($current['address']) ?>">
    </div>

    <div class="form-row">
        <label>E-mail</label>
        <input type="email" name="email" value="<?= h($current['email']) ?>">
    </div>

    <div class="form-row">
        <label>Комментарий</label>
        <textarea name="comment"><?= h($current['comment']) ?></textarea>
    </div>

    <div class="form-row">
        <label></label>
        <input type="submit" name="button" value="Изменить запись">
    </div>
</form>
