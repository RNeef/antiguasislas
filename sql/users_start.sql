CREATE  TABLE IF NOT EXISTS `lands` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `land_maps` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `landId` INT(11) NULL ,
  `land_koords` VARCHAR(45) NULL ,
  `backgroud_image` VARCHAR(100) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `landmaps_idx` (`landId` ASC) ,
  CONSTRAINT `landmaps`
    FOREIGN KEY (`landId` )
    REFERENCES `lands` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `password` VARCHAR(250) NOT NULL ,
  `mail` VARCHAR(100) NOT NULL ,
  `register_date` DATETIME NOT NULL ,
  `current_map` INT(11) NOT NULL DEFAULT '1' ,
  `current_x_pos` SMALLINT NOT NULL DEFAULT 230 ,
  `current_y_pos` SMALLINT NOT NULL DEFAULT 375 ,
  `highscore_points` BIGINT NOT NULL DEFAULT 0 ,
  `level` SMALLINT NOT NULL DEFAULT 1 ,
  `activ` BIT(1) NOT NULL DEFAULT b'0' ,
  `activhash` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `usermaps_idx` (`current_map` ASC) ,
  CONSTRAINT `usermaps`
    FOREIGN KEY (`current_map` )
    REFERENCES `land_maps` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `user_game_datas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `userId` INT(11) NOT NULL ,
  `money` BIGINT NOT NULL DEFAULT 0 ,
  `perls` BIGINT NOT NULL DEFAULT 0 ,
  `working_points` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) ,
  INDEX `user_idx` (`userId` ASC) ,
  CONSTRAINT `user`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `land_neighbors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `left` INT(11) NULL ,
  `right` INT(11) NULL ,
  `top` INT(11) NULL ,
  `bottom` INT(11) NULL ,
  `current` INT(11) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `topl_idx` (`top` ASC) ,
  INDEX `bottoml_idx` (`bottom` ASC) ,
  INDEX `leftl_idx` (`left` ASC) ,
  INDEX `rightl_idx` (`right` ASC) ,
  INDEX `currentl_idx` (`current` ASC) ,
  CONSTRAINT `topl`
    FOREIGN KEY (`top` )
    REFERENCES `land_maps` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `bottoml`
    FOREIGN KEY (`bottom` )
    REFERENCES `land_maps` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `leftl`
    FOREIGN KEY (`left` )
    REFERENCES `land_maps` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `rightl`
    FOREIGN KEY (`right` )
    REFERENCES `land_maps` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `currentl`
    FOREIGN KEY (`current` )
    REFERENCES `land_maps` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

ALTER TABLE `user_game_datas` ADD `generation` SMALLINT;
ALTER TABLE `user_game_datas` ADD `generation_days` SMALLINT;
ALTER TABLE `lands` ADD `generation` SMALLINT;