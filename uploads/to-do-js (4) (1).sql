-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-09-2025 a las 04:32:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `to-do-js`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `size_bytes` bigint(20) UNSIGNED NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `ip_address` varbinary(16) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `labels`
--

CREATE TABLE `labels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(20) DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `owner_id`, `is_archived`, `created_at`, `updated_at`, `assigned_to`) VALUES
(1, 'wffw', 'trj', 1, 0, '2025-09-17 16:53:27', '2025-09-17 16:53:27', NULL),
(2, 'wffw', 'trj', 1, 0, '2025-09-17 17:10:39', '2025-09-17 17:10:39', NULL),
(3, 'wffw', 'trj', 1, 0, '2025-09-17 17:11:30', '2025-09-17 17:11:30', NULL),
(4, 's1000rr', 'Motto', 1, 0, '2025-09-17 17:17:39', '2025-09-17 17:17:39', NULL),
(5, 'paisaje', 'Simon', 1, 1, '2025-09-17 17:23:07', '2025-09-17 17:23:07', NULL),
(6, 'hjbiubibb', 'hj h   i', 1, 1, '2025-09-17 17:24:54', '2025-09-17 17:24:54', NULL),
(7, 'simono', 'bycvayvaucbuiabcui', 7, 0, '2025-09-17 21:00:45', '2025-09-17 21:00:45', 2),
(8, 'simono', 'bycvayvaucbuiabcui', 7, 0, '2025-09-17 21:09:43', '2025-09-17 21:09:43', 2),
(9, 'termino de tareas', 'pille necesita cambiar los estilos', 7, 0, '2025-09-17 21:10:05', '2025-09-18 02:16:44', 6),
(10, 'control de ps5', 'un super control para jugar con susu amigos jici ci huc wh yu y vty ty tttvtvtvx', 7, 1, '2025-09-17 21:22:36', '2025-09-18 02:02:47', 5),
(11, 'poder elimainar usuarios', 'eliminar simon', 7, 1, '2025-09-18 02:20:08', '2025-09-18 12:18:42', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project_members`
--

CREATE TABLE `project_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reminders`
--

CREATE TABLE `reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `remind_at` datetime NOT NULL,
  `channel` enum('email','push','both') NOT NULL DEFAULT 'email',
  `sent_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `description_md` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `creator_id` bigint(20) UNSIGNED NOT NULL,
  `assignee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Pendiente','En_progreso','Completada','Archivada') DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'medium',
  `due_date` datetime DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `estimated_time` int(10) UNSIGNED DEFAULT NULL,
  `time_spent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `recurrence_rule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `due_completed_at` datetime DEFAULT NULL,
  `parent_task_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `etiquetas` varchar(255) DEFAULT NULL,
  `prioridad` enum('Alta','Media','Baja') DEFAULT 'Media'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description_md`, `creator_id`, `assignee_id`, `project_id`, `status`, `priority`, `due_date`, `archivo`, `start_date`, `estimated_time`, `time_spent`, `recurrence_rule`, `due_completed_at`, `parent_task_id`, `position`, `created_at`, `updated_at`, `etiquetas`, `prioridad`) VALUES
(30, 'borraer esta tareas', '', 6, NULL, NULL, 'En_progreso', 'medium', '2025-10-04 00:00:00', '', '2025-09-17 00:00:00', NULL, 0, NULL, NULL, NULL, 0, '2025-09-18 02:34:01', '2025-09-18 02:34:01', 'sena , tun tun sahur , messi', 'Media'),
(31, 'ioniobnioio', 'HHHHHHH', 7, NULL, NULL, 'Completada', 'medium', '2025-09-17 00:00:00', '', '2025-09-17 00:00:00', NULL, 0, NULL, NULL, NULL, 0, '2025-09-18 02:40:17', '2025-09-18 12:22:05', 'estudio , sena', 'Media'),
(34, 'bdvyvdyvdyd', '', 4, NULL, NULL, 'En_progreso', 'medium', '2025-10-02 00:00:00', 'Act3_Ficheros_Python.pdf', '2025-09-23 00:00:00', NULL, 0, NULL, NULL, NULL, 0, '2025-09-19 00:52:58', '2025-09-19 00:52:58', 'sena , funk pop', 'Baja'),
(44, 'subsubsvus', '', 4, NULL, NULL, 'Pendiente', 'medium', NULL, '', NULL, NULL, 0, NULL, NULL, NULL, 0, '2025-09-19 01:20:49', '2025-09-19 01:20:49', '', 'Media'),
(45, 'snsbsibsi', '', 4, NULL, NULL, 'Pendiente', 'medium', NULL, 'Act3_Ficheros_Python.pdf', NULL, NULL, 0, NULL, NULL, NULL, 0, '2025-09-19 01:37:34', '2025-09-19 01:37:34', '', 'Media'),
(47, 'sbsubsuss', '', 4, NULL, NULL, 'Completada', 'medium', NULL, 'Act3_Ficheros_Python (1).pdf', NULL, NULL, 0, NULL, NULL, NULL, 0, '2025-09-19 01:48:14', '2025-09-19 01:48:14', '', 'Media'),
(48, 'simon gallego', '', 4, NULL, NULL, 'En_progreso', 'medium', '2025-09-25 00:00:00', 'Act3_Ficheros_Python.pdf', '2025-09-18 00:00:00', NULL, 0, NULL, NULL, NULL, 0, '2025-09-19 01:52:00', '2025-09-19 01:52:00', 'urgente, messi', 'Baja'),
(49, 'dwewwfwff', '', 6, NULL, NULL, 'Completada', 'medium', NULL, '', NULL, NULL, 0, NULL, NULL, 30, 0, '2025-09-19 02:02:39', '2025-09-19 02:02:39', '', 'Media');

--
-- Disparadores `tasks`
--
DELIMITER $$
CREATE TRIGGER `trg_tasks_done_timestamp` BEFORE UPDATE ON `tasks` FOR EACH ROW BEGIN
  IF NEW.status = 'done' AND (OLD.status <> 'done' OR OLD.status IS NULL) THEN
    SET NEW.due_completed_at = IFNULL(NEW.due_completed_at, NOW());
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `task_labels`
--

CREATE TABLE `task_labels` (
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `label_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `name` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `notify_pref` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notify_pref`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `reset_expiration` datetime DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'img/default_profile.png',
  `role` enum('admin','moderator','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password_hash`, `avatar_url`, `notify_pref`, `is_active`, `created_at`, `updated_at`, `reset_token`, `reset_expires_at`, `reset_expiration`, `profile_pic`, `role`) VALUES
(1, 'goldarmony8776@gmail.com', 'Jacob', '$2y$10$XR.ZhRqV/ep8NdAkR8uz/.r0EUhx2R4Ng8IzKDPLVrNPUbMc6mgNm', NULL, NULL, 1, '2025-09-02 21:06:24', '2025-09-17 20:36:19', NULL, NULL, NULL, NULL, 'user'),
(2, 'roro@gmail.com', 'roro', '$2y$10$qJSdIt/T896bPO32KpN/WucnSXDuV/eeWpr8oIqluVmcFVqYsd1IS', NULL, NULL, 1, '2025-09-09 19:21:42', '2025-09-09 19:21:42', NULL, NULL, NULL, 'img/default_profile.png', 'user'),
(3, '290290292@gmail.com', 'Sys', '$2y$10$yAD1j0aFX1dXQD5pe7ve3.gtCdtPRIMkqbnlwojhLqJXMolKZYYjy', NULL, NULL, 1, '2025-09-10 19:45:46', '2025-09-10 19:45:46', NULL, NULL, NULL, 'img/default_profile.png', 'user'),
(4, 'alba.lucia.sys@gmail.com', 'Sys', '$2y$10$pXA0WmtHWHFpIb4pPcCghe54NMSHueF68REp4jsXzGSqQDFJprhMu', NULL, NULL, 1, '2025-09-10 19:45:59', '2025-09-17 20:37:25', NULL, NULL, NULL, 'img/perfiles/1758141445_Imagen de WhatsApp 2025-07-18 a las 23.43.40_bf6e27df.jpg', 'user'),
(5, 'play4simon2305@gmail.com', 'simonoo', '$2y$10$UChQA2Tb2r5XyCYAOfgRGOUGkNRHoehBkSoyBZMZU0WJoQVyjOL1K', NULL, NULL, 0, '2025-09-11 19:29:19', '2025-09-18 02:03:13', 'f840308e54752bd7b48c44e89c90602faa56a7dbf59e98f46e9fff04cd4b4d72', NULL, '2025-09-11 22:29:32', 'img/default_profile.png', 'user'),
(6, 'promhansa@gmail.com', 'Prueba gmail.com', '$2y$10$BO90phwBjbluQuv14Mr3gedP09AJfxtiph/lgOS9k68lXaxtiygQa', NULL, NULL, 1, '2025-09-11 19:31:11', '2025-09-18 02:21:53', '1b0d4cca7da1b5172ac058db0aac9389cdcab92ba3c68e1e4b6111ccc83447a30fd50635883918856751fda0a458518020dc', NULL, '2025-09-15 20:16:43', 'img/default_profile.png', 'user'),
(7, 'simon.23051997@gmail.com', 'principal', '$2y$10$aA8Cc3XTQeK/IdpOXxE5Veuyf3hpQeK7ofBfr2hlWGpChrPEgTX/q', NULL, NULL, 1, '2025-09-16 15:00:48', '2025-09-18 15:18:14', NULL, NULL, NULL, 'img/perfiles/1758208694_Imagen de WhatsApp 2025-07-18 a las 23.43.40_bf6e27df.jpg', 'admin');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_task_search`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_task_search` (
`id` bigint(20) unsigned
,`title` varchar(200)
,`description_md` mediumtext
,`status` enum('Pendiente','En_progreso','Completada','Archivada')
,`priority` enum('low','medium','high','urgent')
,`due_date` datetime
,`start_date` datetime
,`project_id` bigint(20) unsigned
,`assignee_id` bigint(20) unsigned
,`labels` mediumtext
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_task_search`
--
DROP TABLE IF EXISTS `v_task_search`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_task_search`  AS SELECT `t`.`id` AS `id`, `t`.`title` AS `title`, `t`.`description_md` AS `description_md`, `t`.`status` AS `status`, `t`.`priority` AS `priority`, `t`.`due_date` AS `due_date`, `t`.`start_date` AS `start_date`, `t`.`project_id` AS `project_id`, `t`.`assignee_id` AS `assignee_id`, group_concat(distinct `l`.`name` order by `l`.`name` ASC separator ',') AS `labels` FROM ((`tasks` `t` left join `task_labels` `tl` on(`tl`.`task_id` = `t`.`id`)) left join `labels` `l` on(`l`.`id` = `tl`.`label_id`)) GROUP BY `t`.`id` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_attachments_task` (`task_id`);

--
-- Indices de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_audit_actor` (`actor_id`);

--
-- Indices de la tabla `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_comments_task` (`task_id`);

--
-- Indices de la tabla `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_label_scope` (`owner_id`,`project_id`,`name`),
  ADD KEY `idx_labels_project` (`project_id`),
  ADD KEY `idx_labels_owner` (`owner_id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`);

--
-- Indices de la tabla `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_projects_owner` (`owner_id`),
  ADD KEY `idx_projects_archived` (`is_archived`);

--
-- Indices de la tabla `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_project_user` (`project_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_project_members_role` (`role_id`);

--
-- Indices de la tabla `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_refresh_token` (`token_hash`),
  ADD KEY `idx_refresh_user` (`user_id`);

--
-- Indices de la tabla `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `idx_reminders_due` (`sent_at`,`remind_at`),
  ADD KEY `idx_reminders_user` (`user_id`);

--
-- Indices de la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `idx_tasks_project` (`project_id`),
  ADD KEY `idx_tasks_assignee` (`assignee_id`),
  ADD KEY `idx_tasks_status` (`status`),
  ADD KEY `idx_tasks_priority` (`priority`),
  ADD KEY `idx_tasks_due` (`due_date`),
  ADD KEY `idx_tasks_parent` (`parent_task_id`);
ALTER TABLE `tasks` ADD FULLTEXT KEY `ftx_tasks_text` (`title`,`description_md`);

--
-- Indices de la tabla `task_labels`
--
ALTER TABLE `task_labels`
  ADD PRIMARY KEY (`task_id`,`label_id`),
  ADD KEY `label_id` (`label_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_active` (`is_active`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `labels`
--
ALTER TABLE `labels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attachments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `labels`
--
ALTER TABLE `labels`
  ADD CONSTRAINT `labels_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `labels_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD CONSTRAINT `refresh_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reminders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_4` FOREIGN KEY (`parent_task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `task_labels`
--
ALTER TABLE `task_labels`
  ADD CONSTRAINT `task_labels_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_labels_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
