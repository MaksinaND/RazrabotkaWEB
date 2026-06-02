<?php
/* ============================================================
   Модуль delete.php
   Показывает список контактов и удаляет выбранную запись.
   ============================================================ */

require_once 'db.php';

$mysqli = connectDB();

// Если в GET передан параметр id, значит нужно удалить выбранную запись.
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Сначала узнаем фамилию удаляемой записи, чтобы вывести ее в сообщении.
    $stmt = mysqli_prepare($mysqli, 'SELECT surname FROM contacts WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $surname = $row['surname'];

        // Удаляем запись по id.
        $del = mysqli_prepare($mysqli, 'DELETE FROM contacts WHERE id=?');
        mysqli_stmt_bind_param($del, 'i', $id);

        if (mysqli_stmt_execute($del)) {
            echo '<div class="ok">Запись с фамилией ' . h($surname) . ' удалена</div>';
        } else {
            echo '<div class="error">Ошибка: запись не удалена</div>';
        }
    } else {
        echo '<div class="error">Запись для удаления не найдена</div>';
    }
}

// Получаем список оставшихся записей для удаления.
$result = mysqli_query($mysqli, 'SELECT id, surname, name, patronymic FROM contacts ORDER BY surname ASC, name ASC');

if (!$result || mysqli_num_rows($result) == 0) {
    echo '<div class="error">В базе нет записей для удаления</div>';
    return;
}
?>

<h2>Удаление записи</h2>
<p>Нажмите на контакт, который нужно удалить.</p>

<div class="record-links">
<?php
// Каждая запись выводится ссылкой. При клике передается id удаляемой записи.
while ($row = mysqli_fetch_assoc($result)) {
    $initials = '';
    if ($row['name'] !== '') {
        $initials .= mb_substr($row['name'], 0, 1, 'UTF-8') . '.';
    }
    if ($row['patronymic'] !== '') {
        $initials .= mb_substr($row['patronymic'], 0, 1, 'UTF-8') . '.';
    }

    echo '<a href="?p=delete&id=' . (int)$row['id'] . '">' . h($row['surname'] . ' ' . $initials) . '</a>';
}
?>
</div>
