ALTER TABLE `server` ADD description_short TEXT NOT NULL AFTER name;
ALTER TABLE `server` ADD description_full TEXT NOT NULL AFTER description_short;
ALTER TABLE `server` ADD changed datetime NULL;
ALTER TABLE `server` CHANGE changed changed datetime NULL AFTER created_user_id;
ALTER TABLE `server` ADD changed_user_id int(10) unsigned NULL;
ALTER TABLE `server` CHANGE changed_user_id changed_user_id int(10) unsigned NULL AFTER changed;
ALTER TABLE `server` CHANGE created created datetime DEFAULT current_timestamp() NOT NULL AFTER description_full;
ALTER TABLE `server` ADD CONSTRAINT server_FK_1 FOREIGN KEY (changed_user_id) REFERENCES `user`(id) ON DELETE RESTRICT ON UPDATE RESTRICT;


CREATE TABLE tag (
    id int (10) unsigned auto_increment NOT NULL,
    name varchar(100) NOT NULL,
    font_color varchar(100) NOT NULL,
    background_color varchar(100) NOT NULL,
    created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_user_id int (10) unsigned NOT NULL,
    changed datetime NULL,
    changed_user_id int(10) unsigned NULL,
    not_deleted bit(1) DEFAULT 1 NULL,
    CONSTRAINT tag_PK PRIMARY KEY (id),
    CONSTRAINT tag_UN UNIQUE KEY (name,not_deleted),
    CONSTRAINT tag_FK FOREIGN KEY (created_user_id) REFERENCES `user`(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT tag_FK_1 FOREIGN KEY (changed_user_id) REFERENCES `user`(id) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

CREATE TABLE server_tag (
    id int(10) unsigned auto_increment NOT NULL,
    server_id int(10) unsigned NOT NULL,
    tag_id int(10) unsigned NOT NULL,
    created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_user_id int(10) unsigned NOT NULL,
    changed datetime NULL,
    changed_user_id int(10) unsigned NULL,
    not_deleted bit(1) DEFAULT 1 NULL,
    CONSTRAINT server_tag_PK PRIMARY KEY (id),
    CONSTRAINT server_tag_UN UNIQUE KEY (server_id,tag_id,not_deleted),
    CONSTRAINT server_tag_FK_tag FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT server_tag_FK_server FOREIGN KEY (server_id) REFERENCES server(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT server_tag_FK_created_user FOREIGN KEY (created_user_id) REFERENCES `user`(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT server_tag_FK_changed_user FOREIGN KEY (changed_user_id) REFERENCES `user`(id) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

ALTER TABLE `server` ADD banner text NULL;
ALTER TABLE `server` CHANGE banner banner text NULL AFTER description_full;
ALTER TABLE `server` ADD `character` text NULL;
ALTER TABLE `server` CHANGE `character` `character` text NULL AFTER banner;
ALTER TABLE `server` ADD `show` bit(1) DEFAULT 0 NOT NULL;
ALTER TABLE `server` CHANGE `show` `show` bit(1) DEFAULT 0 NOT NULL AFTER `character`;
