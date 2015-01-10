<?php /*
	Copyright 2014 Cédric Levieux, Jérémy Collot, ArmagNet

	This file is part of OpenTweetBar.

    OpenTweetBar is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    OpenTweetBar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with OpenTweetBar.  If not, see <http://www.gnu.org/licenses/>.
*/
/*
 * Don't forget to include a cron line in the crontab like this one :

* * * * * cd /my/installed/opentweetbar/path/ && php do_cron.php

 */
include_once("config/database.php");
require_once("engine/bo/TweetBo.php");

$tweetBo = TweetBo::newInstance(openConnection());
$now = date("Y-m-d H:i:s");

$tweets = $tweetBo->getCronedTweets($now);

foreach($tweets as $tweet) {
	$tweetBo->sendTweet($tweet);
	$tweetBo->updateStatus($tweet, "validated");
}

exit("done\n");
?>