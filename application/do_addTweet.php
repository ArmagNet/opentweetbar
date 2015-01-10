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
session_start();
include_once("config/database.php");
include_once("config/mail.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/TweetBo.php");
include_once("language/language.php");

$data = array();

$accountBo = AccountBo::newInstance(openConnection());
$tweetBo = TweetBo::newInstance(openConnection());
$logActionBo = LogActionBo::newInstance(openConnection());

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
$account = $_REQUEST["account"];

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
$tweet["twe_validation_score"] = $account["sco_validation_score"];
$tweet["twe_validation_duration"] = $_REQUEST["validationDuration"];
$tweet["twe_cron_datetime"] = $_REQUEST["cronDate"];
$tweet["twe_creation_datetime"] = date("Y-m-d H:i:s");
$tweet["twe_content"] = $_REQUEST["tweet"];

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
		$validation["tva_score"] = $validatorGroup["vgr_score"];
		$validation["tva_ip"] = $remoteIp;
		$validation["tva_referer"] = $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : '';
		$validation["tva_datetime"] = $tweet["twe_creation_date"];

		$tweetBo->addValidation($validation);
	}

	$validators = $accountBo->getAccountValidators($accountId);

//	print_r($validators);

	foreach($validators as $validator) {
		if ($validator["use_id"] != $userId) {
//			$data["validators"][] = $validator["use_login"];

			$hash = TweetBo::hash($tweet, $validator["use_id"]);

			$validationLink = $config["base_url"] . "dvt.php?";
			$validationLink .= "u=" . $validator["use_id"];
			$validationLink .= "&h=$hash";
			$validationLink .= "&t=" . $tweet["twe_id"];

			if ($validator["use_notification"] == "mail") {
				$mail = getMailInstance();

				$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
				$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
				$mail->addAddress($validator["use_mail"]);

				$mailMessage = lang("add_tweet_mail_content", false, $validator["use_language"]);
				$mailMessage = str_replace("{validationLink}", $validationLink, $mailMessage);
				$mailMessage = str_replace("{login}", $validator["use_login"], $mailMessage);
				$mailMessage = str_replace("{tweet}", $tweet["twe_content"], $mailMessage);
				$mailMessage = str_replace("{account}", $account["sna_name"], $mailMessage);
				$mailSubject = lang("add_tweet_mail_subject", false, $validator["use_language"]);

				$mail->Subject = mb_encode_mimeheader(utf8_decode($mailSubject), "ISO-8859-1");
				$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
				$mail->AltBody = utf8_decode($mailMessage);

				$mail->send();
			}
			else if ($validator["use_notification"] == "simpledm") {
				$noticeTweet = array();
				$noticeTweet["twe_destination"] = $account["sna_name"];
				$noticeTweet["twe_destination_id"] = $account["sna_id"];

				$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " un message en attente de validation vous attend sur " . $config["base_url"];
				$tweetBo->sendTweet($noticeTweet);
			}
			else if ($validator["use_notification"] == "dm") {
				$noticeTweet = array();
				$noticeTweet["twe_destination"] = $account["sna_name"];
				$noticeTweet["twe_destination_id"] = $account["sna_id"];

				$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " un message en attente de validation vous attend : ";
				$tweetBo->sendTweet($noticeTweet);
				time_nanosleep(0, 200000000);

				$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " " . $tweet["twe_content"];
				$tweetBo->sendTweet($noticeTweet);
				time_nanosleep(0, 200000000);

				$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " Pour valider : " . $validationLink;
				$tweetBo->sendTweet($noticeTweet);
			}
		}
	}
}
else {
	$data["ko"] = "ko";
}

echo json_encode($data);
?>