-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: database:3306
-- Время создания: Июн 07 2025 г., 15:27
-- Версия сервера: 8.4.4
-- Версия PHP: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `boardnotes`
--

-- --------------------------------------------------------

--
-- Структура таблицы `boards`
--

CREATE TABLE `boards` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `boards`
--

INSERT INTO `boards` (`id`, `user_id`, `name`, `created_at`, `updated_at`, `session_id`) VALUES
(1, 1, 'Личная доска', '2025-05-20 15:53:08', '2025-05-20 15:53:08', NULL),
(3, 3, 'Личная доска', '2025-05-21 15:14:24', '2025-05-21 15:15:06', NULL),
(15, 9, 'Личная доска', '2025-05-22 15:29:06', '2025-05-22 20:58:24', NULL),
(16, 12, 'Личная доска', '2025-05-22 15:35:59', '2025-05-22 20:58:27', NULL),
(17, 13, 'Личная доска', '2025-05-22 15:41:45', '2025-05-22 20:58:28', NULL),
(26, 16, 'Личная доска', '2025-05-30 18:08:13', '2025-05-30 18:13:03', NULL),
(27, 17, 'Личная доска', '2025-05-30 18:13:43', '2025-05-30 18:13:43', NULL),
(32, 1, '123123', '2025-05-30 19:35:52', '2025-05-30 19:35:52', 21),
(33, 1, '123123', '2025-05-30 19:56:32', '2025-05-30 19:56:32', 22),
(34, 1, 'Тест 3', '2025-06-06 15:30:30', '2025-06-06 15:30:30', 23),
(35, 1, '1', '2025-06-06 15:51:20', '2025-06-06 15:51:20', 24);

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int NOT NULL,
  `board_id` int NOT NULL,
  `user_id` int NOT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `filepath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mimetype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `size` int DEFAULT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `notes`
--

CREATE TABLE `notes` (
  `id` int NOT NULL,
  `board_id` int NOT NULL,
  `note_column` enum('todo','doing','done') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `marker` enum('created','updated') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `notes`
--

INSERT INTO `notes` (`id`, `board_id`, `note_column`, `content`, `created_at`, `updated_at`, `updated_by`, `marker`) VALUES
(33, 3, 'done', '123123', '2025-05-21 16:08:17', '2025-05-21 16:53:11', NULL, 'created'),
(37, 3, 'done', 'ё1', '2025-05-21 16:53:16', '2025-05-21 16:59:58', NULL, 'created'),
(38, 3, 'todo', '21123', '2025-05-21 16:59:48', '2025-05-21 16:59:48', NULL, 'created'),
(39, 15, 'todo', '1234', '2025-05-22 15:34:51', '2025-05-22 15:34:51', NULL, 'created'),
(55, 3, 'todo', '111111111', '2025-05-22 21:34:35', '2025-05-22 21:34:35', 3, 'created');

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE `sessions` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `owner_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `sessions`
--

INSERT INTO `sessions` (`id`, `name`, `status`, `owner_id`, `created_at`, `updated_at`) VALUES
(21, 'Тест', 'completed', 1, '2025-05-30 19:35:52', '2025-06-05 20:41:18'),
(22, 'Тест 2', 'active', 1, '2025-05-30 19:56:32', '2025-06-05 20:41:29'),
(23, 'Тест 3', 'active', 1, '2025-06-06 15:30:30', '2025-06-06 15:30:30'),
(24, '1', 'completed', 1, '2025-06-06 15:51:20', '2025-06-06 15:51:20');

-- --------------------------------------------------------

--
-- Структура таблицы `sessionuser`
--

CREATE TABLE `sessionuser` (
  `id` int NOT NULL,
  `session_id` int NOT NULL,
  `user_id` int NOT NULL,
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `sessionuser`
--

INSERT INTO `sessionuser` (`id`, `session_id`, `user_id`, `joined_at`) VALUES
(46, 21, 3, '2025-05-30 19:35:52'),
(47, 21, 12, '2025-05-30 19:35:52'),
(48, 21, 9, '2025-05-30 19:35:52'),
(49, 21, 13, '2025-05-30 19:35:52'),
(50, 21, 16, '2025-05-30 19:35:52'),
(51, 21, 17, '2025-05-30 19:35:52'),
(52, 22, 9, '2025-05-30 19:56:32'),
(53, 22, 3, '2025-05-30 19:56:32'),
(54, 22, 12, '2025-05-30 19:56:32'),
(55, 22, 13, '2025-05-30 19:56:32'),
(56, 22, 16, '2025-05-30 19:56:32'),
(57, 22, 17, '2025-05-30 19:56:32'),
(58, 23, 3, '2025-06-06 15:30:30'),
(59, 23, 9, '2025-06-06 15:30:30'),
(60, 23, 12, '2025-06-06 15:30:30'),
(61, 23, 13, '2025-06-06 15:30:30'),
(62, 23, 16, '2025-06-06 15:30:30'),
(63, 23, 17, '2025-06-06 15:30:30'),
(64, 24, 16, '2025-06-06 15:51:20'),
(65, 24, 17, '2025-06-06 15:51:20'),
(66, 24, 12, '2025-06-06 15:51:20'),
(67, 24, 13, '2025-06-06 15:51:20'),
(68, 24, 9, '2025-06-06 15:51:20'),
(69, 24, 3, '2025-06-06 15:51:20');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'SanchoKusika', 'sanchezzkusika@gmail.com', '$2y$10$gnSOo3dM51UH6rg.0hWT..xeiSyc.q.hSsUnRbimHK8r0KNcIg/BK', 'uploads/avatars/682c986f7a599.png', '2025-05-20 14:15:05', '2025-06-07 13:55:08'),
(3, 'Test', 'test@gmail.com', '$2y$10$4zyhz46c0GM5Gu.zGqYk1.FAZI5eMGMu53qTEsoJLHfdPLmEJQIRS', 'assets/img/profile/avatar-placeholder.png', '2025-05-20 14:58:30', '2025-05-30 18:08:51'),
(9, 'SessionTest', 'session@gmail.com', '$2y$10$WSmnTagna3A2DwtArEl0fO0jCuEwT4JEMkP8myk3EjBAPJJmP4s7a', 'assets/img/profile/avatar-placeholder.png', '2025-05-22 15:29:06', '2025-05-30 18:08:50'),
(12, 'RegisterTest', 'RegisterTest@gmail.com', '$2y$10$dZBPrWibmD/hNXC1ENUg.e0c98PzLDRb97O.ght78C0H.Yc9kbkWO', 'assets/img/profile/avatar-placeholder.png', '2025-05-22 15:35:59', '2025-05-30 18:08:49'),
(13, 'NewUser', 'NewUser@mail.com', '$2y$10$DUuxvOAXNKUbyC023lr2uekG2vI7SWNPuNTMsPr4vhpu/qhlfRbo6', 'assets/img/profile/avatar-placeholder.png', '2025-05-22 15:41:45', '2025-05-30 18:08:47'),
(16, 'AvatarTest', 'avatar@mail.com', '$2y$10$LqJe7dcdNeJJgobC0neDw.mVlKl5ngNZCOZUYf2sN2CXq78S2TMVC', 'uploads/avatars/6839f48434cdd.png', '2025-05-30 18:08:13', '2025-05-30 18:10:18'),
(17, '1234', '123@mail.ru', '$2y$10$/u0tc5RDvmmhf7xgeuuOLe3Fj4RGGpAF78SK5fM7r5JESXRHSFDHa', 'uploads/avatars/6839f57070048.png', '2025-05-30 18:13:43', '2025-05-30 18:14:08');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `board_id` (`board_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `board_id` (`board_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Индексы таблицы `sessionuser`
--
ALTER TABLE `sessionuser`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `boards`
--
ALTER TABLE `boards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT для таблицы `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT для таблицы `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `sessionuser`
--
ALTER TABLE `sessionuser`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `boards`
--
ALTER TABLE `boards`
  ADD CONSTRAINT `boards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `boards_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `sessionuser`
--
ALTER TABLE `sessionuser`
  ADD CONSTRAINT `sessionuser_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessionuser_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
