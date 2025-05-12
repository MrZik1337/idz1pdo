-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: mysql-8.0
-- Время создания: Апр 29 2025 г., 14:37
-- Версия сервера: 8.0.35
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `hospital_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `nurse`
--

CREATE TABLE `nurse` (
  `id_nurse` int NOT NULL,
  `name` text NOT NULL,
  `date` date NOT NULL,
  `department` int NOT NULL,
  `shift` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `nurse`
--

INSERT INTO `nurse` (`id_nurse`, `name`, `date`, `department`, `shift`) VALUES
(1, 'ivanova', '2021-12-20', 1, 'First'),
(2, 'petrova', '2022-12-20', 2, 'Third'),
(3, 'sidorova', '2023-12-20', 3, 'Second'),
(4, 'egorova', '2024-12-20', 4, 'First'),
(5, 'bondarenko', '2025-01-15', 1, 'Second'),
(6, 'kovalenko', '2025-01-16', 2, 'First'),
(7, 'shevchenko', '2025-01-17', 1, 'Third'),
(8, 'tkachenko', '2025-01-18', 3, 'Second'),
(9, 'volkova', '2025-01-19', 2, 'Second'),
(10, 'dmitrieva', '2025-01-20', 2, 'Third'),
(11, 'kuzmina', '2025-01-21', 2, 'First'),
(12, 'liknarovich', '2025-01-22', 4, 'First');

-- --------------------------------------------------------

--
-- Структура таблицы `nurse_ward`
--

CREATE TABLE `nurse_ward` (
  `fid_nurse` int NOT NULL,
  `fid_ward` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `nurse_ward`
--

INSERT INTO `nurse_ward` (`fid_nurse`, `fid_ward`) VALUES
(1, 1),
(4, 1),
(4, 2),
(3, 2),
(3, 3),
(2, 1),
(5, 4),
(6, 5),
(7, 5),
(8, 6),
(9, 2),
(10, 4),
(11, 7),
(12, 8),
(6, 5),
(6, 3),
(9, 5),
(8, 8),
(12, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `ward`
--

CREATE TABLE `ward` (
  `id_ward` int NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `ward`
--

INSERT INTO `ward` (`id_ward`, `name`) VALUES
(1, 'WardFirst'),
(2, 'WardSecond'),
(3, 'WardThird'),
(4, 'WardFourth'),
(5, 'WardFifth'),
(6, 'WardSix'),
(7, 'WardSeven'),
(8, 'WardEight');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `nurse`
--
ALTER TABLE `nurse`
  ADD PRIMARY KEY (`id_nurse`),
  ADD UNIQUE KEY `id_nurse` (`id_nurse`);

--
-- Индексы таблицы `nurse_ward`
--
ALTER TABLE `nurse_ward`
  ADD KEY `fid_nurse` (`fid_nurse`),
  ADD KEY `fid_ward` (`fid_ward`);

--
-- Индексы таблицы `ward`
--
ALTER TABLE `ward`
  ADD PRIMARY KEY (`id_ward`),
  ADD UNIQUE KEY `id_ward` (`id_ward`);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `nurse_ward`
--
ALTER TABLE `nurse_ward`
  ADD CONSTRAINT `nurse_ward_ibfk_1` FOREIGN KEY (`fid_nurse`) REFERENCES `nurse` (`id_nurse`),
  ADD CONSTRAINT `nurse_ward_ibfk_2` FOREIGN KEY (`fid_ward`) REFERENCES `ward` (`id_ward`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
