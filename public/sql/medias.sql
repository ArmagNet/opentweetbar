CREATE TABLE `medias` (
  `med_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `med_sna_id` bigint(20) NOT NULL,
  `med_content` longblob NOT NULL,
  `med_mimetype` varchar(255) NOT NULL,
  `med_name` varchar(255) NOT NULL,
  PRIMARY KEY (`med_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
