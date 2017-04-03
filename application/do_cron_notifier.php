<?php /*
	Copyright 2014-2015 Cédric Levieux, Jérémy Collot, ArmagNet

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
include_once("config/mail.php");
include_once("language/language.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/MediaBo.php");
require_once("engine/bo/TweetBo.php");

require_once("engine/notification/NotifierFactory.php");
require_once("engine/notification/MailNotifier.php");
require_once("engine/notification/SimpleDmNotifier.php");
require_once("engine/notification/DmNotifier.php");

$connection = openConnection();

$tweetBo = TweetBo::newInstance($connection);
$accountBo = AccountBo::newInstance($connection);

$accountId = $argv[1];
$userId = $argv[2];
$tweetId = $argv[3];
$method = "notifyValidationLink";

if (isset($argv[4])) {
	$method = $argv[4];
}

$tweet = $tweetBo->getTweet($tweetId);
$account = $accountBo->getAccount($accountId);

$validators = $accountBo->getAccountValidators($accountId);

//	print_r($validators);

foreach($validators as $validator) {
	if ($validator["use_id"] == $userId) continue;
	if ($validator["vgr_score"] < 1) continue;
	
	$hash = TweetBo::hash($tweet, $validator["use_id"]);

	$validationLink = $config["base_url"] . "dvt.php?";
	$validationLink .= "u=" . $validator["use_id"];
	$validationLink .= "&h=$hash";
	$validationLink .= "&t=" . $tweet["twe_id"];

	$notifier = NotifierFactory::getInstance($validator["use_notification"]);
	if ($notifier) {
		// TODO use $method
		$notifier->notifyValidationLink($account, $validator, $tweet, $validationLink);
	}

	echo $validator["use_id"] . " notified\n";
}

exit("done\n");
?>
