CREATE TABLE positions (
    id int(10) unsigned auto_increment NOT NULL,
    name varchar(100) NOT NULL,
    text varchar(100) NULL,
    url text NOT NULL,
    `active` bit(1) NOT NULL DEFAULT b'1',
    created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_user_id int(10) unsigned NOT NULL,
    changed datetime NULL,
    changed_user_id int(10) unsigned NULL,
    not_deleted bit DEFAULT 1 NULL,
    CONSTRAINT positions_PK PRIMARY KEY (id),
    CONSTRAINT positions_UN UNIQUE KEY (name,not_deleted),
    CONSTRAINT positions_FK FOREIGN KEY (created_user_id) REFERENCES `user`(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT positions_FK_1 FOREIGN KEY (changed_user_id) REFERENCES `user`(id) ON DELETE RESTRICT ON UPDATE RESTRICT
)
    ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;
