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
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/TweetBo.php");

$accountBo = AccountBo::newInstance(openConnection());
$tweetBo = TweetBo::newInstance(openConnection());

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

$administratedAccounts = $accountBo->getAdministratedAccounts($userId);

$isAdmin = false;
foreach($administratedAccounts as $administratedAccount) {
	if ($administratedAccount["sna_id"] == $tweet["twe_destination_id"]) {
		$isAdmin = true;
		break;
	}
}

// Only the author can delete a tweet or an admin
if ($tweet["twe_author_id"] != $userId && !$isAdmin) {
	echo json_encode(array("ko" => "ko", "message" => "not_allowed"));
	exit();
}

// Only "in validation" tweet can be deleted
if ($tweet["twe_status"] != "inValidation") {
	echo json_encode(array("ko" => "ko", "message" => "not_in_validation"));
	exit();
}

$data = array();

if ($tweetBo->updateStatus($tweet, "deleted")) {
	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
}

echo json_encode($data);
?>