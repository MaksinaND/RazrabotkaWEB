<?php
date_default_timezone_set('Europe/Moscow');
$pageLabel = 'Участники';
$pageTitle = 'Максина Надежда Дмитриевна, группа 241-353 - Лабораторная работа № А-1';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <div class="brand">Gorillaz</div>
        <nav class="menu">
            <a href="<?php $name='Главная'; $link='index.php'; $current_page=false; echo $link; ?>"<?php if($current_page) echo ' class="selected_menu"'; echo '>'.$name; ?></a>
            <a href="<?php $name='Участники'; $link='members.php'; $current_page=true; echo $link; ?>"<?php if($current_page) echo ' class="selected_menu"'; echo '>'.$name; ?></a>
            <a href="<?php $name='Альбомы'; $link='albums.php'; $current_page=false; echo $link; ?>"<?php if($current_page) echo ' class="selected_menu"'; echo '>'.$name; ?></a>
        </nav>
    </div>
</header>

<main class="page">

    <!-- HERO -->
    <section class="hero">
        <h1>Основные участники виртуальной группы</h1>

        <p>
            В отличие от обычных музыкальных коллективов, Gorillaz строятся вокруг вымышленных персонажей, каждый из которых имеет собственный характер, историю и визуальный стиль. Именно это делает проект узнаваемым: слушатели запоминают не только музыку, но и образы участников, которые появляются в клипах, на обложках и в других визуальных материалах.
        </p>

        <p>
            Основной состав группы включает четырёх персонажей: 2D, Murdoc Niccals, Noodle и Russel Hobbs. Формально они выполняют привычные роли - вокал, бас, гитара и ударные, - однако их значение выходит за рамки музыкальных функций. Каждый из них задаёт настроение и усиливает общую атмосферу проекта.
        </p>

        <p>
            Благодаря такому подходу Gorillaz воспринимаются не просто как группа, а как единая художественная концепция, в которой музыка и визуальные образы неразделимы.
        </p>
    </section>

    <!-- КАРТИНКИ -->
    <section class="gallery">
        <h2>Визуальные образы участников</h2>

        <div class="gallery-grid">
            <figure class="gallery-item">
                <?php echo '<img src="images/member-change-'.(date('s') % 2 + 1).'.jpg" alt="Меняющееся изображение участника">'; ?>
                <figcaption>
                    Образы участников варьируются, но сохраняют ключевые черты, благодаря которым персонажи остаются узнаваемыми.
                </figcaption>
            </figure>

            <figure class="gallery-item">
                <img src="images/member-static.jpg" alt="Участники Gorillaz">
                <figcaption>
                    Совместные иллюстрации подчёркивают взаимодействие персонажей и создают ощущение единой команды.
                </figcaption>
            </figure>
        </div>
    </section>

    <!-- ТАБЛИЦА -->
    <section class="table-section">
        <h2>Сравнение персонажей</h2>

        <table class="info-table">
            <?php echo '<tr><th>Персонаж</th><th>Роль</th><th>Особенность</th></tr>'; ?>

            <tr>
                <td><?php echo '2D'; ?></td>
                <td><?php echo 'Вокал, клавишные'; ?></td>
                <td><?php echo 'Спокойный и отстранённый образ'; ?></td>
            </tr>

            <tr>
                <td>Murdoc</td>
                <td>Бас-гитара</td>
                <td>Провокационный и агрессивный характер</td>
            </tr>

            <tr>
                <td>Noodle</td>
                <td>Гитара</td>
                <td>Динамичность</td>
            </tr>

            <tr>
                <td>Russel</td>
                <td>Ударные</td>
                <td>Сдержанный образ</td>
            </tr>
        </table>

        <p class="note">
            Персонажи различаются по характеру и роли, но вместе формируют целостный образ группы.
        </p>
    </section>

    <!-- ФИНАЛ -->
    <section class="section">
        <h2>Роль персонажей в восприятии проекта</h2>

        <p>
            Персонажи Gorillaz выполняют не только визуальную функцию, но и задают способ восприятия всей группы. Благодаря им проект воспринимается не как набор отдельных треков, а как единая история с собственным стилем и атмосферой.
        </p>

        <p>
            Такой подход делает Gorillaz более запоминающимися: даже без знания конкретных песен многие узнают визуальные образы. Это показывает, насколько сильным может быть сочетание музыки и художественного оформления.
        </p>
    </section>

</main>

<footer class="site-footer">
    <div class="footer-inner">
        Сформировано <?php echo date('d.m.Y'); ?> в <?php echo date('H-i:s'); ?>
    </div>
</footer>

</body>
</html>