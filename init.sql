-- ============================================================
-- SQL-файл для ЛР9 / В-1. Записная книжка.
-- Выполнить в phpMyAdmin или MySQL перед запуском сайта.
-- ============================================================

DROP DATABASE IF EXISTS notebook_lab9;

CREATE DATABASE notebook_lab9
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE notebook_lab9;

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surname VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    patronymic VARCHAR(100),
    gender VARCHAR(10),
    birth_date DATE,
    phone VARCHAR(50),
    address VARCHAR(255),
    email VARCHAR(150),
    comment TEXT
) DEFAULT CHARSET=utf8mb4;

-- Несколько тестовых записей, чтобы при первом запуске таблица не была пустой.
-- Дата записывается строго в формате YYYY-MM-DD, потому что поле birth_date имеет тип DATE.
INSERT INTO contacts
(surname, name, patronymic, gender, birth_date, phone, address, email, comment)
VALUES
('Максина', 'Надежда', 'Дмитриевна', 'Ж', '2005-06-09', '+7 900 111-22-33', 'Москва', 'nadezhda@example.com', 'Тестовая запись'),
('Петров', 'Иван', 'Сергеевич', 'М', '2004-10-02', '+7 900 222-33-44', 'Москва', 'petrov@example.com', 'Ещё одна запись'),
('Сидорова', 'Анна', 'Игоревна', 'Ж', '2003-03-21', '+7 900 333-44-55', 'Санкт-Петербург', 'anna@example.com', 'Контакт для проверки сортировки');
