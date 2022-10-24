CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) COLLATE 'utf8_czech_ci' NOT NULL,
  `text` longtext COLLATE 'utf8_czech_ci' NULL,
  `is_published` bit NOT NULL DEFAULT b'0',
  `is_pinned` bit NOT NULL DEFAULT b'0',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `created_user_id` int(10) unsigned NOT NULL,
  `changed` datetime NULL,
  `changed_user_id` int(10) unsigned NULL,
  `not_deleted` bit NULL DEFAULT b'1',
  FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`changed_user_id`) REFERENCES `user` (`id`)
) COLLATE 'utf8_general_ci';