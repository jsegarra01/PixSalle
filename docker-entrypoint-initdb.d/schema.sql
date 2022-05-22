SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `pixsalle`;
USE `pixsalle`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `email`     VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `password`  VARCHAR(255)                                            NOT NULL,
    `createdAt` DATETIME                                                NOT NULL,
    `updatedAt` DATETIME                                                NOT NULL,
    `username`  VARCHAR(255)                                            NOT NULL,
    `phone`     VARCHAR(255)                                            NULL,
    `picture`   VARCHAR(255)                                            NULL,
    `membership`   VARCHAR(255)                                         NOT NULL,

    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `picture`;
CREATE TABLE `picture`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `pic_url`   VARCHAR(255)                                            NOT NULL,
    `user_id`   INT                                            NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `album`;
CREATE TABLE `album`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `name`      VARCHAR(255)                                            NOT NULL,
    `pic_id`    INT                                                     NOT NULL,
    `user_id`   INT                                                     NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (pic_id) REFERENCES picture(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `title`     VARCHAR(255)                                            NOT NULL,
    `content`   VARCHAR(255)                                            NOT NULL,
    `userId`    INT                                                     NOT NULL,

    PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;