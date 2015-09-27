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

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$tweetBo = TweetBo::newInstance($connection);
$userId = SessionUtils::getUserId($_SESSION);

$data = array();
if ($userId) {
	$accounts = $accountBo->getAccessibleAccounts($userId);
	$tweets = array();

	if (count($accounts)) {
		$tweets = $tweetBo->getTweets($accounts);
		$tweets = TweetBo::indexValidations($tweets, $userId);
	}

	$data["numberOfValidations"] = count($tweets);
	$data["ok"] = "ok";
}
else {
	$data["numberOfValidations"] = 0;
	$data["ko"] = "not_connected";
}

echo json_encode($data);
?>