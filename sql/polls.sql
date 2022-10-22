REATE TABLE `poll` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `is_private` bit(1) NOT NULL DEFAULT b'0',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_user_id` int(10) unsigned NOT NULL,
  `changed` datetime DEFAULT NULL,
  `changed_user_id` int(10) unsigned DEFAULT NULL,
  `not_deleted` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `changed_user_id` (`changed_user_id`),
  CONSTRAINT `poll_ibfk_1` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `poll_ibfk_2` FOREIGN KEY (`changed_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- web_test.poll_option definition

CREATE TABLE `poll_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_user_id` int(10) unsigned NOT NULL,
  `changed` datetime DEFAULT NULL,
  `changed_user_id` int(10) unsigned DEFAULT NULL,
  `not_deleted` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `changed_user_id` (`changed_user_id`),
  CONSTRAINT `poll_option_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`),
  CONSTRAINT `poll_option_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `poll_option_ibfk_3` FOREIGN KEY (`changed_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- web_test.poll_participant definition

CREATE TABLE `poll_participant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_option_id` int(10) unsigned DEFAULT NULL COMMENT 'Zvolená možnost',
  `user_id` int(10) unsigned NOT NULL COMMENT 'Hlasující uživatel',
  `created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Datum přiřazení účastníka',
  `poll_id` int(10) unsigned NOT NULL COMMENT 'ID hlasování',
  `changed` datetime DEFAULT NULL COMMENT 'Datum hlasování',
  `not_deleted` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_participant_UN` (`user_id`,`poll_id`),
  KEY `poll_participant_FK` (`poll_id`),
  KEY `poll_participant_FK_1` (`poll_option_id`),
  CONSTRAINT `poll_participant_FK` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`),
  CONSTRAINT `poll_participant_FK_1` FOREIGN KEY (`poll_option_id`) REFERENCES `poll_option` (`id`),
  CONSTRAINT `poll_participant_FK_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- web_test.poll_role definition

CREATE TABLE `poll_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `poll_id` int(10) unsigned NOT NULL,
  `not_deleted` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_role_UN` (`role_id`,`poll_id`,`not_deleted`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `poll_role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  CONSTRAINT `poll_role_ibfk_2` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;