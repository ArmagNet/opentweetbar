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
include_once("config/database.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/MediaBo.php");
require_once("engine/bo/TweetBo.php");
require_once("engine/bo/UserBo.php");
include_once("language/language.php");

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$tweetBo = TweetBo::newInstance($connection);
$userBo = UserBo::newInstance($connection);

$userId = $_REQUEST["userId"];

$user = $userBo->get($userId);

$hash = $_REQUEST["hash"];
$tweetId = $_REQUEST["tweetId"];
$remoteIp = (isset($_SERVER["HTTP_X_REAL_IP"]) && $_SERVER["HTTP_X_REAL_IP"]) ? $_SERVER["HTTP_X_REAL_IP"] : $_SERVER["REMOTE_ADDR"];

if (strpos($remoteIp, "199.59.148.") !== false ||
	strpos($remoteIp, "199.16.156.") !== false
	) {
	// We exit cause it's a twitter validation
	exit();
}

// The tweet id must be a numeric
if (!is_numeric($tweetId)) {
	exit();
}

$tweet = $tweetBo->getTweet($tweetId);

// The tweet can't be null
if (!$tweet) {
	if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], "t.co") !== false) {
		echo lang("do_validation_error", true, $user["use_language"]);
	}
	exit();
}

if (!isset($_SERVER["HTTP_REFERER"])) {
	echo lang("do_validation_error", true, $user["use_language"]);
	exit();
}

$trueHash = TweetBo::hash($tweet, $userId);

// The hash is verified (forged form)
if ($trueHash != $hash) {
	if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], "t.co") !== false) {
		echo lang("do_validation_error", true, $user["use_language"]);
	}
	exit();
}

// We can only validate once
foreach($tweet["validatorIds"] as $validator) {
	if ($validator == $userId) {
		if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], "t.co") !== false) {
			echo lang("do_validation_error", true, $user["use_language"]);
		}
		else {
			echo json_encode(array("ko" => "ko", "message" => "has_already_validated"));
		}
		exit();
	}
}

$validatorGroup = $accountBo->getValidator($tweet["twe_destination"], $userId);

// We must be in a validator group
if (!$validatorGroup) {
	if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], "t.co") !== false) {
		echo lang("do_validation_error", true, $user["use_language"]);
	}
	else {
		echo json_encode(array("ko" => "ko", "message" => "not_allowed"));
	}
	exit();
}

$validation = array();
$validation["tva_validator"] = $userId;
$validation["tva_tweet_id"] = $tweetId;
$validation["tva_score"] = $validatorGroup["vgr_score"];
if (isset($_REQUEST["rejection"])) {
	$validation["tva_status"] = "rejection";
	$validation["tva_motivation"] = $_REQUEST["motivation"]; // TODO
}
else {
	$validation["tva_status"] = "validation";
	$validation["tva_motivation"] = "";
}
$validation["tva_ip"] = $remoteIp;
$validation["tva_referer"] = $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : '';
$validation["tva_datetime"] = date("Y-m-d H:i:s");

$data = array();

if ($tweetBo->addValidation($validation)) {
	$data["ok"] = "ok";

	$tweet = $tweetBo->getTweet($validation["tva_tweet_id"]);
	$currentScore = $tweet["validation"][0] + $tweet["validation"][1] + $tweet["validation"][2];

	if ($validation["tva_status"] == "validation") {

	//	error_log(print_r($tweet, true));

		$data["validated"] = false;
		if ($currentScore >= $tweet["twe_validation_score"]) {
			$data["validated"] = true;

	// 		error_log("Will do something");
	// 		error_log("tweet[twe_cron_datetime] " . $tweet["twe_cron_datetime"]);

			if ($tweet["twe_status"] == "validated" || $tweet["twe_status"] == "croned") {
	//			error_log("in fact no");
			}
			else if (isset($tweet["twe_cron_datetime"]) && $tweet["twe_cron_datetime"] && $tweet["twe_cron_datetime"] != "0000-00-00 00:00:00") {
	//			error_log("Will do cron");
				$tweetBo->updateStatus($tweet, "croned");
			}
			else {
	//			error_log("Will do sending");
				$tweetBo->sendTweet($tweet);
				$tweetBo->updateStatus($tweet, "validated");
			}
		}
	}

	$data["validatorGroup"] = $validatorGroup["vgr_name"];
	$data["score"] = ($validation["tva_status"] == "validation" ? 100 : - 100) * $validation["tva_score"] / $tweet["twe_validation_score"];
	$data["total_score"] = $currentScore;
	$data["validation_score"] = $tweet["twe_validation_score"];
}
else {
	$data["ko"] = "ko";
}

if (isset($_SERVER["HTTP_REFERER"]) && strpos($_SERVER["HTTP_REFERER"], "t.co") === false) {
	echo json_encode($data);
}
else {
	if (isset($data["ok"])) {

		$validationLink = $config["base_url"] . "seeTweetValidation.php?";
		$validationLink .= "userId=" . $userId;
		$validationLink .= "&hash=$hash";
		$validationLink .= "&tweetId=" . $tweetId;

		header('Location: ' . $validationLink);
		exit();
	}
	else {
		echo lang("do_validation_error", true, $user["use_language"]);
	}
}
?>