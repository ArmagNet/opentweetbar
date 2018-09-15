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
require_once("engine/bo/MediaBo.php");
require_once("engine/bo/TweetBo.php");
require_once("engine/bo/UserBo.php");
include_once("language/language.php");

$connection = openConnection();

$userId = SessionUtils::getUserId($_SESSION);

$accountBo = AccountBo::newInstance($connection);
$accounts = $accountBo->getAccessibleAccounts($userId);

foreach($accounts as $index => $accountArray) {
	$account = $accountBo->getAccount($accountArray["sna_id"]);

	//$tweetId = intval($_REQUEST["tweetId"]);
	$tweetId = $_REQUEST["tweetId"];
	//echo TweetBo::getOembed($account, $tweetId)
	$tweet = TweetBo::getTweetFromTwitter($account, $tweetId);

	if (!isset($tweet->errors)) {
		echo json_encode(array("ok" => "ok", "index" => $index, "maxIndex" => count($accounts), "accountId" => -1, "tweetId" => $tweetId, "tweet" => $tweet));
		break;
	}
}
?>