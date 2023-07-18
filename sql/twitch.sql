CREATE TABLE `user_twitch_account` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `user_id` int(10) unsigned NOT NULL,
   `twitch_id` varchar(255) DEFAULT NULL,
   `access_token` varchar(255) NOT NULL,
   `refresh_token` varchar(255) NOT NULL,
   `created` datetime NOT NULL DEFAULT current_timestamp(),
   `not_deleted` bit(1) DEFAULT b'1',
   PRIMARY KEY (`id`),
   UNIQUE KEY `user_twitch_account_UN` (`user_id`,`not_deleted`),
   CONSTRAINT `user_twitch_account_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

ALTER TABLE `user` ADD sub_required bit DEFAULT 0 NOT NULL;

