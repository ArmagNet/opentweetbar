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
session_start();
include_once("config/database.php");
include_once("config/mail.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/TweetBo.php");
require_once("engine/bo/UserBo.php");
include_once("language/language.php");

require_once("engine/notification/NotifierFactory.php");
require_once("engine/notification/MailNotifier.php");
require_once("engine/notification/SimpleDmNotifier.php");
require_once("engine/notification/DmNotifier.php");

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$tweetBo = TweetBo::newInstance($connection);
$userBo = UserBo::newInstance($connection);

$userId = $_REQUEST["userId"];
$hash = $_REQUEST["hash"];
$tweetId = $_REQUEST["tweetId"];

// The tweet id must be a numeric
if (!is_numeric($tweetId)) {
	exit();
}

$tweet = $tweetBo->getTweet($tweetId);

// The tweet can't be null
if (!$tweet) {
	exit();
}

$trueHash = TweetBo::hash($tweet, $userId);


// The hash is verified (forged form)
if ($trueHash != $hash) {
	exit();
}

$validatorGroup = $accountBo->getValidator($tweet["twe_destination"], $userId);

// We must be in a validator group
if (!$validatorGroup) {
	if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], "t.co") !== false) {
		echo lang("do_ask_for_modification_error", true, $user["use_language"]);
	}
	else {
		echo json_encode(array("ko" => "ko", "message" => "not_allowed"));
	}
	exit();
}

$data = array();

// tag it

$tweetToUpdate = array("twe_id" => $tweet["twe_id"]);
$tweetToUpdate["twe_ask_modification"] = 0;
$tweetToUpdate["twe_content"] = $_REQUEST["content"];
$tweetToUpdate["twe_author"] = $userId;
$tweetBo->update($tweetToUpdate);

$tweet["twe_content"] = $_REQUEST["content"];

// notify it

//print_r($tweet);

foreach($tweet["validations"] as $validation) {
//	print_r($validation);

	if ($validation["tva_validator"] != $userId) {
		$tweetBo->removeValidation($validation);
	}
}

if (isset($config["cron_enabled"]) && $config["cron_enabled"]) {
	$accountId = $tweet["twe_destination"];
	//		error_log("php do_cron_notifier.php $accountId $userId " . $tweet["twe_id"] . " > /dev/null 2> /dev/null &");
	exec("php do_cron_notifier.php $accountId $userId " . $tweet["twe_id"] . " > /dev/null 2> /dev/null &");
}
else {
	$validators = $accountBo->getAccountValidators($accountId);

	//	print_r($validators);

	foreach($validators as $validator) {
		if ($validator["use_id"] != $userId) {
			$hash = TweetBo::hash($tweet, $validator["use_id"]);

			$validationLink = $config["base_url"] . "dvt.php?";
			$validationLink .= "u=" . $validator["use_id"];
			$validationLink .= "&h=$hash";
			$validationLink .= "&t=" . $tweet["twe_id"];

			$notifier = NotifierFactory::getInstance($validator["use_notification"]);
			if ($notifier) {
				$notifier->notifyValidationLink($account, $validator, $tweet, $validationLink);
			}
		}
	}
}


$data["ok"] = "ok";
$data["hash"] = TweetBo::hash($tweet, $userId);

echo json_encode($data);
?>