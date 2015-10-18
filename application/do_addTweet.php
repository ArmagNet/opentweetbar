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
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/TweetBo.php");
include_once("language/language.php");

require_once("engine/notification/NotifierFactory.php");
require_once("engine/notification/MailNotifier.php");
require_once("engine/notification/SimpleDmNotifier.php");
require_once("engine/notification/DmNotifier.php");

$data = array();

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$tweetBo = TweetBo::newInstance($connection);
$logActionBo = LogActionBo::newInstance($connection);

$remoteIp = (isset($_SERVER["HTTP_X_REAL_IP"]) && $_SERVER["HTTP_X_REAL_IP"]) ? $_SERVER["HTTP_X_REAL_IP"] : $_SERVER["REMOTE_ADDR"];

$user = SessionUtils::getUser($_SESSION);
if ($user) {
	$userId = SessionUtils::getUserId($_SESSION);
	$login = $user;
	$password = $_REQUEST["password"];
}
else {
	$userId = -1;
	$nickname = $_REQUEST["nickname"];
	$mail = $_REQUEST["xxx"];
	$login = $nickname . "(" . $mail . ")";
	$password = $_REQUEST["password"];

	if (!isset($_REQUEST["cgv"]) || $_REQUEST["cgv"] != "okgirls") {
		$data["ok"] = "ok";
		$data["message"] = "ok";
		echo json_encode($data);
		exit();
	}
}

$accounts = array();
$accounts[] = $_REQUEST["account"];

if (isset($_REQUEST["secondaryAccounts"])) {
	foreach($_REQUEST["secondaryAccounts"] as $secondaryAccount) {
		$accounts[] = $secondaryAccount;
	}
}

foreach($accounts as $account) {
	$accountId = $accountBo->getAccountId($account);
	$account = $accountBo->getAccount($accountId);

	$validatorGroup = $accountBo->getValidator($accountId, $userId);

	// We must be in a validator group or the account permits anonymous tweet proposition
	if (	(
				($account["sco_anonymous_permitted"] == 0) ||
				($account["sco_anonymous_permitted"] == 1 && $account["sco_anonymous_password"] && $account["sco_anonymous_password"] != $password)
			)
			&& !$validatorGroup) {
		echo json_encode(array("ko" => "ko", "message" => "not_allowed"));
		exit();
	}
}

foreach($accounts as $account) {
	$accountId = $accountBo->getAccountId($account);
	$account = $accountBo->getAccount($accountId);

	$validatorGroup = $accountBo->getValidator($accountId, $userId);

	$tweet = array();

	if ($user) {
		$tweet["twe_author"] = $userId;
		$tweet["twe_anonymous_nickname"] = "";
		$tweet["twe_anonymous_mail"] = "";
	}
	else {
		$tweet["twe_author"] = 0;
		$tweet["twe_anonymous_nickname"] = $nickname;
		$tweet["twe_anonymous_mail"] = $mail;
	}
	$tweet["twe_destination"] = $accountId;
	$tweet["twe_content"] = $_REQUEST["tweet"];
	$tweet["twe_supports"] = $_REQUEST["supports"];
	$tweet["twe_validation_score"] = $account["sco_validation_score"];
	$tweet["twe_validation_duration"] = $_REQUEST["validationDuration"];
	$tweet["twe_cron_datetime"] = $_REQUEST["cronDate"];
	$tweet["twe_creation_datetime"] = date("Y-m-d H:i:s");
	if (isset($_REQUEST["toRetweet"])) {
		$tweet["twe_to_retweet"] = $_REQUEST["toRetweet"];
	}
	else {
		$tweet["twe_to_retweet"] = null;
	}

	$mediaIds = explode(",", $_REQUEST["mediaIds"]);

	$tweet["twe_media_ids"] = array();
	foreach($mediaIds as $mediaId) {
		if ($mediaId != -1) {
			$tweet["twe_media_ids"][] = trim($mediaId);
		}
	}

	//print_r($tweet);

	if ($tweetBo->addTweet($tweet)) {

		$logAction = array();
		$logAction["lac_status"] = 1;
		$logAction["lac_label"] = "addTweet";
		$logAction["lac_login"] = $login;
		$logAction["lac_ip"] = $remoteIp;

		$logActionBo->addLogAction($logAction);

		$data["ok"] = "ok";
	//	$data["validators"] = array();

		// If the user is connected and in a validator group then autovalidation
		if ($validatorGroup) {
			$validation = array();
			$validation["tva_tweet_id"] = $tweet["twe_id"];
			$validation["tva_validator"] = $tweet["twe_author"];
			$validation["tva_status"] = "validation";
			$validation["tva_motivation"] = "";
			$validation["tva_score"] = $validatorGroup["vgr_score"];
			$validation["tva_ip"] = $remoteIp;
			$validation["tva_referer"] = $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : '';

			if (isset($tweet["twe_creation_date"])) {
				$validation["tva_datetime"] = $tweet["twe_creation_date"];
			}
			else {
				$validation["tva_datetime"] = null;
			}

			$tweetBo->addValidation($validation);
		}

		if (isset($config["cron_enabled"]) && $config["cron_enabled"]) {
	//		error_log("php do_cron_notifier.php $accountId $userId " . $tweet["twe_id"] . " > /dev/null 2> /dev/null &");
			exec("php do_cron_notifier.php $accountId $userId " . $tweet["twe_id"] . " > /dev/null 2> /dev/null &");
		}
		else {
			$validators = $accountBo->getAccountValidators($accountId);

		//	print_r($validators);

			foreach($validators as $validator) {
				if ($validator["use_id"] == $userId) continue;

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
	else {
//		$data["ko"] = "ko";
	}
}

if (isset($data["ok"])) {
	$data["ko"] = "ko";
}

echo json_encode($data);
?>