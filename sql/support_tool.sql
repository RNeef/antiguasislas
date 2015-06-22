CREATE  TABLE IF NOT EXISTS `rights` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) 
)
ENGINE = InnoDB
COLLATE='utf8_general_ci';

INSERT INTO `rights` (`name`)VALUES
('Administrator'),
('Supporter');

CREATE  TABLE IF NOT EXISTS `supporter` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `rightId` INT(11) NOT NULL ,
  `password` VARCHAR(100) NOT NULL ,
  `e-mail` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `rights_idx` (`rightId` ASC) ,
  CONSTRAINT `rights`
    FOREIGN KEY (`rightId` )
    REFERENCES `rights` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COLLATE='utf8_general_ci';

INSERT INTO `supporter` (`name`, `rightId`, `password`)VALUES
('Rolf', 1, '8291562b806089514fc5cac70c7a660668341b2244fd6411624e1de7b0d48ee7'),
('gummihuhn', 1, 'e9ad86c2a8c4bb3f4158492b54c9c412226929d6c1a0845e5aacafc639b9116e');


CREATE  TABLE IF NOT EXISTS `menu_points` (
  `id` INT(11) NOT NULL  AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `action` VARCHAR(50) NOT NULL ,
  `rightId` INT(11) NOT NULL ,
  `left_site` SMALLINT NOT NULL ,
  `right_site` SMALLINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `rights_idx` (`rightId` ASC) ,
  UNIQUE INDEX `left_site_UNIQUE` (`left_site` ASC) ,
  UNIQUE INDEX `right_site_UNIQUE` (`right_site` ASC) ,
  CONSTRAINT `menue_rights`
    FOREIGN KEY (`rightId` )
    REFERENCES `rights` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COLLATE='utf8_general_ci';

INSERT INTO `menu_points` (`name`, `rightId`, `left_site`, `right_site`)VALUES
(1, 'Administration', '', 1, 1, 5),
(2, 'Neuen Supporter', 'newsupporter', 1, 2, 3),
(3, 'Supporter löschen', '', 1, 3, 4),
(4, 'Tabellen bearbeiten', '', 1, 6, 11),
(5, 'Tabelle Länder', 'lands', 1, 7, 8),
(6, 'User bearbeiten', '', 2, 12, 15),
(7, 'User suchen', '', 2, 13, 14),
(8, 'Tabelle Karten', 'land_maps', 1, 8, 9);

CREATE TABLE `lands` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL ,
	`generation` SMALLINT(6) NOT NULL ,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `land_maps` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `landId` INT(11) NOT NULL,
    `landKoords` VARCHAR(45) NOT NULL,
    `backgroundImage` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `landmaps_idx` (`landId`),
    CONSTRAINT `landmaps` FOREIGN KEY (`landId`) REFERENCES `lands` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;