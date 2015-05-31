CREATE TABLE `tweet_medias` (
  `tme_tweet_id` bigint(20) NOT NULL,
  `tme_media_id` bigint(20) NOT NULL,
  PRIMARY KEY (`tme_tweet_id`,`tme_media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
