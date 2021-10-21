SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED DEFAULT NULL,
  `method` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `pattern` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

TRUNCATE TABLE `permission`;
INSERT INTO `permission` (`id`, `role_id`, `method`, `pattern`, `status`) VALUES
(970, 1, 'GET', '/admin/home', 'allow'),
(971, 1, 'GET', '/admin/users/list', 'allow'),
(972, 1, 'GET', '/admin/users/create', 'allow'),
(973, 1, 'POST', '/admin/users/save', 'allow'),
(974, 1, 'GET', '/admin/users/update/{id}', 'allow'),
(975, 1, 'GET', '/admin/users/delete/{id}', 'allow'),
(976, 1, 'DELETE', '/admin/users/delete', 'allow'),
(977, 1, 'GET', '/admin/users/roles/list', 'allow'),
(978, 1, 'GET', '/admin/users/roles/create', 'allow'),
(979, 1, 'POST', '/admin/users/roles/save', 'allow'),
(980, 1, 'GET', '/admin/users/roles/update/{id}', 'allow'),
(981, 1, 'GET', '/admin/users/roles/delete/{id}', 'allow'),
(982, 1, 'DELETE', '/admin/users/roles/delete', 'allow'),
(983, 1, 'GET', '/admin/profile', 'allow'),
(984, 1, 'POST', '/admin/profile/save', 'allow'),
(985, 1, 'GET', '/admin/profile/change-photo', 'allow'),
(986, 1, 'POST', '/admin/profile/save-photo', 'allow'),
(987, 1, 'POST', '/admin/profile/save-newpass', 'allow');

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

TRUNCATE TABLE `role`;
INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Desenvolvedor');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `role_id` int(10) UNSIGNED DEFAULT NULL,
  `login` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `pass` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

TRUNCATE TABLE `user`;
INSERT INTO `user` (`id`, `name`, `role_id`, `login`, `pass`, `lastlogin`, `photo`) VALUES
(1, 'User Admin', 1, 'admin@admin', '202cb962ac59075b964b07152d234b70', '2021-10-21 14:46:23', NULL);


ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_permission_role` (`role_id`);

ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_user_role` (`role_id`);


ALTER TABLE `permission`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;

ALTER TABLE `role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `permission`
  ADD CONSTRAINT `c_fk_permission_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `user`
  ADD CONSTRAINT `c_fk_user_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
