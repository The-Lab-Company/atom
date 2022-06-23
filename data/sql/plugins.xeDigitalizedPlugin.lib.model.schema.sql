
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- digitalized
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `digitalized`;


CREATE TABLE `digitalized`
(
	`id` INTEGER  NOT NULL,
	PRIMARY KEY (`id`),
	CONSTRAINT `digitalized_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `information_object` (`id`)
		ON DELETE CASCADE
)Engine=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
